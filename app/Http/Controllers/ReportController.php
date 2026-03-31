<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Commission;
use App\Models\Salesperson;
use App\Models\ProductReturn;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $bills = Bill::with('customer', 'salesperson', 'payments')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $summary = [
            'total_bills'   => $bills->count(),
            'total_value'   => $bills->sum('total'),
            'total_paid'    => Payment::whereBetween('created_at', [$from, $to])->sum('amount'),
            'total_returns' => ProductReturn::where('status', 'accepted')
                                ->whereBetween('created_at', [$from, $to])
                                ->sum('total'),
        ];

        // Sales by salesperson
        $bySalesperson = $bills->groupBy('salesperson_id')->map(fn($group) => [
            'name'  => $group->first()->salesperson?->name ?? 'Direct Sale',
            'count' => $group->count(),
            'total' => $group->sum('total'),
        ])->values();

        return view('reports.sales', compact(
            'bills', 'summary', 'bySalesperson', 'from', 'to'
        ));
    }

    public function commissions(Request $request)
    {
        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $salespeople = Salesperson::where('is_active', true)->orderBy('name')->get();

        $query = Commission::with('bill.customer', 'salesperson')
            ->whereBetween('created_at', [$from, $to]);

        if ($request->filled('salesperson_id')) {
            $query->where('salesperson_id', $request->salesperson_id);
        }

        $commissions = $query->latest()->get();

        $summary = [
            'gross'     => $commissions->sum('commission_amount'),
            'deducted'  => $commissions->sum('deducted_returns'),
            'net'       => $commissions->sum('net_commission'),
            'paid'      => $commissions->where('status', 'paid')->sum('net_commission'),
            'pending'   => $commissions->whereIn('status', ['pending', 'payable'])->sum('net_commission'),
        ];

        return view('reports.commissions', compact(
            'commissions', 'summary', 'salespeople', 'from', 'to'
        ));
    }

    public function inventory(Request $request)
    {
        $inventory = Inventory::with('supplier')
            ->orderBy('name')
            ->orderBy('expiry_date')
            ->get();

        $summary = [
            'total_products'  => $inventory->groupBy('sku')->count(),
            'total_batches'   => $inventory->count(),
            'total_value'     => $inventory->sum(fn($i) => $i->qty * $i->price),
            'low_stock'       => $inventory->filter(fn($i) => $i->qty <= $i->low_stock_threshold)->count(),
            'expiring_soon'   => $inventory->filter(fn($i) =>
                $i->expiry_date && $i->expiry_date->diffInDays(now(), false) < 0
                && $i->expiry_date->diffInDays(now(), false) > -90
            )->count(),
        ];

        return view('reports.inventory', compact('inventory', 'summary'));
    }

    public function expenses(Request $request)
    {
        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $expenses = Expense::with('category', 'createdBy')
            ->whereBetween('expense_date', [$from, $to])
            ->latest('expense_date')
            ->get();

        $byCategory = $expenses->groupBy('category_id')->map(fn($group) => [
            'name'  => $group->first()->category?->name ?? 'Uncategorised',
            'total' => $group->sum('amount'),
            'count' => $group->count(),
        ])->sortByDesc('total')->values();

        $summary = [
            'total'      => $expenses->sum('amount'),
            'categories' => $byCategory->count(),
        ];

        return view('reports.expenses', compact(
            'expenses', 'byCategory', 'summary', 'from', 'to'
        ));
    }
}