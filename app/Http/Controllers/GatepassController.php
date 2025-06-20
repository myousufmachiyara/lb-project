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
        $coas = ChartOfAccounts::where('account_type', 'vendor')->get();
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

    public function print($id)
    {
        $gatepass = Gatepass::with('coa', 'details.project', 'details')->findOrFail($id);

        $pdf = new \TCPDF();

        $pdf->SetTitle('Gatepass - ' . $gatepass->coa->name);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // Logo + Header
        $logo = public_path('images/company-logo.png'); // Update path to your logo
        $html = '
            <div style="text-align: center;">
                <img src="' . $logo . '" height="60"><br>
                <h2>Gatepass</h2>
            </div>
            <table cellspacing="0" cellpadding="4">
                <tr>
                    <td><strong>Vendor:</strong> ' . $gatepass->coa->name . '</td>
                    <td align="right"><strong>Date:</strong> ' . $gatepass->date . '</td>
                </tr>
            </table>
            <br><br>
            <table border="1" cellpadding="4" cellspacing="0">
                <thead>
                    <tr style="background-color: #f5f5f5;font-weight:bold">
                        <th width="5%">#</th>
                        <th width="20%">Project</th>
                        <th width="20%">Service</th>
                        <th width="25%">Description</th>
                        <th width="8%">Qty</th>
                        <th width="7%">Unit</th>
                        <th width="15%">Rate</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($gatepass->details as $index => $detail) {
            $html .= '
                <tr>
                    <td width="5%">' . ($index + 1) . '</td>
                    <td width="20%">' . ($detail->project->name ?? '-') . '</td>
                    <td width="20%">' . ($detail->service->name ?? '-') . '</td>
                    <td width="25%">' . ($detail->description ?? '-') . '</td>
                    <td width="8%" align="center">' . $detail->qty . '</td>
                    <td width="7%" align="center">' . $detail->unit . '</td>
                    <td width="15%" align="right">' . number_format($detail->rate, 2) . '</td>
                </tr>';
        }

        $html .= '</tbody></table><br><br>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Signature
        $pdf->SetY(-40);
        $signatureHtml = '
            <table width="100%" cellpadding="4">
                <tr>
                    <td align="right"><strong>Authorized By:</strong> ____________________</td>
                </tr>
            </table>';
        $pdf->writeHTML($signatureHtml, true, false, true, false, '');

        $pdf->lastPage();
        return $pdf->Output('gatepass-' . $gatepass->id . '.pdf', 'I');
    }
}
