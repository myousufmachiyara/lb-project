<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProjectController extends Controller
{
    // Project Status CRUD
    public function getAllProjectStatuses()
    {
        try {
            return response()->json(ProjectStatus::all(), 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to fetch project statuses'], 500);
        }
    }

    public function createProjectStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'color' => 'required|string',
            ]);

            $status = ProjectStatus::create($validated);
            return response()->json($status, 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to create project status'], 500);
        }
    }

    public function updateProjectStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'color' => 'required|string',
            ]);

            $status = ProjectStatus::findOrFail($id);
            $status->update($validated);
            return response()->json($status, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to update project status'], 500);
        }
    }

    public function deleteProjectStatus($id)
    {
        try {
            ProjectStatus::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to delete project status'], 500);
        }
    }

    // Project CRUD
    public function getAllProjects()
    {
        try {
            return response()->json(Project::with('attachments')->get(), 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to fetch projects'], 500);
        }
    }

    public function createProject(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer',
                'attachments' => 'array',
                'attachments.*' => 'file',
            ]);

            $project = Project::create($validated);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments');
                    ProjectAttachment::create(['proj_id' => $project->id, 'att_path' => $path]);
                }
            }

            return response()->json($project, 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to create project'], 500);
        }
    }

    public function updateProject(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'acc_id' => 'required|exists:chart_of_accounts,id',
                'total_pcs' => 'required|integer',
            ]);

            $project = Project::findOrFail($id);
            $project->update($validated);

            return response()->json($project, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to update project'], 500);
        }
    }

    public function deleteProject($id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->attachments()->each(function ($attachment) {
                Storage::delete($attachment->att_path);
                $attachment->delete();
            });
            $project->delete();

            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to delete project'], 500);
        }
    }
}
