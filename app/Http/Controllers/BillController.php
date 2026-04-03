<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Commission;
use App\Models\CommissionTier;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Salesperson;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('customer', 'salesperson', 'createdBy', 'payments')
            ->latest()
            ->paginate(15);

        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $salespeople = Salesperson::where('is_active', true)->orderBy('name')->get();
        $inventory = Inventory::where('qty', '>', 0)
            ->orderBy('name')
            ->orderBy('expiry_date')
            ->get();

        return view('bills.create', compact('customers', 'salespeople', 'inventory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'salesperson_id' => 'required|exists:salespeople,id',
            'payment_type' => 'required|in:cash,card,online',
            'payment_term' => 'required|in:cash,credit_30,credit_45,credit_60',
            'advance_payment' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $bill = null;

        try {
            DB::transaction(function () use ($request, &$bill) {
                $dueDate = match ($request->payment_term) {
                    'credit_30' => Carbon::today()->addDays(30),
                    'credit_45' => Carbon::today()->addDays(45),
                    'credit_60' => Carbon::today()->addDays(60),
                    default => Carbon::today(),
                };

                $total = collect($request->items)
                    ->sum(fn($item) => $item['qty'] * $item['unit_price']);

                $bill = Bill::create([
                    'customer_id' => $request->customer_id,
                    'salesperson_id' => $request->salesperson_id,
                    'created_by' => auth()->id(),
                    'payment_type' => $request->payment_type,
                    'payment_term' => $request->payment_term,
                    'due_date' => $dueDate,
                    'advance_payment' => $request->advance_payment ?? 0,
                    'total' => $total,
                ]);

                $requestedQuantities = $this->getRequestedQuantities($request->items);
                $inventoryItems = $this->getLockedInventoryItems(array_keys($requestedQuantities));

                $this->ensureSufficientStock($requestedQuantities, $inventoryItems);

                foreach ($request->items as $item) {
                    $inventoryItem = $inventoryItems->get($item['inventory_id']);

                    $bill->items()->create([
                        'inventory_id' => $item['inventory_id'],
                        'batch_number' => $inventoryItem->batch_number ?: 'No batch',
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => $item['qty'] * $item['unit_price'],
                    ]);
                }

                foreach ($requestedQuantities as $inventoryId => $qty) {
                    $inventoryItem = $inventoryItems->get($inventoryId);
                    $inventoryItem->qty -= $qty;
                    $inventoryItem->save();

                    if ($inventoryItem->isLowStock()) {
                        $smsService = new SmsService();
                        $smsService->lowStockSms($inventoryItem);
                    }
                }

                $this->createCommission($bill, $total);
            });
        } catch (Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        if ($bill) {
            $bill->load('customer', 'salesperson', 'payments');
            $smsService = new SmsService();
            $smsService->newBillSms($bill);
        }

        return redirect()->route('bills.index')
            ->with('success', 'Bill created successfully.');
    }

    public function show(Bill $bill)
    {
        $bill->load('customer', 'salesperson', 'createdBy', 'items.inventory', 'payments.receivedBy', 'commission');

        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $customers = Customer::orderBy('name')->get();
        $salespeople = Salesperson::where('is_active', true)->orderBy('name')->get();
        $billInventoryIds = $bill->items()->pluck('inventory_id');
        $inventory = Inventory::where(function ($query) use ($billInventoryIds) {
                $query->where('qty', '>', 0)
                    ->orWhereIn('id', $billInventoryIds);
            })
            ->orderBy('name')
            ->orderBy('expiry_date')
            ->get();

        return view('bills.edit', compact('bill', 'customers', 'salespeople', 'inventory'));
    }

    public function update(Request $request, Bill $bill)
    {
        if ($bill->payments->count() > 0) {
            return redirect()->route('bills.show', $bill)
                ->with('error', 'Cannot edit a bill that has payments recorded.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'salesperson_id' => 'required|exists:salespeople,id',
            'payment_type' => 'required|in:cash,card,online',
            'payment_term' => 'required|in:cash,credit_30,credit_45,credit_60',
            'advance_payment' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $bill) {
                $requestedQuantities = $this->getRequestedQuantities($request->items);
                $previousQuantities = $bill->items
                    ->whereNotNull('inventory_id')
                    ->groupBy('inventory_id')
                    ->map(fn($items) => (int) $items->sum('qty'))
                    ->all();

                $inventoryIds = array_values(array_unique(array_merge(
                    array_keys($requestedQuantities),
                    array_keys($previousQuantities)
                )));

                $inventoryItems = $this->getLockedInventoryItems($inventoryIds);

                foreach ($requestedQuantities as $inventoryId => $requestedQty) {
                    $inventoryItem = $inventoryItems->get($inventoryId);
                    $availableQty = $inventoryItem->qty + ($previousQuantities[$inventoryId] ?? 0);

                    if ($availableQty < $requestedQty) {
                        $batchNumber = $inventoryItem->batch_number ?: 'No batch';

                        throw new \RuntimeException(
                            "Insufficient stock for {$inventoryItem->name} (Batch: {$batchNumber}). Available: {$availableQty}"
                        );
                    }
                }

                $dueDate = match ($request->payment_term) {
                    'credit_30' => Carbon::today()->addDays(30),
                    'credit_45' => Carbon::today()->addDays(45),
                    'credit_60' => Carbon::today()->addDays(60),
                    default => Carbon::today(),
                };

                $total = collect($request->items)
                    ->sum(fn($item) => $item['qty'] * $item['unit_price']);

                $bill->update([
                    'customer_id' => $request->customer_id,
                    'salesperson_id' => $request->salesperson_id,
                    'payment_type' => $request->payment_type,
                    'payment_term' => $request->payment_term,
                    'due_date' => $dueDate,
                    'advance_payment' => $request->advance_payment ?? 0,
                    'total' => $total,
                ]);

                $bill->items()->delete();

                foreach ($request->items as $item) {
                    $inventoryItem = $inventoryItems->get($item['inventory_id']);

                    $bill->items()->create([
                        'inventory_id' => $item['inventory_id'],
                        'batch_number' => $inventoryItem->batch_number ?: 'No batch',
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => $item['qty'] * $item['unit_price'],
                    ]);
                }

                foreach ($inventoryItems as $inventoryId => $inventoryItem) {
                    $inventoryItem->qty = $inventoryItem->qty
                        + ($previousQuantities[$inventoryId] ?? 0)
                        - ($requestedQuantities[$inventoryId] ?? 0);

                    $inventoryItem->save();
                }

                if ($bill->commission) {
                    $bill->commission->delete();
                }

                $this->createCommission($bill, $total);
            });
        } catch (Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        if ($bill->payments->count() > 0) {
            return redirect()->route('bills.index')
                ->with('error', 'Cannot delete a bill with payments.');
        }

        DB::transaction(function () use ($bill) {
            foreach ($bill->items as $item) {
                if ($item->inventory_id) {
                    Inventory::where('id', $item->inventory_id)
                        ->increment('qty', $item->qty);
                }
            }

            $bill->commission?->delete();
            $bill->delete();
        });

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted and inventory restored.');
    }

    private function createCommission(Bill $bill, float $total): void
    {
        $salesperson = Salesperson::find($bill->salesperson_id);

        if (!$salesperson || !$salesperson->user_id) {
            return;
        }

        $type = $salesperson->commission_type;
        $threshold = $type === 'qty_based'
            ? $bill->items->sum('qty')
            : $total;

        $tier = CommissionTier::getApplicableTier($type, $threshold);

        if (!$tier) {
            return;
        }

        $amount = round($total * ($tier->rate / 100), 2);

        Commission::create([
            'bill_id' => $bill->id,
            'salesperson_id' => $salesperson->id,
            'bill_total' => $total,
            'commission_type' => $type,
            'commission_rate' => $tier->rate,
            'commission_amount' => $amount,
            'deducted_returns' => 0,
            'net_commission' => $amount,
            'status' => 'pending',
        ]);
    }

    public function exportPdf(Bill $bill)
    {
        $bill->load('customer', 'salesperson', 'createdBy', 'items.inventory', 'payments');

        $isPdf = true;

        $pdf = Pdf::loadView('bills.print', compact('bill', 'isPdf'))
            ->setPaper('a5', 'portrait');

        return $pdf->download('Invoice-' . $bill->id . '.pdf');
    }

    public function printView(Bill $bill)
    {
        $bill->load('customer', 'salesperson', 'createdBy', 'items.inventory', 'payments');

        $isPdf = false;

        return view('bills.print', compact('bill', 'isPdf'));
    }

    private function getRequestedQuantities(array $items): array
    {
        return collect($items)
            ->groupBy('inventory_id')
            ->map(fn($group) => (int) $group->sum('qty'))
            ->all();
    }

    private function getLockedInventoryItems(array $inventoryIds): Collection
    {
        $inventoryItems = Inventory::whereIn('id', $inventoryIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($inventoryItems->count() !== count($inventoryIds)) {
            throw new \RuntimeException('One or more selected inventory batches are no longer available.');
        }

        return $inventoryItems;
    }

    private function ensureSufficientStock(array $requestedQuantities, Collection $inventoryItems): void
    {
        foreach ($requestedQuantities as $inventoryId => $requestedQty) {
            $inventoryItem = $inventoryItems->get($inventoryId);

            if ($inventoryItem->qty < $requestedQty) {
                $batchNumber = $inventoryItem->batch_number ?: 'No batch';

                throw new \RuntimeException(
                    "Insufficient stock for {$inventoryItem->name} (Batch: {$batchNumber}). Available: {$inventoryItem->qty}"
                );
            }
        }
    }
}
