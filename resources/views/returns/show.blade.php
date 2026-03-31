@extends('layouts.app')
@section('title', 'Return #' . $return->id)

@section('content')
@php
    $statusColors = [
        'pending'  => 'warning text-dark',
        'accepted' => 'success',
        'rejected' => 'danger',
    ];
@endphp

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-arrow-return-left me-2"></i>Return #{{ $return->id }}</span>
                <span class="badge bg-{{ $statusColors[$return->status] }}">
                    {{ ucfirst($return->status) }}
                </span>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Bill</dt>
                    <dd class="col-7">
                        <a href="{{ route('bills.show', $return->bill_id) }}"
                           class="text-decoration-none fw-semibold">
                            #{{ $return->bill_id }}
                        </a>
                    </dd>

                    <dt class="col-5 text-muted small">Customer</dt>
                    <dd class="col-7">{{ $return->bill?->customer?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Return Total</dt>
                    <dd class="col-7 fw-bold text-danger">
                        {{ config('app.currency') }} {{ number_format($return->total, 2) }}
                    </dd>

                    <dt class="col-5 text-muted small">Reason</dt>
                    <dd class="col-7">{{ $return->reason ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Submitted</dt>
                    <dd class="col-7">{{ $return->created_at->format('d M Y H:i') }}</dd>

                    <dt class="col-5 text-muted small">Approved By</dt>
                    <dd class="col-7">{{ $return->approvedBy?->name ?? '—' }}</dd>
                </dl>
            </div>

            @if($return->status === 'pending')
            <div class="card-footer d-flex gap-2">
                <form action="{{ route('returns.accept', $return) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="btn btn-sm btn-success"
                            onclick="return confirm('Accept this return? Inventory will be restored.')">
                        <i class="bi bi-check-circle me-1"></i> Accept
                    </button>
                </form>
                <form action="{{ route('returns.reject', $return) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Reject this return?')">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>
                </form>
            </div>
            @endif

            @if($return->status === 'accepted')
            <div class="card-footer">
                <div class="alert alert-success mb-0 small">
                    <i class="bi bi-check-circle me-1"></i>
                    Inventory has been restored and commission deducted.
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>Returned Items
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Qty Returned</th>
                            <th>Original Price</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $item)
                        <tr>
                            <td class="fw-semibold">
                                {{ $item->inventory?->name ?? '—' }}
                            </td>
                            <td><code>{{ $item->batch_number }}</code></td>
                            <td>{{ $item->qty }}</td>
                            <td>
                                {{ config('app.currency') }}
                                {{ number_format($item->billItem?->unit_price ?? 0, 2) }}
                            </td>
                            <td class="fw-semibold text-danger">
                                {{ config('app.currency') }} {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Return Total</td>
                            <td class="fw-bold text-danger">
                                {{ config('app.currency') }} {{ number_format($return->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Returns
                </a>
                <a href="{{ route('bills.show', $return->bill_id) }}"
                   class="btn btn-sm btn-outline-primary ms-2">
                    <i class="bi bi-receipt me-1"></i> View Original Bill
                </a>
            </div>
        </div>
    </div>
</div>
@endsection