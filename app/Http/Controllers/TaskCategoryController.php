<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TaskCategoryController extends Controller
{
    public function index()
    {
        try {
            $taskCat = TaskCategory::all();
            Log::info('Successfully fetched task category');
            return view('tasks.categories', compact('taskCat'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch Task Categories: ' . $e->getMessage());
            return redirect()->route('task-categories.index')
                             ->with('error', 'Failed to fetch project stages')
                             ->withInput();  // Optionally return the previous input in case of error
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
            ]);

            $status = TaskCategory::create($validated);
            return redirect()->route('task-categories.index')->with('success', 'Task Category created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create task category: ' . $e->getMessage());
            return redirect()->route('task-categories.index')->with('error', 'Failed to create task category');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);
    
        $taskCat = TaskCategory::findOrFail($id);
        $taskCat->update($request->only(['name', 'code']));
    
        return redirect()->route('task-categories.index')->with('success', 'Task Category updated successfully');
    }

    /**
     * Remove the specified project status from storage.
     */
    public function destroy($id)
    {
        try {
            $taskCat = TaskCategory::findOrFail($id);
            $taskCat->delete();
            return redirect()->route('task-categories.index')->with('success', 'Task Category deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete Task Category: ' . $e->getMessage());
            return redirect()->route('task-categories.index')->with('error', 'Failed to delete Task Category');
        }
    }

    public function showJson($id)
    {
        $taskCat = TaskCategory::findOrFail($id);
        return response()->json($taskCat);
    }
}
