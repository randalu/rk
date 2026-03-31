@extends('layouts.app')
@section('title', 'Sales Report')

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">From</label>
                <input type="date" name="from" class="form-control"
                       value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">To</label>
                <input type="date" name="to" class="form-control"
                       value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('reports.sales') }}"
                   class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Bills</div>
            <div class="fs-4 fw-bold text-primary">{{ $summary['total_bills'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Value</div>
            <div class="fs-4 fw-bold text-success">
                {{ config('app.currency') }} {{ number_format($summary['total_value'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Collected</div>
            <div class="fs-4 fw-bold text-info">
                {{ config('app.currency') }} {{ number_format($summary['total_paid'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Returns</div>
            <div class="fs-4 fw-bold text-danger">
                {{ config('app.currency') }} {{ number_format($summary['total_returns'], 2) }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- By Salesperson --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Sales by Salesperson
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Salesperson</th>
                            <th>Bills</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bySalesperson as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['name'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ config('app.currency') }} {{ number_format($row['total'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">No data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Sales Distribution
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Bills Table --}}
<div class="card">
    <div class="card-header">
        <i class="bi bi-receipt me-2"></i>Bills
        ({{ $from->format('d M Y') }} — {{ $to->format('d M Y') }})
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Salesperson</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                @php
                    $paid    = $bill->payments->sum('amount');
                    $balance = $bill->total - $paid;
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('bills.show', $bill) }}"
                           class="text-decoration-none">#{{ $bill->id }}</a>
                    </td>
                    <td>{{ $bill->created_at->format('d M Y') }}</td>
                    <td>{{ $bill->customer?->name ?? '—' }}</td>
                    <td>{{ $bill->salesperson?->name ?? '—' }}</td>
                    <td>{{ config('app.currency') }} {{ number_format($bill->total, 2) }}</td>
                    <td class="text-success">
                        {{ config('app.currency') }} {{ number_format($paid, 2) }}
                    </td>
                    <td class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ config('app.currency') }} {{ number_format($balance, 2) }}
                    </td>
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
                    <td colspan="8" class="text-center text-muted py-4">No bills in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const labels = @json($bySalesperson->pluck('name'));
const values = @json($bySalesperson->pluck('total'));

if (labels.length > 0) {
    new Chart(document.getElementById('salesChart'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#4f46e5','#10b981','#f59e0b',
                    '#ef4444','#06b6d4','#8b5cf6'
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                tooltip: {
                    callbacks: {
                        label: ctx => '{{ config('app.currency') }} ' +
                            parseFloat(ctx.raw).toLocaleString()
                    }
                }
            }
        }
    });
}
</script>
@endsection