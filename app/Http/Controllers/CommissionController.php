<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Salesperson;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Commission::with('bill.customer', 'salesperson', 'approvedBy')
            ->latest();

        // Filter by salesperson
        if ($request->filled('salesperson_id')) {
            $query->where('salesperson_id', $request->salesperson_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions  = $query->paginate(15)->withQueryString();
        $salespeople  = Salesperson::where('is_active', true)->orderBy('name')->get();

        $summary = [
            'pending'   => Commission::where('status', 'pending')->sum('net_commission'),
            'payable'   => Commission::where('status', 'payable')->sum('net_commission'),
            'paid'      => Commission::where('status', 'paid')->sum('net_commission'),
        ];

        return view('commissions.index', compact('commissions', 'salespeople', 'summary'));
    }

    public function show(Commission $commission)
    {
        $commission->load('bill.customer', 'salesperson', 'approvedBy');
        return view('commissions.show', compact('commission'));
    }

    public function release(Commission $commission)
    {
        if ($commission->status !== 'payable') {
            return redirect()->route('commissions.index')
                             ->with('error', 'Only payable commissions can be released.');
        }

        $commission->update([
            'status'      => 'paid',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'paid_at'     => now(),
        ]);

        return redirect()->route('commissions.show', $commission)
                         ->with('success', 'Commission marked as paid.');
    }

    public function cancel(Commission $commission)
    {
        if (!in_array($commission->status, ['pending', 'payable'])) {
            return redirect()->route('commissions.index')
                             ->with('error', 'Only pending or payable commissions can be cancelled.');
        }

        $commission->update([
            'status'      => 'cancelled',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('commissions.show', $commission)
                         ->with('success', 'Commission cancelled.');
    }

    public function create() {}
    public function store(Request $request) {}
    public function edit(Commission $commission) {}
    public function update(Request $request, Commission $commission) {}
    public function destroy(Commission $commission) {}
}