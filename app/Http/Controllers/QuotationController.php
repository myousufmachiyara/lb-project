<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Service;
use App\Models\QuotationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::get();
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $services = Service::all(); // Or Service::pluck('name', 'id');
        return view('quotations.create', compact('services'));    
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.service_id' => 'required|exists:services,id',
            'details.*.description' => 'nullable|string',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.unit' => 'required|string|max:50',
            'details.*.cost' => 'required|numeric|min:0',
            'details.*.service_charges_per_pc' => 'required|numeric|min:0|max:100',
            'details.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $quotation = Quotation::create([
                'customer_name' => $request->customer_name,
                'date' => $request->date,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->details as $detail) {
                $imagePath = null;

                if (isset($detail['image']) && $detail['image']->isValid()) {
                    $imagePath = $detail['image']->store('quotation_images', 'public');
                }

                $quotation->details()->create([
                    'service_id' => $detail['service_id'],
                    'image' => $imagePath,
                    'description' => $detail['description'] ?? null,
                    'quantity' => $detail['quantity'],
                    'unit' => $detail['unit'],
                    'cost' => $detail['cost'],
                    'service_charges_per_pc' => $detail['service_charges_per_pc'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(Quotation $quotation)
    {
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        return view('quotations.edit', compact('quotation'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.service_id' => 'required|exists:services,id',
            'details.*.description' => 'nullable|string',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.unit' => 'required|string|max:50',
            'details.*.cost' => 'required|numeric|min:0',
            'details.*.service_charges_per_pc' => 'nullable|numeric|min:0|max:100',
            'details.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            // Update quotation main info
            $quotation->update([
                'customer_name' => $request->customer_name,
                'date' => $request->date,
                'created_by' => Auth::id(), // Optional: update creator
            ]);

            // Delete old quotation details
            foreach ($quotation->details as $oldDetail) {
                // Optionally delete old images from storage
                if ($oldDetail->image && Storage::disk('public')->exists($oldDetail->image)) {
                    Storage::disk('public')->delete($oldDetail->image);
                }
            }
            $quotation->details()->delete();

            // Insert new details
            foreach ($request->details as $detail) {
                $imagePath = null;

                if (isset($detail['image']) && $detail['image']->isValid()) {
                    $imagePath = $detail['image']->store('quotation_images', 'public');
                }

                $quotation->details()->create([
                    'service_id' => $detail['service_id'],
                    'image' => $imagePath,
                    'description' => $detail['description'] ?? null,
                    'quantity' => $detail['quantity'],
                    'unit' => $detail['unit'],
                    'cost' => $detail['cost'],
                    'service_charges_per_pc' => $detail['service_charges_per_pc'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Quotation update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update quotation. Please try again.');
        }
    }

    public function destroy(Quotation $quotation)
    {
        try {
            $quotation->delete();
            return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
        } catch (\Exception $e) {
            Log::error('Quotation deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete quotation.');
        }
    }
}
