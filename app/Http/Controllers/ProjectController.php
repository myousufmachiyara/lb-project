<?php

namespace App\Http\Controllers;

use App\Models\{Project, ChartOfAccounts, ProjectStatus, ProjectAttachment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Storage};
use Exception;
use App\Traits\SaveImage;

class ProjectController extends Controller
{
    use SaveImage;

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
            // $accounts = ChartOfAccounts::all();
            $projectStatuses = ProjectStatus::all();

            return view('projects.create', [
                // 'accounts' => $accounts,
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
                // 'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer|min:1',
                'status_id' => 'required|exists:project_status,id',
                'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:20480',
            ]);
    
            // Create the project
            $project = Project::create($validated);
    
            // Handle file uploads (attachments)
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                foreach ($files as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $att_path = $this->projectDoc($file, $extension);
    
                    ProjectAttachment::create([
                        'proj_id' => $project->id,
                        'att_path' => $att_path,
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
            // $accounts = ChartOfAccounts::all();
            $statuses = ProjectStatus::all();
            return view('projects.edit', compact('project', 'statuses'));
            // return view('projects.edit', compact('project', 'accounts', 'statuses'));
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
                // 'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer|min:1',
                'status_id' => 'required|exists:project_status,id',
            ]);

            $project = Project::findOrFail($id);
            $project->update($validated);

            // Handle file uploads (attachments) for the update
            // Check if new attachments are being uploaded
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    // Resize & compress the image
                    $image = Image::make($file)
                        ->resize(1200, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode($file->getClientOriginalExtension(), 75); // 75% quality

                    // Save optimized image to 'public/attachments'
                    Storage::disk('public')->put("attachments/{$filename}", (string) $image);

                    // Save in DB
                    ProjectAttachment::create([
                        'proj_id' => $project->id,
                        'att_path' => "attachments/{$filename}",
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
