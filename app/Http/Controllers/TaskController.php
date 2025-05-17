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
            $tasks = Task::with(['project', 'project.attachments'])->get();
            $category = TaskCategory::all(); // Fetch all categories
            $status = ProjectStatus::all(); // Fetch all categories
            $projects = Project::all(); // Fetch all categories

            return view('tasks.index', compact('tasks', 'category', 'status' , 'projects'));
        } catch (\Exception $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to retrieve tasks.');
        }
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
