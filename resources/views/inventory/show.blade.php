@extends('layouts.app')
@section('title', $inventory->name)

@section('content')
@php $isLow = $inventory->qty <= $inventory->low_stock_threshold; @endphp

<div class="row g-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-box-seam me-2"></i>Item Details</span>
                @if($isLow)
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-exclamation-triangle"></i> Low Stock
                    </span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Product</dt>
                    <dd class="col-7 fw-semibold">{{ $inventory->name }}</dd>

                    <dt class="col-5 text-muted small">SKU</dt>
                    <dd class="col-7"><code>{{ $inventory->sku }}</code></dd>

                    <dt class="col-5 text-muted small">Batch</dt>
                    <dd class="col-7">
                        @if($inventory->batch_number)
                            <span class="badge bg-secondary">{{ $inventory->batch_number }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted small">Expiry</dt>
                    <dd class="col-7">
                        @if($inventory->expiry_date)
                            @php $daysLeft = now()->diffInDays($inventory->expiry_date, false); @endphp
                            <span class="badge bg-{{ $daysLeft < 30 ? 'danger' : ($daysLeft < 90 ? 'warning text-dark' : 'success') }}">
                                {{ $inventory->expiry_date->format('d M Y') }}
                                ({{ $daysLeft > 0 ? $daysLeft . ' days left' : 'Expired' }})
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted small">Stock</dt>
                    <dd class="col-7">
                        <span class="fs-5 fw-bold {{ $isLow ? 'text-danger' : 'text-success' }}">
                            {{ $inventory->qty }}
                        </span>
                        <span class="text-muted small">units</span>
                    </dd>

                    <dt class="col-5 text-muted small">Low Alert</dt>
                    <dd class="col-7">{{ $inventory->low_stock_threshold }} units</dd>

                    <dt class="col-5 text-muted small">Sell Price</dt>
                    <dd class="col-7">{{ config('app.currency') }} {{ number_format($inventory->price, 2) }}</dd>

                    <dt class="col-5 text-muted small">Cost Price</dt>
                    <dd class="col-7">{{ config('app.currency') }} {{ number_format($inventory->cost, 2) }}</dd>

                    <dt class="col-5 text-muted small">Margin</dt>
                    <dd class="col-7">
                        @php $margin = $inventory->price - $inventory->cost; @endphp
                        <span class="text-{{ $margin >= 0 ? 'success' : 'danger' }}">
                            {{ config('app.currency') }} {{ number_format($margin, 2) }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted small">Supplier</dt>
                    <dd class="col-7">{{ $inventory->supplier?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Added</dt>
                    <dd class="col-7">{{ $inventory->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('inventory.edit', $inventory) }}"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('inventory.index') }}"
                   class="btn btn-sm btn-outline-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-layers me-2"></i>All Batches — {{ $inventory->sku }}
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                        @php
                            $batchIsLow  = $batch->qty <= $batch->low_stock_threshold;
                            $isCurrent   = $batch->id === $inventory->id;
                        @endphp
                        <tr class="{{ $isCurrent ? 'table-primary' : '' }}">
                            <td>
                                <span class="badge bg-{{ $isCurrent ? 'primary' : 'secondary' }}">
                                    {{ $batch->batch_number ?? '—' }}
                                </span>
                            </td>
                            <td>
                                @if($batch->expiry_date)
                                    @php $d = now()->diffInDays($batch->expiry_date, false); @endphp
                                    <span class="badge bg-{{ $d < 30 ? 'danger' : ($d < 90 ? 'warning text-dark' : 'success') }}">
                                        {{ $batch->expiry_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $batchIsLow ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                    {{ $batch->qty }}
                                </span>
                            </td>
                            <td>{{ config('app.currency') }} {{ number_format($batch->cost, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection