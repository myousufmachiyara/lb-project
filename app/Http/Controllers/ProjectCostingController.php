<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectCosting;
use App\Models\ProjectCostingDetail;
use App\Models\Project;
use App\Models\Service;
use App\Models\PurchaseVoucherDetail;

use Illuminate\Support\Facades\DB;

class ProjectCostingController extends Controller
{
    public function create($projectId)
    {
        $project = Project::findOrFail($projectId);

        if ($project->is_billed) {
            return redirect()->route('projects.index')->with('error', 'Costing already exists.');
        }

        // Load details with service relationship
        $purchaseDetails = PurchaseVoucherDetail::with('service')
            ->where('project_id', $projectId)
            ->get();

        return view('projects.costing.create', compact('project', 'purchaseDetails'));
    }

    public function store(Request $request, $projectId)
    {
        DB::beginTransaction();

        try {
            $project = Project::findOrFail($projectId);

            // Create Project Costing
            $costing = ProjectCosting::create([
                'project_id' => $projectId,
                'created_by' => auth()->id(),
                'date' => $request->input('date'),
            ]);

            foreach ($request->input('details', []) as $item) {
                ProjectCostingDetail::create([
                    'project_costing_id' => $costing->id,
                    'service_id' => $item['service_id'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'service_percent' => $item['service_percent'],
                ]);
            }

            // Mark project as billed
            $project->update(['is_billed' => true]);

            DB::commit();

            return redirect()->route('projects.index')->with('success', 'Project costing saved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
