<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
{
    $inventory = Inventory::with('supplier')
        ->orderBy('name')
        ->orderBy('sku')
        ->orderBy('expiry_date')
        ->paginate(50);

    $lowStockCount = Inventory::whereColumn('qty', '<=', 'low_stock_threshold')->count();

    return view('inventory.index', compact('inventory', 'lowStockCount'));
}

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'         => 'nullable|exists:suppliers,id',
            'name'                => 'required|string|max:255',
            'sku'                 => 'required|string|max:100|unique:inventory,sku',
            'qty'                 => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'price'               => 'required|numeric|min:0',
            'cost'                => 'required|numeric|min:0',
        ]);

        Inventory::create($validated);

        return redirect()->route('inventory.index')
                         ->with('success', 'Product added to inventory.');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('supplier');

        // Show all batches of the same SKU
        $batches = Inventory::where('sku', $inventory->sku)
            ->orderBy('expiry_date')
            ->get();

        return view('inventory.show', compact('inventory', 'batches'));
    }

    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'suppliers'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'supplier_id'         => 'nullable|exists:suppliers,id',
            'name'                => 'required|string|max:255',
            'sku'                 => 'required|string|max:100|unique:inventory,sku,' . $inventory->id,
            'qty'                 => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'price'               => 'required|numeric|min:0',
            'cost'                => 'required|numeric|min:0',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')
                         ->with('success', 'Inventory item updated.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.index')
                         ->with('success', 'Item deleted.');
    }
}