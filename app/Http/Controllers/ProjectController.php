<?php

namespace App\Http\Controllers;

use App\Models\{Project, ChartOfAccounts, ProjectStatus, ProjectAttachment, ProjectPcsInOut};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Storage};
use Exception;
use App\Traits\SaveImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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

        // Validate the incoming data first
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_pcs' => 'required|integer|min:1',
            'status_id' => 'required|exists:project_status,id',
            'attachments.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
        ]);
        Log::info('Validation passed', $validated);

        DB::beginTransaction();
    
        try {
            // Create the project
            $project = Project::create($validated);
    
            // Handle file uploads (attachments)
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $att_path = $this->projectDoc($file, $extension);
    
                    ProjectAttachment::create([
                        'proj_id' => $project->id,
                        'att_path' => $att_path,
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            dd($e); // or dd($e->getMessage(), $e->getTraceAsString());

            // Optionally delete any files already stored if needed here
    
            Log::error('Failed to create project: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to create project.');
        }
    }
    
    public function edit($id)
    {
        try {
            $project = Project::with(['attachments', 'status'])->findOrFail($id);

            // Retrieve all statuses for the status dropdown
            $statuses = ProjectStatus::all();

            // Fetch existing attachment IDs (these will be kept by the user)
            $keptAttachmentIds = $project->attachments->pluck('id')->toArray();

            // Return the edit view with necessary data
            return view('projects.edit', compact('project', 'statuses', 'keptAttachmentIds'));

        } catch (Exception $e) {
            Log::error('Failed to load edit form: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Unable to load project.');
        }
    }

    public function pcsUpdate(Request $request, $id){

        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'date' => 'required|date', 
                'type' => 'required|string',
                'pcs' => 'required|integer|min:1',
                'remarks' => 'nullable|string',
            ]);

            $project_pcs_in_out = ProjectPcsInOut::create($validated);

            DB::commit();

            return redirect()->route('projects.index')->with('success', 'Project Pieces updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update Project Pcs Error: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to update project pcs.');
        }
    }

    public function getPcs($id)
    {
        $pcsRecords = ProjectPcsInOut::where('project_id', $id)->orderBy('date', 'desc')->get();
        return response()->json($pcsRecords);
    }

    public function deletePcs($id)
    {
        $pcs = ProjectPcsInOut::findOrFail($id);
        $pcs->delete();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'total_pcs' => 'required|integer|min:1',
                'status_id' => 'required|exists:project_status,id',
            ]);

            // Get the project
            $project = Project::findOrFail($id);
            $project->update($validated);

            // Get list of kept attachments from the request (comma-separated string)
            $keptAttachments = $request->input('kept_attachments', '');  // Get the kept attachments as a comma-separated string
            $keptIds = explode(',', $keptAttachments);  // Convert to an array of IDs

            // Loop through all attachments and delete those not marked as "kept"
            foreach ($project->attachments as $attachment) {
                // Check if the attachment ID is in the list of kept IDs
                if (!in_array($attachment->id, $keptIds)) {
                    // Delete file from storage
                    $fullPath = public_path($attachment->att_path);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }

                    // Delete DB record
                    $attachment->delete();
                }
            }

            // Upload new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $att_path = $this->projectDoc($file, $extension);

                    ProjectAttachment::create([
                        'proj_id' => $project->id,
                        'att_path' => $att_path,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update Project Error: ' . $e->getMessage());
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
