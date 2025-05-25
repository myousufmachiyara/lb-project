<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\ProjectStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    
    public function index()
    {
        try {
            $today = now()->startOfDay();
            $tomorrow = $today->copy()->addDay();
            $tasks = Task::with(['project.attachments', 'category'])
            ->get()
            ->map(function ($task) use ($today, $tomorrow) {
                // Compute next due date
                if ($task->due_date === null) {
                    $task->next_due_date = null;
                } elseif ($task->is_recurring && $task->last_completed_at) {
                    $task->next_due_date = \Carbon\Carbon::parse($task->last_completed_at)->addDays((int) $task->recurring_frequency);
                } else {
                    $task->next_due_date = \Carbon\Carbon::parse($task->due_date);
                }

                // Determine custom status
                if ($task->last_completed_at && !$task->is_recurring) {
                    $task->custom_status = 'Completed';
                } elseif ($task->is_recurring && $task->last_completed_at &&
                    now()->diffInDays($task->last_completed_at) < (int) $task->recurring_frequency) {
                    $task->custom_status = 'Completed';
                } elseif ($task->next_due_date === null) {
                    $task->custom_status = 'Unscheduled';
                } elseif ($task->next_due_date->lt($today)) {
                    $task->custom_status = 'Due';
                } elseif ($task->next_due_date->eq($today)) {
                    $task->custom_status = 'In Progress';
                } elseif ($task->next_due_date->gte($tomorrow)) {
                    $task->custom_status = 'Scheduled';
                }

                return $task;
            })
            ->sortBy(function ($task) {
                // Assign a sort weight to each status
                return match ($task->custom_status) {
                    'Due' => 0,
                    'In Progress' => 1,
                    'Assigned' => 2,
                    'Unassigned' => 3,
                    'Completed' => 4,
                    default => 5,
                };
            })
            ->sortBy(function ($task) {
                // Sort by date within the same status group (except completed)
                if ($task->custom_status === 'Completed') return PHP_INT_MAX;
                return $task->next_due_date?->timestamp ?? PHP_INT_MAX - 1;
            })
            ->values();

            $category = TaskCategory::all();
            $status = ProjectStatus::all();
            $projects = Project::all();

            return view('tasks.index', compact('tasks', 'category', 'status', 'projects'));

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
                'is_recurring'        => 'nullable|boolean',
                'recurring_frequency' => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30',
            ]);

            // Checkbox handling: Laravel sends "0"/"1" as strings, so cast it to bool.
            $validated['is_recurring'] = (bool) $request->input('is_recurring', false);

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
        $task = Task::findOrFail($id);

        DB::beginTransaction();

        try {
            // Mark the current task as complete
            $task->last_completed_at = now();
            $task->status_id = 3; // Completed
            $task->save();

            // If this task belongs to a project
            if ($task->project_id) {
                // Find the next task in the project based on sort_order
                $nextTask = Task::where('project_id', $task->project_id)
                    ->where('sort_order', '>', $task->sort_order)
                    ->orderBy('sort_order', 'asc')
                    ->first();

                if ($nextTask) {
                    // Set due_date only if not already set
                    if ($nextTask->due_date === null) {
                        $nextTask->due_date = now()->toDateString();
                    }

                    $nextTask->status_id = 1; // Assigned/In Progress
                    $nextTask->save();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Task marked as complete.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error completing task: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark task as complete.');
        }
    }

    public function edit(Task $task)
    {
        return response()->json($task);
    }   

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);

            $validated = $request->validate([
                'task_name'           => 'required|string|max:255',
                'category_id'         => 'nullable|integer|exists:task_categories,id',
                'status_id'           => 'nullable|integer|exists:project_status,id',
                'project_id'          => 'nullable|integer|exists:projects,id',
                'description'         => 'nullable|string',
                'due_date'            => 'nullable|date',
                'is_recurring'        => 'nullable|boolean',
                'recurring_frequency' => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30',
            ]);

            $validated['is_recurring'] = (bool) $request->input('is_recurring', false);

            $task->update($validated);

            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating task: ' . $e->getMessage());

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
}
