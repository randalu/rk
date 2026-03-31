<?php

namespace App\Http\Controllers;

use App\Models\CommissionTier;
use Illuminate\Http\Request;

class CommissionTierController extends Controller
{
    public function index()
    {
        $qtyTiers   = CommissionTier::where('type', 'qty_based')
                        ->orderBy('min_threshold')->get();
        $valueTiers = CommissionTier::where('type', 'value_based')
                        ->orderBy('min_threshold')->get();

        return view('commissions.tiers', compact('qtyTiers', 'valueTiers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:qty_based,value_based',
            'min_threshold' => 'required|numeric|min:0',
            'max_threshold' => 'nullable|numeric|gt:min_threshold',
            'rate'          => 'required|numeric|min:0|max:100',
        ]);

        CommissionTier::create($request->only(
            'type', 'min_threshold', 'max_threshold', 'rate'
        ) + ['is_active' => true]);

        return redirect()->route('commission-tiers.index')
                         ->with('success', 'Tier added successfully.');
    }

    public function update(Request $request, CommissionTier $commissionTier)
    {
        $request->validate([
            'min_threshold' => 'required|numeric|min:0',
            'max_threshold' => 'nullable|numeric|gt:min_threshold',
            'rate'          => 'required|numeric|min:0|max:100',
            'is_active'     => 'nullable|boolean',
        ]);

        $commissionTier->update([
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
            'rate'          => $request->rate,
            'is_active'     => $request->boolean('is_active'),
        ]);

        return redirect()->route('commission-tiers.index')
                         ->with('success', 'Tier updated.');
    }

    public function destroy(CommissionTier $commissionTier)
    {
        $commissionTier->delete();

        return redirect()->route('commission-tiers.index')
                         ->with('success', 'Tier deleted.');
    }

    public function create() {}
    public function show(CommissionTier $commissionTier) {}
    public function edit(CommissionTier $commissionTier) {}
}