<?php

namespace App\Http\Controllers;

use App\Models\Gatepass;
use App\Models\GatepassDetail;
use App\Models\ChartOfAccounts;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GatepassController extends Controller
{
    public function index()
    {
        $gatepasses = Gatepass::with('details', 'coa')->latest()->get();
        return view('gatepass.index', compact('gatepasses'));
    }

    public function create()
    {
        $coas = ChartOfAccounts::all();
        $projects = Project::all();
        $services = Service::all();
        return view('gatepass.create', compact('coas', 'projects', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.project_id' => 'required|exists:projects,id',
            'details.*.service_id' => 'required|exists:services,id',
            'details.*.description' => 'nullable|string',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.unit' => 'required|string|max:50',
            'details.*.rate' => 'required|numeric|min:0',
            'details.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $gatepass = Gatepass::create([
                'coa_id' => $request->coa_id,
                'date' => $request->date,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->details as $detail) {
                $imagePath = null;
                if (isset($detail['image']) && $detail['image']->isValid()) {
                    $imagePath = $detail['image']->store('gatepass_images', 'public');
                }

                $gatepass->details()->create([
                    'project_id' => $detail['project_id'],
                    'service_id' => $detail['service_id'],
                    'description' => $detail['description'] ?? null,
                    'qty' => $detail['qty'],
                    'unit' => $detail['unit'],
                    'rate' => $detail['rate'],
                    'image' => $imagePath,
                ]);
            }

            DB::commit();
            return redirect()->route('gatepass.index')->with('success', 'Gatepass created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gatepass creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function edit($id)
    {
        $gatepass = Gatepass::with('details')->findOrFail($id);
        $coas = ChartOfAccounts::all();
        $projects = Project::all();
        $services = Service::all();
        return view('gatepass.edit', compact('gatepass', 'coas', 'projects', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.project_id' => 'required|exists:projects,id',
            'details.*.service_id' => 'required|exists:services,id',
            'details.*.description' => 'nullable|string',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.unit' => 'required|string|max:50',
            'details.*.rate' => 'required|numeric|min:0',
            'details.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $gatepass = Gatepass::findOrFail($id);
            $gatepass->update([
                'coa_id' => $request->coa_id,
                'date' => $request->date,
            ]);

            $gatepass->details()->delete();

            foreach ($request->details as $detail) {
                $imagePath = null;
                if (isset($detail['image']) && $detail['image']->isValid()) {
                    $imagePath = $detail['image']->store('gatepass_images', 'public');
                }

                $gatepass->details()->create([
                    'project_id' => $detail['project_id'],
                    'service_id' => $detail['service_id'],
                    'description' => $detail['description'] ?? null,
                    'qty' => $detail['qty'],
                    'unit' => $detail['unit'],
                    'rate' => $detail['rate'],
                    'image' => $imagePath,
                ]);
            }

            DB::commit();
            return redirect()->route('gatepass.index')->with('success', 'Gatepass updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gatepass update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $gatepass = Gatepass::findOrFail($id);
            $gatepass->details()->delete();
            $gatepass->delete();

            return redirect()->route('gatepass.index')->with('success', 'Gatepass deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Gatepass deletion failed: ' . $e->getMessage());
            return redirect()->route('gatepass.index')->with('error', 'Failed to delete gatepass.');
        }
    }
}
