@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Stat Cards Row 1 --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Total Revenue</div>
                    <div class="fs-4 fw-bold">
                        {{ config('app.currency') }} {{ number_format($stats['total_revenue'], 2) }}
                    </div>
                </div>
                <i class="bi bi-cash-stack fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">
                This month: {{ config('app.currency') }} {{ number_format($revenueThisMonth, 2) }}
                @if($revenueChange != 0)
                    <span class="{{ $revenueChange > 0 ? 'text-success' : 'text-danger' }}">
                        ({{ $revenueChange > 0 ? '+' : '' }}{{ $revenueChange }}%)
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Total Bills</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_bills'] }}</div>
                </div>
                <i class="bi bi-receipt fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">
                <span class="text-warning">{{ $stats['unpaid_bills'] }} unpaid</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Customers</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_customers'] }}</div>
                </div>
                <i class="bi bi-people fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">Active accounts</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">This Month Expenses</div>
                    <div class="fs-4 fw-bold">
                        {{ config('app.currency') }} {{ number_format($stats['total_expenses'], 2) }}
                    </div>
                </div>
                <i class="bi bi-wallet2 fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">{{ now()->format('F Y') }}</div>
        </div>
    </div>
</div>

{{-- Stat Cards Row 2 --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Low Stock Items</div>
                    <div class="fs-4 fw-bold">{{ $stats['low_stock_items'] }}</div>
                </div>
                <i class="bi bi-exclamation-triangle fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">Need restocking</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Pending Returns</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_returns'] }}</div>
                </div>
                <i class="bi bi-arrow-return-left fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">Awaiting approval</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#06b6d4,#0891b2)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Payable Commissions</div>
                    <div class="fs-4 fw-bold">
                        {{ config('app.currency') }} {{ number_format($stats['payable_commissions'], 2) }}
                    </div>
                </div>
                <i class="bi bi-graph-up-arrow fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">Ready to release</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#ec4899,#db2777)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small mb-1 opacity-75">Due in 7 Days</div>
                    <div class="fs-4 fw-bold">{{ $upcomingDue->count() }}</div>
                </div>
                <i class="bi bi-calendar-event fs-3 opacity-50"></i>
            </div>
            <div class="small opacity-75 mt-2">Upcoming collections</div>
        </div>
    </div>
</div>

{{-- Chart + Recent Bills --}}
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Revenue vs Expenses — Last 6 Months
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock</span>
                <a href="{{ route('inventory.index') }}"
                   class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($lowStockItems as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                        <div>
                            <div class="fw-semibold small">{{ $item->name }}</div>
                            <div class="text-muted" style="font-size:11px">
                                {{ $item->batch_number ?? 'No batch' }}
                            </div>
                        </div>
                        <span class="badge bg-danger">
                            {{ $item->qty }} / {{ $item->low_stock_threshold }}
                        </span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3 small">
                        All items are well stocked ✓
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Recent Bills + Overdue + Upcoming --}}
<div class="row g-4">

    {{-- Recent Bills --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Recent Bills</span>
                <a href="{{ route('bills.index') }}"
                   class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBills as $bill)
                        @php
                            $paid    = $bill->payments->sum('amount');
                            $balance = $bill->total - $paid;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('bills.show', $bill) }}"
                                   class="text-decoration-none fw-semibold">
                                    #{{ $bill->id }}
                                </a>
                            </td>
                            <td>{{ $bill->customer?->name ?? '—' }}</td>
                            <td>{{ config('app.currency') }} {{ number_format($bill->total, 2) }}</td>
                            <td>
                                @if($balance <= 0)
                                    <span class="badge bg-success">Paid</span>
                                @elseif($bill->due_date?->isPast())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3 small">
                                No bills yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Overdue + Upcoming --}}
    <div class="col-md-6">

        {{-- Overdue Bills --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-exclamation-circle me-2 text-danger"></i>Overdue Bills
                </span>
                <a href="{{ route('bills.index') }}"
                   class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($overdueBills as $bill)
                    @php
                        $balance  = $bill->total - $bill->payments->sum('amount');
                        $daysOver = now()->diffInDays($bill->due_date);
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                        <div>
                            <a href="{{ route('bills.show', $bill) }}"
                               class="text-decoration-none fw-semibold">
                                #{{ $bill->id }} — {{ $bill->customer?->name ?? '—' }}
                            </a>
                            <div class="text-danger" style="font-size:11px">
                                {{ $daysOver }} days overdue
                            </div>
                        </div>
                        <span class="badge bg-danger">
                            {{ config('app.currency') }} {{ number_format($balance, 2) }}
                        </span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3 small">
                        No overdue bills ✓
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Upcoming Due --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-check me-2 text-info"></i>Due in Next 7 Days
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($upcomingDue as $bill)
                    @php
                        $balance = $bill->total - $bill->payments->sum('amount');
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                        <div>
                            <a href="{{ route('bills.show', $bill) }}"
                               class="text-decoration-none fw-semibold">
                                #{{ $bill->id }} — {{ $bill->customer?->name ?? '—' }}
                            </a>
                            <div class="text-muted" style="font-size:11px">
                                Due {{ $bill->due_date->format('d M Y') }}
                                ({{ now()->diffInDays($bill->due_date) }} days left)
                            </div>
                        </div>
                        <span class="badge bg-info text-dark">
                            {{ config('app.currency') }} {{ number_format($balance, 2) }}
                        </span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3 small">
                        No upcoming due bills.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Chart JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const labels   = @json(collect($chartData)->pluck('month'));
const revenue  = @json(collect($chartData)->pluck('revenue'));
const expenses = @json(collect($chartData)->pluck('expenses'));

const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Revenue',
                data: revenue,
                backgroundColor: 'rgba(79,70,229,0.8)',
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Expenses',
                data: expenses,
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => '{{ config('app.currency') }} ' +
                        parseFloat(ctx.raw).toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        })
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => '{{ config('app.currency') }} ' +
                        val.toLocaleString()
                }
            }
        }
    }
});
</script>
@endsection