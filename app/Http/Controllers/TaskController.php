<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\ProjectStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 

class TaskController extends Controller
{
    
    public function index()
    {    
        try {
            $today = now()->startOfDay();
            $tomorrow = $today->copy()->addDay();

            $tasks = Task::with(['project.attachments', 'category'])->get();

            // Compute next_due_date and custom_status
            $tasks = $tasks->map(function ($task) use ($today, $tomorrow) {
                if ($task->is_recurring) {
                    $task->next_due_date = $task->last_completed_at
                        ? \Carbon\Carbon::parse($task->due_date ?? $task->last_completed_at)->copy()->addDays((int) $task->recurring_frequency)
                        : ($task->due_date ? \Carbon\Carbon::parse($task->due_date) : null);
                } elseif ($task->due_date) {
                    $task->next_due_date = \Carbon\Carbon::parse($task->due_date);
                } else {
                    $task->next_due_date = null;
                }

                if (!$task->is_recurring && $task->last_completed_at) {
                    $task->custom_status = 'Completed';
                } elseif ($task->is_recurring && $task->next_due_date && $task->next_due_date->eq($today)) {
                    $task->custom_status = 'In Progress';
                } elseif ($task->is_recurring && $task->last_completed_at && now()->diffInDays($task->last_completed_at) < (int) $task->recurring_frequency) {
                    $task->custom_status = 'Completed';
                } elseif ($task->next_due_date === null) {
                    $task->custom_status = 'Unscheduled';
                } elseif ($task->next_due_date->lt($today)) {
                    $task->custom_status = 'Due';
                } elseif ($task->next_due_date->eq($today)) {
                    $task->custom_status = 'In Progress';
                } elseif ($task->next_due_date->eq($tomorrow)) {
                    $task->custom_status = 'Scheduled';
                } else {
                    $task->custom_status = 'Scheduled';
                }

                return $task;
            });

            // Group by next_due_date (raw format)
            $groupedByDate = $tasks->filter(function ($task) {
                return $task->next_due_date !== null;
            })->groupBy(function ($task) {
                return $task->next_due_date->toDateString(); // 'Y-m-d'
            })->sortKeys();

            // Now format keys for display
            $groupedTasks = [];

            foreach ($groupedByDate as $dateKey => $list) {
                $carbonDate = \Carbon\Carbon::parse($dateKey);

                if ($carbonDate->isToday()) {
                    $heading = 'Today';
                } elseif ($carbonDate->isTomorrow()) {
                    $heading = 'Tomorrow';
                } elseif ($carbonDate->lt($today)) {
                    $heading = 'Due';
                } else {
                    $heading = $carbonDate->format('l, jS F Y');
                }

                if (!isset($groupedTasks[$heading])) {
                    $groupedTasks[$heading] = collect();
                }

                $groupedTasks[$heading] = $groupedTasks[$heading]->merge($list->sortBy('due_time')->values());
            }

            $category = TaskCategory::all();
            $status = ProjectStatus::all();
            $projects = Project::all();

            return view('tasks.index', compact('groupedTasks', 'category', 'status', 'projects', 'tomorrow'));
        } catch (\Exception $e) {
            \Log::error('Error fetching tasks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to retrieve tasks.');
        }
    }

