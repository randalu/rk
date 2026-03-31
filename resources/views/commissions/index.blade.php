@extends('layouts.app')
@section('title', 'Commissions')

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="small mb-1 opacity-75">Pending</div>
            <div class="fs-4 fw-bold">
                {{ config('app.currency') }} {{ number_format($summary['pending'], 2) }}
            </div>
            <div class="small opacity-75 mt-1">Awaiting payment collection</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
            <div class="small mb-1 opacity-75">Payable</div>
            <div class="fs-4 fw-bold">
                {{ config('app.currency') }} {{ number_format($summary['payable'], 2) }}
            </div>
            <div class="small opacity-75 mt-1">Ready to release</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="small mb-1 opacity-75">Paid Out</div>
            <div class="fs-4 fw-bold">
                {{ config('app.currency') }} {{ number_format($summary['paid'], 2) }}
            </div>
            <div class="small opacity-75 mt-1">All time paid</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-graph-up-arrow me-2"></i>Commissions</span>
        <div class="d-flex gap-2">
            <a href="{{ route('commission-tiers.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-gear me-1"></i> Tier Settings
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-body border-bottom pb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Salesperson</label>
                <select name="salesperson_id" class="form-select form-select-sm">
                    <option value="">All Salespeople</option>
                    @foreach($salespeople as $sp)
                    <option value="{{ $sp->id }}"
                        {{ request('salesperson_id') == $sp->id ? 'selected' : '' }}>
                        {{ $sp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="payable"   {{ request('status') == 'payable'   ? 'selected' : '' }}>Payable</option>
                    <option value="paid"      {{ request('status') == 'paid'      ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['salesperson_id', 'status']))
            <div class="col-md-2">
                <a href="{{ route('commissions.index') }}"
                   class="btn btn-outline-secondary btn-sm w-100">
                    Clear
                </a>
            </div>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Bill</th>
                    <th>Customer</th>
                    <th>Salesperson</th>
                    <th>Rate</th>
                    <th>Gross</th>
                    <th>Deductions</th>
                    <th>Net</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $commission)
                @php
                    $statusColors = [
                        'pending'   => 'warning text-dark',
                        'payable'   => 'info',
                        'paid'      => 'success',
                        'cancelled' => 'secondary',
                    ];
                @endphp
                <tr>
                    <td class="text-muted small">{{ $commission->id }}</td>
                    <td>
                        <a href="{{ route('bills.show', $commission->bill_id) }}"
                           class="text-decoration-none fw-semibold">
                            #{{ $commission->bill_id }}
                        </a>
                    </td>
                    <td>{{ $commission->bill?->customer?->name ?? '—' }}</td>
                    <td>{{ $commission->salesperson?->name ?? '—' }}</td>
                    <td>{{ $commission->commission_rate }}%</td>
                    <td>
                        {{ config('app.currency') }}
                        {{ number_format($commission->commission_amount, 2) }}
                    </td>
                    <td class="text-danger">
                        @if($commission->deducted_returns > 0)
                            - {{ config('app.currency') }}
                            {{ number_format($commission->deducted_returns, 2) }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="fw-bold">
                        {{ config('app.currency') }}
                        {{ number_format($commission->net_commission, 2) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusColors[$commission->status] }}">
                            {{ ucfirst($commission->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('commissions.show', $commission) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($commission->status === 'payable')
                        <form action="{{ route('commissions.release', $commission) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Mark this commission as paid?')">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No commissions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="card-footer">{{ $commissions->links() }}</div>
    @endif
</div>
@endsection