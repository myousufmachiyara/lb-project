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
            $projects = Project::with('attachments')->get();
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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer|min:1',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
            ]);

            $project = Project::create($validated);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments');
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
            $project = Project::with('attachments')->findOrFail($id);
            $accounts = ChartOfAccounts::all();
            return view('projects.edit', compact('project', 'accounts'));
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
            ]);

            $project = Project::findOrFail($id);
            $project->update($validated);

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

            foreach ($project->attachments as $attachment) {
                Storage::delete($attachment->att_path);
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