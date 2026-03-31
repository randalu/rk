<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\ProductReturn;
use App\Models\Expense;
use App\Models\Commission;
use App\Models\Purchase;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Core stats
        $stats = [
            'total_customers'  => Customer::count(),
            'total_bills'      => Bill::count(),
            'total_revenue'    => Payment::sum('amount') ?? 0,
            'low_stock_items'  => Inventory::whereColumn('qty', '<=', 'low_stock_threshold')->count(),
            'pending_returns'  => ProductReturn::where('status', 'pending')->count(),
            'unpaid_bills'     => $this->getUnpaidBillsCount(),
            'total_expenses'   => Expense::whereMonth('expense_date', now()->month)
                                         ->whereYear('expense_date', now()->year)
                                         ->sum('amount') ?? 0,
            'payable_commissions' => Commission::where('status', 'payable')->sum('net_commission') ?? 0,
        ];

        // Revenue this month vs last month
        $revenueThisMonth = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $revenueLastMonth = Payment::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueChange = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        // Recent bills
        $recentBills = Bill::with('customer', 'salesperson', 'payments')
            ->latest()
            ->take(6)
            ->get();

        // Overdue bills
        $overdueBills = Bill::with('customer', 'payments')
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->latest()
            ->take(5)
            ->get()
            ->filter(fn($b) => $b->payments->sum('amount') < $b->total);

        // Low stock items
        $lowStockItems = Inventory::whereColumn('qty', '<=', 'low_stock_threshold')
            ->orderBy('qty')
            ->take(5)
            ->get();

        // Upcoming due dates (next 7 days)
        $upcomingDue = Bill::with('customer', 'payments')
            ->whereBetween('due_date', [today(), today()->addDays(7)])
            ->get()
            ->filter(fn($b) => $b->payments->sum('amount') < $b->total)
            ->take(5);

        // Monthly revenue chart data (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData[] = [
                'month'   => $month->format('M Y'),
                'revenue' => Payment::whereMonth('created_at', $month->month)
                                    ->whereYear('created_at', $month->year)
                                    ->sum('amount'),
                'expenses' => Expense::whereMonth('expense_date', $month->month)
                                     ->whereYear('expense_date', $month->year)
                                     ->sum('amount'),
            ];
        }

        return view('dashboard', compact(
            'stats',
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueChange',
            'recentBills',
            'overdueBills',
            'lowStockItems',
            'upcomingDue',
            'chartData'
        ));
    }

    private function getUnpaidBillsCount(): int
    {
        $paidBillIds = Payment::select('bill_id')
            ->groupBy('bill_id')
            ->havingRaw('SUM(amount) >= (SELECT total FROM bills WHERE id = bill_id)')
            ->pluck('bill_id');

        return Bill::whereNotIn('id', $paidBillIds)->count();
    }
}