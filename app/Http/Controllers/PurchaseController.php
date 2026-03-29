<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier', 'createdBy')
            ->latest()
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $inventory = Inventory::orderBy('name')->get();
        return view('purchases.create', compact('suppliers', 'inventory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'              => 'nullable|exists:suppliers,id',
            'purchased_at'             => 'required|date',
            'items'                    => 'required|array|min:1',
            'items.*.inventory_id'     => 'required|exists:inventory,id',
            'items.*.batch_number'     => 'required|string',
            'items.*.expiry_date'      => 'required|date',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.unit_cost'        => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = collect($request->items)
                ->sum(fn($i) => $i['qty'] * $i['unit_cost']);

            $purchase = Purchase::create([
                'supplier_id'  => $request->supplier_id,
                'created_by'   => auth()->id(),
                'total'        => $total,
                'status'       => 'pending',
                'purchased_at' => $request->purchased_at,
            ]);

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'inventory_id' => $item['inventory_id'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date'  => $item['expiry_date'],
                    'qty'          => $item['qty'],
                    'unit_cost'    => $item['unit_cost'],
                    'line_total'   => $item['qty'] * $item['unit_cost'],
                ]);
            }
        });

        return redirect()->route('purchases.index')
                         ->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'createdBy', 'items.inventory');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return redirect()->route('purchases.show', $purchase)
                             ->with('error', 'Cannot edit a received purchase.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $inventory = Inventory::orderBy('name')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'inventory'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return redirect()->route('purchases.show', $purchase)
                             ->with('error', 'Cannot edit a received purchase.');
        }

        $request->validate([
            'supplier_id'          => 'nullable|exists:suppliers,id',
            'purchased_at'         => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.batch_number' => 'required|string',
            'items.*.expiry_date'  => 'required|date',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.unit_cost'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $total = collect($request->items)
                ->sum(fn($i) => $i['qty'] * $i['unit_cost']);

            $purchase->update([
                'supplier_id'  => $request->supplier_id,
                'total'        => $total,
                'purchased_at' => $request->purchased_at,
            ]);

            $purchase->items()->delete();

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'inventory_id' => $item['inventory_id'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date'  => $item['expiry_date'],
                    'qty'          => $item['qty'],
                    'unit_cost'    => $item['unit_cost'],
                    'line_total'   => $item['qty'] * $item['unit_cost'],
                ]);
            }
        });

        return redirect()->route('purchases.show', $purchase)
                         ->with('success', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return redirect()->route('purchases.index')
                             ->with('error', 'Cannot delete a received purchase.');
        }

        $purchase->delete();

        return redirect()->route('purchases.index')
                         ->with('success', 'Purchase deleted.');
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return redirect()->route('purchases.show', $purchase)
                             ->with('error', 'Purchase is already ' . $purchase->status . '.');
        }

        DB::transaction(function () use ($purchase) {
            $purchase->load('items.inventory');

            foreach ($purchase->items as $item) {
                $product = $item->inventory;

                // Check if a row with same SKU + batch already exists
                $existing = Inventory::where('sku', $product->sku)
                    ->where('batch_number', $item->batch_number)
                    ->first();

                if ($existing) {
                    // Just increment qty on existing batch row
                    $existing->increment('qty', $item->qty);
                } else {
                    // Create a new batch row in inventory
                    Inventory::create([
                        'supplier_id'         => $purchase->supplier_id,
                        'name'                => $product->name,
                        'sku'                 => $product->sku,
                        'batch_number'        => $item->batch_number,
                        'expiry_date'         => $item->expiry_date,
                        'qty'                 => $item->qty,
                        'low_stock_threshold' => $product->low_stock_threshold,
                        'price'               => $product->price,
                        'cost'                => $item->unit_cost,
                    ]);
                }
            }

            $purchase->update(['status' => 'received']);
        });

        return redirect()->route('purchases.show', $purchase)
                         ->with('success', 'Purchase received. Inventory updated with batch records.');
    }

    public function cancel(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return redirect()->route('purchases.show', $purchase)
                             ->with('error', 'Only pending purchases can be cancelled.');
        }

        $purchase->update(['status' => 'cancelled']);

        return redirect()->route('purchases.show', $purchase)
                         ->with('success', 'Purchase cancelled.');
    }
}