@extends('layouts.app')
@section('title', 'Commission #' . $commission->id)

@section('content')
@php
    $statusColors = [
        'pending'   => 'warning text-dark',
        'payable'   => 'info',
        'paid'      => 'success',
        'cancelled' => 'secondary',
    ];
@endphp

<div class="row g-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up-arrow me-2"></i>Commission #{{ $commission->id }}</span>
                <span class="badge bg-{{ $statusColors[$commission->status] }}">
                    {{ ucfirst($commission->status) }}
                </span>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-6 text-muted small">Bill</dt>
                    <dd class="col-6">
                        <a href="{{ route('bills.show', $commission->bill_id) }}"
                           class="text-decoration-none fw-semibold">
                            #{{ $commission->bill_id }}
                        </a>
                    </dd>

                    <dt class="col-6 text-muted small">Customer</dt>
                    <dd class="col-6">
                        {{ $commission->bill?->customer?->name ?? '—' }}
                    </dd>

                    <dt class="col-6 text-muted small">Salesperson</dt>
                    <dd class="col-6 fw-semibold">
                        {{ $commission->salesperson?->name ?? '—' }}
                    </dd>

                    <dt class="col-6 text-muted small">Commission Type</dt>
                    <dd class="col-6">
                        {{ $commission->commission_type === 'value_based' ? 'Value Based' : 'Qty Based' }}
                    </dd>

                    <dt class="col-6 text-muted small">Bill Total</dt>
                    <dd class="col-6">
                        {{ config('app.currency') }} {{ number_format($commission->bill_total, 2) }}
                    </dd>

                    <dt class="col-6 text-muted small">Rate Applied</dt>
                    <dd class="col-6 fw-semibold">{{ $commission->commission_rate }}%</dd>

                    <dt class="col-6 text-muted small">Gross Commission</dt>
                    <dd class="col-6">
                        {{ config('app.currency') }}
                        {{ number_format($commission->commission_amount, 2) }}
                    </dd>

                    <dt class="col-6 text-muted small">Return Deductions</dt>
                    <dd class="col-6 text-danger">
                        - {{ config('app.currency') }}
                        {{ number_format($commission->deducted_returns, 2) }}
                    </dd>

                    <dt class="col-6 text-muted small">Net Commission</dt>
                    <dd class="col-6 fw-bold fs-6">
                        {{ config('app.currency') }}
                        {{ number_format($commission->net_commission, 2) }}
                    </dd>

                    @if($commission->approved_by)
                    <dt class="col-6 text-muted small">Approved By</dt>
                    <dd class="col-6">{{ $commission->approvedBy?->name }}</dd>

                    <dt class="col-6 text-muted small">Approved At</dt>
                    <dd class="col-6">
                        {{ $commission->approved_at?->format('d M Y H:i') ?? '—' }}
                    </dd>
                    @endif

                    @if($commission->paid_at)
                    <dt class="col-6 text-muted small">Paid At</dt>
                    <dd class="col-6">
                        {{ $commission->paid_at->format('d M Y H:i') }}
                    </dd>
                    @endif

                    @if($commission->notes)
                    <dt class="col-6 text-muted small">Notes</dt>
                    <dd class="col-6">{{ $commission->notes }}</dd>
                    @endif
                </dl>
            </div>

            @if(in_array($commission->status, ['pending', 'payable']))
            <div class="card-footer d-flex gap-2">
                @if($commission->status === 'payable')
                <form action="{{ route('commissions.release', $commission) }}"
                      method="POST"
                      onsubmit="return confirm('Mark this commission as paid?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check-circle me-1"></i> Mark as Paid
                    </button>
                </form>
                @endif
                <form action="{{ route('commissions.cancel', $commission) }}"
                      method="POST"
                      onsubmit="return confirm('Cancel this commission?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Commission Flow
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @php
                        $steps = [
                            ['pending',   'Bill created',          'Bill saved → commission calculated at current tier rate'],
                            ['payable',   'Bill fully paid',       'All payments received → commission becomes payable'],
                            ['paid',      'Manager releases',      'Manager approves → commission marked as paid'],
                        ];
                    @endphp

                    @foreach($steps as [$status, $title, $desc])
                    @php
                        $isActive = $commission->status === $status;
                        $isPast   = match($commission->status) {
                            'payable' => $status === 'pending',
                            'paid'    => in_array($status, ['pending', 'payable']),
                            default   => false,
                        };
                    @endphp
                    <div class="d-flex gap-3 align-items-start">
                        <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;
                            background:{{ $isActive ? '#4f46e5' : ($isPast ? '#10b981' : '#e5e7eb') }};
                            color:{{ ($isActive || $isPast) ? '#fff' : '#888' }}">
                            {{ $isPast ? '✓' : ($isActive ? '●' : '○') }}
                        </div>
                        <div>
                            <div class="fw-semibold {{ $isActive ? 'text-primary' : ($isPast ? 'text-success' : 'text-muted') }}">
                                {{ $title }}
                            </div>
                            <div class="text-muted small">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach

                    @if($commission->status === 'cancelled')
                    <div class="alert alert-danger mb-0 small">
                        <i class="bi bi-x-circle me-1"></i> This commission was cancelled.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection