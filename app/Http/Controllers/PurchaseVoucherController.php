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
            'voucher_id' => 'required|unique:purchase_vouchers,voucher_id',
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
            $voucher = PurchaseVoucher::create([
                'voucher_id' => $request->voucher_id,
                'coa_id' => $request->coa_id,
                'date' => $request->date,
                'status' => $request->status,
                'created_by' => Auth::id()
            ]);

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
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors('Failed to create voucher. ' . $e->getMessage())->withInput();
        }
    }

    public function show(PurchaseVoucher $purchaseVoucher)
    {
        $purchaseVoucher->load('details.project', 'coa');
        return view('purchase-voucher.show', compact('purchaseVoucher'));
    }

    public function edit(PurchaseVoucher $purchaseVoucher)
    {
        $services = Service::all();
        $coas = ChartOfAccounts::all();
        $projects = Project::all();
        $purchaseVoucher->load('details');

        return view('purchase-voucher.edit', compact('purchaseVoucher', 'services', 'coas', 'projects'));
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
}
