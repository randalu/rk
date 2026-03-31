@extends('layouts.app')
@section('title', 'Commission Report')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">From</label>
                <input type="date" name="from" class="form-control"
                       value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">To</label>
                <input type="date" name="to" class="form-control"
                       value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Salesperson</label>
                <select name="salesperson_id" class="form-select">
                    <option value="">All</option>
                    @foreach($salespeople as $sp)
                    <option value="{{ $sp->id }}"
                        {{ request('salesperson_id') == $sp->id ? 'selected' : '' }}>
                        {{ $sp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="text-muted small">Gross Commission</div>
            <div class="fs-5 fw-bold">
                {{ config('app.currency') }} {{ number_format($summary['gross'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="text-muted small">Net Commission</div>
            <div class="fs-5 fw-bold text-primary">
                {{ config('app.currency') }} {{ number_format($summary['net'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="text-muted small">Paid Out</div>
            <div class="fs-5 fw-bold text-success">
                {{ config('app.currency') }} {{ number_format($summary['paid'], 2) }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-graph-up-arrow me-2"></i>Commissions
        ({{ $from->format('d M Y') }} — {{ $to->format('d M Y') }})
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Bill</th>
                    <th>Customer</th>
                    <th>Salesperson</th>
                    <th>Rate</th>
                    <th>Gross</th>
                    <th>Deductions</th>
                    <th>Net</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $c)
                @php
                    $colors = [
                        'pending'   => 'warning text-dark',
                        'payable'   => 'info',
                        'paid'      => 'success',
                        'cancelled' => 'secondary',
                    ];
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('bills.show', $c->bill_id) }}"
                           class="text-decoration-none">#{{ $c->bill_id }}</a>
                    </td>
                    <td>{{ $c->bill?->customer?->name ?? '—' }}</td>
                    <td>{{ $c->salesperson?->name ?? '—' }}</td>
                    <td>{{ $c->commission_rate }}%</td>
                    <td>{{ config('app.currency') }} {{ number_format($c->commission_amount, 2) }}</td>
                    <td class="text-danger">
                        @if($c->deducted_returns > 0)
                            - {{ config('app.currency') }} {{ number_format($c->deducted_returns, 2) }}
                        @else —
                        @endif
                    </td>
                    <td class="fw-bold">
                        {{ config('app.currency') }} {{ number_format($c->net_commission, 2) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $colors[$c->status] }}">
                            {{ ucfirst($c->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection