<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::all();
            return view('tasks.index', compact('tasks'));
        } catch (\Exception $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to retrieve tasks.');
        }
    }

    /**
     * Show create task form.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store new task.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'task_name' => 'required|string|max:255',
                'category_id' => 'nullable|integer',
                'status_id' => 'nullable|integer',
                'project_id' => 'nullable|integer',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
            ]);

            Task::create($validated);

            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating task: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create task.');
        }
    }

    /**
     * Show edit task form.
     */
    public function edit($id)
    {
        try {
            $task = Task::findOrFail($id);
            return view('tasks.edit', compact('task'));
        } catch (\Exception $e) {
            Log::error('Error fetching task for edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Task not found.');
        }
    }

    /**
     * Update existing task.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);

            $validated = $request->validate([
                'task_name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'status_id' => 'required|exists:statuses,id',
                'project_id' => 'required|exists:projects,id',
                'description' => 'nullable|string',
                'attachment' => 'nullable|file',
                'date' => 'required|date',
            ]);

            if ($request->hasFile('attachment')) {
                $validated['attachment'] = $request->file('attachment')->store('attachments');
            }

            $task->update($validated);

            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
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
