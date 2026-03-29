<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\ProductReturn;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get bills that have been fully paid
        $paidBillIds = Payment::select('bill_id')
            ->groupBy('bill_id')
            ->havingRaw('SUM(amount) >= (SELECT total FROM bills WHERE id = bill_id)')
            ->pluck('bill_id');

        $stats = [
            'total_customers'  => Customer::count(),
            'total_bills'      => Bill::count(),
            'total_revenue'    => Payment::sum('amount') ?? 0,
            'low_stock_items'  => Inventory::whereColumn('qty', '<=', 'low_stock_threshold')->count(),
            'pending_returns'  => ProductReturn::where('status', 'pending')->count(),
            'unpaid_bills'     => Bill::whereNotIn('id', $paidBillIds)->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}