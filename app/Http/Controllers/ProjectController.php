<?php

namespace App\Http\Controllers;

use App\Models\{Project, ChartOfAccounts, ProjectStatus, ProjectAttachment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Storage};
use Exception;

class ProjectController extends Controller
{
    public function index()
    {
        try {
            $projects = Project::with(['attachments', 'status'])->get();
            return view('projects.index', compact('projects'));
        } catch (Exception $e) {
            Log::error('Failed to load projects: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load projects.');
        }
    }

    public function create()
    {
        try {
            $accounts = ChartOfAccounts::all();
            $projectStatuses = ProjectStatus::all();

            return view('projects.create', [
                'accounts' => $accounts,
                'statuses' => $projectStatuses
            ]);
        } catch (Exception $e) {
            Log::error('Failed to load project creation form: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Unable to load project form.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer|min:1',
                'status_id' => 'required|exists:project_status,id',
                'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
            ]);

            // Create the project
            $project = Project::create($validated);

            // Handle file uploads (attachments)
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    // Store each attachment in the 'public/attachments' directory
                    $path = $file->store('attachments', 'public'); // Use 'public' disk to ensure files are publicly accessible
                    
                    // Save the attachment information in the database
                    ProjectAttachment::create([
                        'proj_id' => $project->id,
                        'att_path' => $path,
                    ]);
                }
            }

            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create project: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to create project.');
        }
    }

    public function edit($id)
    {
        try {
            $project = Project::with(['attachments', 'status'])->findOrFail($id);
            $accounts = ChartOfAccounts::all();
            $statuses = ProjectStatus::all();
            return view('projects.edit', compact('project', 'accounts', 'statuses'));
        } catch (Exception $e) {
            Log::error('Failed to load edit form: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Unable to load project.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer|min:1',
                'status_id' => 'required|exists:project_status,id',
            ]);

            $project = Project::findOrFail($id);
            $project->update($validated);

            // Handle file uploads (attachments) for the update
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    ProjectAttachment::create([
                        'proj_id' => $project->id,
                        'att_path' => $path,
                    ]);
                }
            }

            return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to update project: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to update project.');
        }
    }

    public function destroy($id)
    {
        try {
            $project = Project::with('attachments')->findOrFail($id);

            // Delete all associated attachments
            foreach ($project->attachments as $attachment) {
                Storage::delete('public/' . $attachment->att_path); // Ensure the correct path is used
                $attachment->delete();
            }

            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete project: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to delete project.');
        }
    }
}
