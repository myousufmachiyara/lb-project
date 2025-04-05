<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ProjectStatusController extends Controller
{
    /**
     * Display a listing of project statuses.
     */
    public function index()
    {
        try {
            Log::info('Entering ProjectStatusController@index method');
            $statuses = ProjectStatus::all();
            Log::info('Successfully fetched project statuses');
            return view('projects.status', compact('statuses'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch project statuses: ' . $e->getMessage());
            return redirect()->route('project-status.index')
                             ->with('error', 'Failed to fetch project statuses')
                             ->withInput();  // Optionally return the previous input in case of error
        }
    }

    /**
     * Store a newly created project status in storage.
    */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'color' => 'required|string|max:7',
            ]);

            $status = ProjectStatus::create($validated);
            return redirect()->route('project-status.index')->with('success', 'Project Status created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create project status: ' . $e->getMessage());
            return redirect()->route('project-status.index')->with('error', 'Failed to create project status');
        }
    }

    /**
     * Display the specified project status.
     */
    public function show($id)
    {
        try {
            $status = ProjectStatus::findOrFail($id);
            return view('project-status.show', compact('status'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch project status: ' . $e->getMessage());
            return redirect()->route('project-status.index')->with('error', 'Project status not found');
        }
    }

    /**
     * Show the form for editing the specified project status.
     */
    public function edit($id)
    {
        try {
            $status = ProjectStatus::findOrFail($id);
            return view('project-status.edit', compact('status'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch project status for editing: ' . $e->getMessage());
            return redirect()->route('project-status.index')->with('error', 'Failed to fetch project status');
        }
    }

    /**
     * Update the specified project status in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);
    
        $status = ProjectStatus::findOrFail($id);
        $status->update($request->only(['name', 'color']));
    
        return redirect()->route('project-status.index')->with('success', 'Project Status updated successfully');
    }

    /**
     * Remove the specified project status from storage.
     */
    public function destroy($id)
    {
        try {
            $status = ProjectStatus::findOrFail($id);
            $status->delete();
            return redirect()->route('project-status.index')->with('success', 'Project Status deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete project status: ' . $e->getMessage());
            return redirect()->route('project-status.index')->with('error', 'Failed to delete project status');
        }
    }

    public function showJson($id)
    {
        $status = ProjectStatus::findOrFail($id);
        return response()->json($status);
    }
}
