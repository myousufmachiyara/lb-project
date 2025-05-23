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

            $tasks = Task::with(['project', 'project.attachments'])
                ->select('*', DB::raw("
                    CASE
                        WHEN is_recurring = 1 AND last_completed_at IS NOT NULL THEN DATE_ADD(last_completed_at, INTERVAL recurring_frequency DAY)
                        WHEN is_recurring = 1 AND last_completed_at IS NULL THEN due_date
                        ELSE due_date
                    END as next_due_date,
                    CASE
                        WHEN (
                            (is_recurring = 1 AND last_completed_at IS NOT NULL AND DATE_ADD(last_completed_at, INTERVAL recurring_frequency DAY) <= '$today')
                            OR (is_recurring = 1 AND last_completed_at IS NULL AND due_date <= '$today')
                            OR (is_recurring = 0 AND last_completed_at IS NULL AND due_date <= '$today')
                        ) THEN 1
                        ELSE 0
                    END as is_due
                "))
                ->orderByDesc('is_due') // Show due tasks first
                ->orderBy('next_due_date', 'asc') // Then by due date
                ->get();

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
                'status_id'           => 'nullable|integer|exists:project_statuses,id',
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
        $task->last_completed_at = now();
        $task->save();

        return redirect()->back()->with('success', 'Task marked as complete.');
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
                'status_id'           => 'nullable|integer|exists:project_statuses,id',
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