    public function filter(Request $request)
    {
        $filterDate = $request->input('date') ?? now()->toDateString();

        $tasks = Task::query()
            ->with(['project', 'project.attachments'])
            ->select('*')
            ->selectRaw("
                CASE
                    WHEN is_recurring = 1 AND last_completed_at IS NOT NULL
                        THEN DATE_ADD(last_completed_at, INTERVAL recurring_frequency DAY)
                    WHEN is_recurring = 1 AND last_completed_at IS NULL
                        THEN due_date
                    ELSE due_date
                END as next_due_date,
                CASE
                    WHEN (
                        (is_recurring = 1 AND last_completed_at IS NOT NULL AND DATE_ADD(last_completed_at, INTERVAL recurring_frequency DAY) <= ?)
                        OR (is_recurring = 1 AND last_completed_at IS NULL AND due_date <= ?)
                        OR (is_recurring = 0 AND last_completed_at IS NULL AND due_date <= ?)
                    ) THEN 1
                    ELSE 0
                END as is_due
            ", [$filterDate, $filterDate, $filterDate])
            ->havingRaw('DATE(next_due_date) = ?', [$filterDate]) // Only tasks due on this exact date
            ->orderBy('next_due_date', 'asc')
            ->get();

        $category = TaskCategory::all();
        $status = ProjectStatus::all();
        $projects = Project::all();

        return view('tasks.index', compact('tasks', 'category', 'status', 'projects'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'task_name'           => 'required|string|max:255',
                'category_id'         => 'nullable|integer|exists:task_categories,id',
                'status_id'           => 'nullable|integer|exists:project_status,id',
                'project_id'          => 'nullable|integer|exists:projects,id',
                'description'         => 'nullable|string',
                'due_date'            => 'nullable|date',
                'due_time'            => 'nullable|date_format:H:i',
                'recurring_frequency' => 'nullable|integer|between:1,30',
            ]);

            // Automatically determine if it's recurring
            $validated['is_recurring'] = !empty($validated['recurring_frequency']);

            Task::create($validated);

            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating task', [
                'message' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Failed to create task.');
        }
    }

public function markComplete($id)
{
    Log::info("Starting markComplete for Task ID: {$id}");

    $task = Task::findOrFail($id);

    DB::beginTransaction();

    try {
        if ($task->is_recurring) {
            Log::info("Task ID {$id} is recurring. Last completed at before update: {$task->last_completed_at}, Due date: {$task->due_date}");

            $frequency = (int) ($task->recurring_frequency ?? 1);
            $currentDueDate = $task->due_date ? Carbon::parse($task->due_date) : Carbon::now();

            // Keep adding frequency until due_date is today or in future
            $today = Carbon::today();
            while ($currentDueDate->lt($today)) {
                $currentDueDate->addDays($frequency);
            }

            $task->last_completed_at = now();
            $task->due_date = $currentDueDate->toDateString();
            $task->save();

            Log::info("Task ID {$id} marked completed. Next due date set to: {$task->due_date}");
        } else {
            Log::info("Task ID {$id} is non-recurring. Marking complete and updating status.");

            $task->last_completed_at = now();
            $task->status_id = 3; // Completed
            $task->save();

            Log::info("Task ID {$id} marked completed at {$task->last_completed_at} with status 3.");

            if ($task->project_id) {
                Log::info("Looking for next task in project ID {$task->project_id} with sort_order > {$task->sort_order}");

                $nextTask = Task::where('project_id', $task->project_id)
                    ->where('sort_order', '>', $task->sort_order)
                    ->orderBy('sort_order', 'asc')
                    ->first();

                if ($nextTask) {
                    Log::info("Next task found: ID {$nextTask->id}, due_date: {$nextTask->due_date}");

                    if ($nextTask->due_date === null) {
                        $nextTask->due_date = now()->toDateString();
                        Log::info("Next task ID {$nextTask->id} due_date was null, set to today: {$nextTask->due_date}");
                    }

                    $nextTask->status_id = 1; // Assigned/In Progress
                    $nextTask->save();

                    Log::info("Next task ID {$nextTask->id} status updated to 1.");
                } else {
                    Log::info("No next task found for project ID {$task->project_id}.");
                }
            }
        }

        DB::commit();
        Log::info("markComplete completed successfully for Task ID: {$id}");

        return redirect()->back()->with('success', 'Task processed successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error completing task ID {$id}: " . $e->getMessage());
        Log::error($e->getTraceAsString());
        return redirect()->back()->with('error', 'Failed to process task.');
    }
}



    public function edit(Task $task)
    {
        return response()->json($task);
    }   

    public function update(Request $request, $id)
    {
        Log::info("Starting update for Task ID: {$id}");

        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);

            Log::info("Task found for update", ['task_id' => $task->id]);

            $validated = $request->validate([
                'task_name'           => 'required|string|max:255',
                'category_id'         => 'nullable|integer|exists:task_categories,id',
                'status_id'           => 'nullable|integer|exists:project_status,id',
                'project_id'          => 'nullable|integer|exists:projects,id',
                'description'         => 'nullable|string',
                'due_date'            => 'nullable|date',
                'due_time'            => 'nullable|date_format:H:i',
                'recurring_frequency' => 'nullable|integer|between:1,30',
            ]);

            Log::info("Validation passed", ['validated_data' => $validated]);

            // Auto-set recurring flag
            $validated['is_recurring'] = !empty($validated['recurring_frequency']);

            $task->update($validated);

            Log::info("Task updated successfully", ['task_id' => $task->id]);

            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            Log::warning("Validation failed for Task ID: {$id}", [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error updating task ID {$id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()->withInput()->with('error', 'Failed to update task.');
        }
    }

    /**
     * Delete a task.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);
            $task->delete();

            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete task.');
        }
    }

    public function bulkComplete(Request $request)
    {
        try {
            $taskIds = $request->input('task_ids', []);

            if (empty($taskIds)) {
                return response()->json(['message' => 'No tasks selected.'], 400);
            }

            // Get all selected tasks
            $tasks = Task::whereIn('id', $taskIds)->get();

            foreach ($tasks as $task) {
                $task->status_id = 3;
                $task->last_completed_at = now()->toDateString();

                if ($task->is_recurring && (int)$task->recurring_frequency > 0) {
                    $task->due_date = now()->addDays((int)$task->recurring_frequency)->toDateString();
                }

                $task->save();
            }

            return response()->json(['message' => 'Tasks marked as complete.']);
        } catch (\Exception $e) {
            \Log::error('Bulk complete error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error completing tasks.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        try {
            Task::whereIn('id', $request->task_ids)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error("Bulk delete failed: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete tasks.'], 500);
        }
    }

}
