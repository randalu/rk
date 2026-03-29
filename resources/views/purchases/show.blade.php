@extends('layouts.app')
@section('title', 'Purchase #' . $purchase->id)

@section('content')
@php
    $statusColors = [
        'pending'   => 'warning text-dark',
        'received'  => 'success',
        'cancelled' => 'danger',
    ];
@endphp

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-truck me-2"></i>Purchase Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">PO #</dt>
                    <dd class="col-7 fw-semibold">{{ $purchase->id }}</dd>

                    <dt class="col-5 text-muted small">Date</dt>
                    <dd class="col-7">{{ $purchase->purchased_at?->format('d M Y') ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Supplier</dt>
                    <dd class="col-7">{{ $purchase->supplier?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Total</dt>
                    <dd class="col-7 fw-bold">
                        {{ config('app.currency') }} {{ number_format($purchase->total, 2) }}
                    </dd>

                    <dt class="col-5 text-muted small">Status</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $statusColors[$purchase->status] }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted small">Created By</dt>
                    <dd class="col-7">{{ $purchase->createdBy?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Created At</dt>
                    <dd class="col-7">{{ $purchase->created_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>

            @if($purchase->status === 'pending')
            <div class="card-footer d-flex flex-wrap gap-2">
                <form action="{{ route('purchases.receive', $purchase) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success"
                            onclick="return confirm('Mark as received? This will update inventory with batch records.')">
                        <i class="bi bi-check-circle me-1"></i> Mark Received
                    </button>
                </form>
                <form action="{{ route('purchases.cancel', $purchase) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Cancel this purchase?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
                <a href="{{ route('purchases.edit', $purchase) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            </div>
            @endif

            @if($purchase->status === 'received')
            <div class="card-footer">
                <div class="alert alert-success mb-0 small">
                    <i class="bi bi-check-circle me-1"></i>
                    Inventory updated with batch records when this was received.
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>Purchase Items
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->inventory?->name ?? '—' }}</td>
                            <td><code>{{ $item->batch_number }}</code></td>
                            <td>
                                @if($item->expiry_date)
                                    @php $d = now()->diffInDays($item->expiry_date, false); @endphp
                                    <span class="badge bg-{{ $d < 30 ? 'danger' : ($d < 90 ? 'warning text-dark' : 'success') }}">
                                        {{ $item->expiry_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ config('app.currency') }} {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="fw-semibold">
                                {{ config('app.currency') }} {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total</td>
                            <td class="fw-bold">
                                {{ config('app.currency') }} {{ number_format($purchase->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection