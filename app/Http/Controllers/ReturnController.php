<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\ReturnItem;
use App\Models\Bill;
use App\Models\Inventory;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ProductReturn::with('bill.customer', 'approvedBy')
            ->latest()
            ->paginate(15);

        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $bills = Bill::with('customer', 'items.inventory')
            ->latest()
            ->get();

        return view('returns.create', compact('bills'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_id'              => 'required|exists:bills,id',
            'reason'               => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.bill_item_id' => 'required|exists:bill_items,id',
            'items.*.qty'          => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $bill  = Bill::with('items.inventory')->find($request->bill_id);
            $total = 0;

            $return = ProductReturn::create([
                'bill_id' => $bill->id,
                'status'  => 'pending',
                'reason'  => $request->reason,
                'total'   => 0,
            ]);

            foreach ($request->items as $item) {
                $billItem = $bill->items->find($item['bill_item_id']);
                if (!$billItem) continue;

                $lineTotal = $item['qty'] * $billItem->unit_price;
                $total    += $lineTotal;

                $return->items()->create([
                    'bill_item_id' => $billItem->id,
                    'inventory_id' => $billItem->inventory_id,
                    'batch_number' => $billItem->batch_number,
                    'qty'          => $item['qty'],
                    'line_total'   => $lineTotal,
                ]);
            }

            $return->update(['total' => $total]);
        });

        return redirect()->route('returns.index')
                         ->with('success', 'Return request submitted successfully.');
    }

    public function show(ProductReturn $return)
    {
        $return->load('bill.customer', 'items.inventory', 'items.billItem', 'approvedBy');
        return view('returns.show', compact('return'));
    }

    public function edit(ProductReturn $return) {}
    public function update(Request $request, ProductReturn $return) {}

    public function destroy(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('returns.index')
                             ->with('error', 'Only pending returns can be deleted.');
        }

        $return->delete();

        return redirect()->route('returns.index')
                         ->with('success', 'Return deleted.');
    }

    public function accept(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('returns.show', $return)
                             ->with('error', 'This return has already been ' . $return->status . '.');
        }

        DB::transaction(function () use ($return) {
            $return->load('items.inventory');

            // Restore inventory qty for each returned item
            foreach ($return->items as $item) {
                if ($item->inventory_id) {
                    Inventory::where('id', $item->inventory_id)
                             ->increment('qty', $item->qty);
                }
            }

            // Update commission — deduct return value
            $commission = Commission::where('bill_id', $return->bill_id)->first();

            if ($commission) {
                $commission->deducted_returns += $return->total;
                $commission->net_commission    = $commission->commission_amount
                                                - $commission->deducted_returns;

                // If bill is no longer fully paid after return, revert to pending
                $bill      = Bill::find($return->bill_id);
                $totalPaid = $bill->payments()->sum('amount');

                if ($totalPaid < ($bill->total - $return->total)) {
                    $commission->status = 'pending';
                }

                $commission->save();
            }

            $return->update([
                'status'      => 'accepted',
                'approved_by' => auth()->id(),
            ]);
        });

        return redirect()->route('returns.show', $return)
                         ->with('success', 'Return accepted. Inventory restored.');
    }

    public function reject(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('returns.show', $return)
                             ->with('error', 'This return has already been ' . $return->status . '.');
        }

        $return->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('returns.show', $return)
                         ->with('success', 'Return rejected.');
    }

    // AJAX endpoint — load bill items when a bill is selected on create form
    public function getBillItems(Bill $bill)
    {
        $bill->load('items.inventory');
        return response()->json($bill->items);
    }
}