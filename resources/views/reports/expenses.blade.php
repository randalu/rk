@extends('layouts.app')
@section('title', 'Expense Report')

@section('content')
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
                <a href="{{ route('reports.expenses') }}"
                   class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-tags me-2"></i>By Category
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th>Count</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byCategory as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['name'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td class="text-danger fw-semibold">
                                {{ config('app.currency') }} {{ number_format($row['total'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">No data</td>
                        </tr>
                        @endforelse
                        @if($byCategory->count() > 0)
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-end">Total</td>
                            <td class="fw-bold text-danger">
                                {{ config('app.currency') }} {{ number_format($summary['total'], 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Distribution
            </div>
            <div class="card-body">
                <canvas id="expenseChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>All Expenses
        ({{ $from->format('d M Y') }} — {{ $to->format('d M Y') }})
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $expense->category?->name ?? '—' }}
                        </span>
                    </td>
                    <td>{{ $expense->description ?? '—' }}</td>
                    <td class="fw-semibold text-danger">
                        {{ config('app.currency') }} {{ number_format($expense->amount, 2) }}
                    </td>
                    <td>{{ $expense->createdBy?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No expenses in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const labels = @json($byCategory->pluck('name'));
const values = @json($byCategory->pluck('total'));

if (labels.length > 0) {
    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#ef4444','#f59e0b','#10b981',
                    '#4f46e5','#06b6d4','#8b5cf6'
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
}
</script>
@endsection