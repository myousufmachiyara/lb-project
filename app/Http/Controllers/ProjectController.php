<?php

namespace App\Http\Controllers;

use App\Models\{Project, PurchaseVoucherDetail ,ChartOfAccounts, Status, ProjectAttachment, Service, ProjectPcsInOut, TaskCategory, Task};
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
            $projects = Project::with(['attachments', 'status', 'pcsInOut'])->get();

            // Sort projects by custom status order: Assigned → In Progress → Completed
            $projects = $projects->sortBy(function ($project) {
                return match ($project->status_id) {
                    1 => 0, // Assigned
                    2 => 1, // In Progress
                    3 => 2, // Completed
                    default => 3, // Unknown/other statuses last
                };
            })->values(); // Reindex

            $statuses = Status::all();

            return view('projects.index', compact('projects', 'statuses'));
        } catch (\Exception $e) {
            Log::error('Failed to load projects: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load projects.');
        }
    }

    public function create()
    {
        try {
            // $accounts = ChartOfAccounts::all();
            $projectStatuses = Status::all();
            $taskCat = TaskCategory::all();

            return view('projects.create', [
                // 'accounts' => $accounts,
                'statuses' => $projectStatuses,
                'taskCat' => $taskCat
            ]);
        } catch (Exception $e) {
            Log::error('Failed to load project creation form: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Unable to load project form.');
        }
    }

    public function costingForm($id)
    {
        $project = Project::findOrFail($id);
        $services = Service::all();

        // Fetch all purchase voucher details for this project
        $purchaseDetails = PurchaseVoucherDetail::where('project_id', $id)->get();

        return view('projects.costing', compact('project', 'purchaseDetails', 'services'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_pcs' => 'required|integer|min:1',
            'status_id' => 'required|exists:status,id',
            'attachments.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
            'tasks' => 'nullable|array|min:1',
            'tasks.*.task_name' => 'nullable|string|max:255',
            'tasks.*.description' => 'nullable|string|max:1000',
            'tasks.*.due_date' => 'nullable|date',
            'tasks.*.due_time' => 'nullable|date_format:H:i',
            'tasks.*.category_id' => 'nullable|exists:task_categories,id',
            'tasks.*.status_id' => 'nullable|exists:status,id',
            'tasks.*.sort_order' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $project = Project::create($validated);

            // Handle file uploads
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

            // Save tasks in sorted order
            if (!empty($validated['tasks'])) {
                foreach ($validated['tasks'] as $taskData) {
                    if (empty($taskData['task_name'])) continue;

                    Task::create([
                        'project_id' => $project->id,
                        'task_name' => $taskData['task_name'],
                        'description' => $taskData['description'] ?? null,
                        'due_date' => $taskData['due_date'] ?? null,
                        'due_time' => $taskData['due_time'] ?? null,
                        'category_id' => $taskData['category_id'] ?? 0,
                        'status_id' => $taskData['status_id'] ?? 0,
                        'sort_order' => $taskData['sort_order'] ?? 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project creation failed: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to create project.');
        }
    }

    public function storeCosting(Request $request, $project_id)
    {
        $request->validate([
            'date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $project = Project::findOrFail($project_id);
        $services = Service::all();

        // Get all purchase voucher details for this project
        $purchaseDetails = PurchaseVoucherDetail::where('project_id', $project_id)->get();

        // Create costing record
        $costing = ProjectCosting::create([
            'project_id' => $project->id,
            'date' => $request->date,
            'remarks' => $request->remarks,
        ]);

        foreach ($purchaseDetails as $detail) {
            $service = $services->firstWhere('name', $detail->service);
            if (!$service) continue;

            $percentage = $service->charges_per_pc ?? 0;
            $totalRate = $detail->rate + ($detail->rate * $percentage / 100);
            $totalAmount = $totalRate * $detail->qty;

            ProjectCostingDetail::create([
                'project_costing_id' => $costing->id,
                'purchase_voucher_detail_id' => $detail->id,
                'service' => $detail->service,
                'qty' => $detail->qty,
                'rate' => $detail->rate,
                'service_percentage' => $percentage,
                'total_rate' => $totalRate,
                'total_amount' => $totalAmount,
            ]);
        }

        return redirect()->route('projects.show', $project_id)->with('success', 'Project costing saved successfully.');
    }

    public function edit($id)
    {
        try {
            $project = Project::with([
                'attachments',
                'status',
                'tasks' => function ($query) {
                    $query->orderBy('sort_order', 'asc'); // 🟢 Sort tasks by sort_order
                }
            ])->findOrFail($id);

            $statuses = Status::all();
            $taskCat = TaskCategory::all();
            $keptAttachmentIds = $project->attachments->pluck('id')->toArray();

            return view('projects.edit', compact('project', 'statuses', 'keptAttachmentIds', 'taskCat'));

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
                'status_id' => 'required|exists:status,id',

                'tasks' => 'nullable|array',
                'tasks.*.id' => 'nullable|exists:tasks,id',
                'tasks.*.task_name' => 'nullable|string|max:255',
                'tasks.*.description' => 'nullable|string|max:1000',
                'tasks.*.due_date' => 'nullable|date',
                'tasks.*.due_time' => 'nullable|date_format:H:i',
                'tasks.*.category_id' => 'nullable|exists:task_categories,id',
                'tasks.*.status_id' => 'nullable|exists:status,id',
                'tasks.*.sort_order' => 'nullable|integer',
            ]);

            // Update project
            $project = Project::findOrFail($id);
            $project->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'total_pcs' => $validated['total_pcs'],
                'status_id' => $validated['status_id'],
            ]);

            // Handle attachments
            $keptIds = explode(',', $request->input('kept_attachments', ''));
            foreach ($project->attachments as $attachment) {
                if (!in_array($attachment->id, $keptIds)) {
                    $fullPath = public_path($attachment->att_path);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                    $attachment->delete();
                }
            }

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

            // Handle tasks
            $existingTaskIds = $project->tasks->pluck('id')->toArray();
            $submittedTaskIds = [];

            if (!empty($validated['tasks'])) {
                foreach ($validated['tasks'] as $taskData) {
                    if (empty($taskData['task_name'])) continue;

                    $taskAttributes = [
                        'task_name' => $taskData['task_name'],
                        'description' => $taskData['description'] ?? null,
                        'due_date' => $taskData['due_date'] ?? null,
                        'due_time' => $taskData['due_time'] ?? null,
                        'category_id' => $taskData['category_id'] ?? 0,
                        'status_id' => $taskData['status_id'] ?? 0,
                        'sort_order' => $taskData['sort_order'] ?? 0, // ✅ Use the order directly
                    ];

                    if (!empty($taskData['id'])) {
                        $task = Task::find($taskData['id']);
                        $task->update($taskAttributes);
                        $submittedTaskIds[] = $task->id;
                    } else {
                        $task = Task::create(array_merge($taskAttributes, ['project_id' => $project->id]));
                        $submittedTaskIds[] = $task->id;
                    }
                }
            }

            // Delete removed tasks
            $tasksToDelete = array_diff($existingTaskIds, $submittedTaskIds);
            Task::destroy($tasksToDelete);

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update Project Error: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to update project.');
        }
    }
   
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
            'status_id' => 'required|exists:status,id',
        ]);

        Project::whereIn('id', $request->project_ids)->update(['status_id' => $request->status_id]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        foreach ($request->project_ids as $id) {
            $project = Project::find($id);
            foreach ($project->attachments as $attachment) {
                $fullPath = public_path($attachment->att_path);
                if (File::exists($fullPath)) File::delete($fullPath);
                $attachment->delete();
            }

            $project->tasks()->delete();
            $project->pcsInOut()->delete();
            $project->delete();
        }

        return response()->json(['success' => true]);
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
