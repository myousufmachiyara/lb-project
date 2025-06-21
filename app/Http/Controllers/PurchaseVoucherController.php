<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\PurchaseVoucher;
use App\Models\ChartOfAccounts;
use App\Models\Project;
use App\Models\PurchaseVoucherDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use TCPDF;

class PurchaseVoucherController extends Controller
{
    public function index()
    {
        $vouchers = PurchaseVoucher::with('details', 'coa')->latest()->get();
        return view('purchase-voucher.index', compact('vouchers'));
    }

    public function create()
    {
        $services = Service::all();
        $coas = ChartOfAccounts::all();
        $projects = Project::all();
        return view('purchase-voucher.create', compact('services', 'coas', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'voucher_id' => 'required|unique:purchase_vouchers,voucher_id', // Remove this line
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'date' => 'required|date',
            'status' => 'required|string',
            'details.*.project_id' => 'required|exists:projects,id',
            'details.*.service' => 'required|string',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.unit' => 'required|string',
            'details.*.rate' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Step 1: Create voucher without voucher_id
            $voucher = PurchaseVoucher::create([
                'coa_id' => $request->coa_id,
                'date' => $request->date,
                'status' => $request->status,
                'created_by' => Auth::id()
            ]);

            // Step 2: Generate voucher_id as PV + padded ID (e.g. PV01)
            $voucher->voucher_id = 'PV' . str_pad($voucher->id, 2, '0', STR_PAD_LEFT);
            $voucher->save();

            // Step 3: Store details
            foreach ($request->details as $detail) {
                $imagePath = null;
                if (isset($detail['image']) && $detail['image']->isValid()) {
                    $imagePath = $detail['image']->store('purchase_voucher_images', 'public');
                }

                $voucher->details()->create([
                    'project_id' => $detail['project_id'],
                    'service' => $detail['service'],
                    'description' => $detail['description'] ?? '',
                    'image' => $imagePath,
                    'qty' => $detail['qty'],
                    'unit' => $detail['unit'],
                    'rate' => $detail['rate'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchase-vouchers.index')->with('success', 'Purchase Voucher created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Failed to create voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(PurchaseVoucher $purchaseVoucher)
    {
        $purchaseVoucher->load('details.project', 'coa');
        return view('purchase-voucher.show', compact('purchaseVoucher'));
    }

public function edit(PurchaseVoucher $purchaseVoucher)
{
    $purchaseVoucher->load('details');

    $coas = ChartOfAccounts::all();
    $projects = Project::all();
    $services = Service::all();

    return view('purchase-voucher.edit', [
        'voucher' => $purchaseVoucher,
        'coas' => $coas,
        'projects' => $projects,
        'services' => $services,
    ]);
}

    public function update(Request $request, PurchaseVoucher $purchaseVoucher)
    {
        $request->validate([
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'date' => 'required|date',
            'status' => 'required|string',
            'details.*.project_id' => 'required|exists:projects,id',
            'details.*.service' => 'required|string',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.unit' => 'required|string',
            'details.*.rate' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $purchaseVoucher->update([
                'coa_id' => $request->coa_id,
                'date' => $request->date,
                'status' => $request->status,
            ]);

            $purchaseVoucher->details()->delete();

            foreach ($request->details as $detail) {
                $imagePath = null;
                if (isset($detail['image']) && $detail['image']->isValid()) {
                    $imagePath = $detail['image']->store('purchase_voucher_images', 'public');
                }

                $purchaseVoucher->details()->create([
                    'project_id' => $detail['project_id'],
                    'service' => $detail['service'],
                    'description' => $detail['description'] ?? '',
                    'image' => $imagePath,
                    'qty' => $detail['qty'],
                    'unit' => $detail['unit'],
                    'rate' => $detail['rate'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchase-vouchers.index')->with('success', 'Voucher updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors('Update failed: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PurchaseVoucher $purchaseVoucher)
    {
        try {
            $purchaseVoucher->delete();
            return redirect()->route('purchase-vouchers.index')->with('success', 'Voucher deleted successfully.');
        } catch (Exception $e) {
            return redirect()->route('purchase-vouchers.index')->withErrors('Delete failed: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $voucher = PurchaseVoucher::with('coa', 'details.project', 'details.service')->findOrFail($id);

        $pdf = new \TCPDF();

        $pdf->SetTitle('Purchase Voucher - ' . $voucher->coa->name);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $logo = public_path('images/company-logo.png'); // Update this if needed

        $html = '
            <div style="text-align: center;">
                <img src="' . $logo . '" height="60"><br>
                <h2>Purchase Voucher</h2>
            </div>
            <table cellspacing="0" cellpadding="4">
                <tr>
                    <td><strong>Vendor:</strong> ' . $voucher->coa->name . '</td>
                    <td align="right"><strong>Date:</strong> ' . $voucher->date . '</td>
                </tr>
            </table>
            <br><br>
            <table border="1" cellpadding="4" cellspacing="0">
                <thead>
                    <tr style="background-color: #f5f5f5; font-weight: bold;">
                        <th width="5%">#</th>
                        <th width="15%">Project</th>
                        <th width="17%">Service</th>
                        <th width="21%">Description</th>
                        <th width="8%">Qty</th>
                        <th width="8%">Unit</th>
                        <th width="10%">Rate</th>
                        <th width="14%">Amount</th>
                    </tr>
                </thead>
                <tbody>';

        $totalAmount = 0;

        foreach ($voucher->details as $index => $detail) {
            $amount = $detail->qty * $detail->rate;
            $totalAmount += $amount;

            $html .= '
                <tr>
                    <td width="5%">' . ($index + 1) . '</td>
                    <td width="15%">' . ($detail->project->name ?? '-') . '</td>
                    <td width="17%">' . ($detail->service->name ?? '-') . '</td>
                    <td width="21%">' . ($detail->description ?? '-') . '</td>
                    <td width="8%" align="center">' . $detail->qty . '</td>
                    <td width="8%" align="center">' . $detail->unit . '</td>
                    <td width="10%" align="right">' . number_format($detail->rate, 2) . '</td>
                    <td width="14%" align="right">' . number_format($amount, 2) . '</td>
                </tr>';
        }

        $html .= '
            <tr>
                <td colspan="7" align="right"><strong>Total</strong></td>
                <td align="right"><strong>' . number_format($totalAmount, 2) . '</strong></td>
            </tr>';

        $html .= '</tbody></table><br><br>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Signature block
        $pdf->SetY(-40);
        $signatureHtml = '
            <table width="100%" cellpadding="4">
                <tr>
                    <td align="right"><strong>Approved By:</strong> ____________________</td>
                </tr>
            </table>';
        $pdf->writeHTML($signatureHtml, true, false, true, false, '');

        $pdf->lastPage();
        return $pdf->Output('purchase-voucher-' . $voucher->id . '.pdf', 'I');
    }

}
