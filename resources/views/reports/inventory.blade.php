@extends('layouts.app')
@section('title', 'Inventory Report')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Products</div>
            <div class="fs-4 fw-bold text-primary">{{ $summary['total_products'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Batches</div>
            <div class="fs-4 fw-bold">{{ $summary['total_batches'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Stock Value</div>
            <div class="fs-4 fw-bold text-success">
                {{ config('app.currency') }} {{ number_format($summary['total_value'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Low Stock Batches</div>
            <div class="fs-4 fw-bold text-danger">{{ $summary['low_stock'] }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-box-seam me-2"></i>Full Inventory
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Batch</th>
                    <th>Expiry</th>
                    <th>Qty</th>
                    <th>Sell Price</th>
                    <th>Stock Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $item)
                @php $isLow = $item->qty <= $item->low_stock_threshold; @endphp
                <tr class="{{ $isLow ? 'table-warning' : '' }}">
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td><code>{{ $item->sku }}</code></td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $item->batch_number ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if($item->expiry_date)
                            @php $d = now()->diffInDays($item->expiry_date, false); @endphp
                            <span class="badge bg-{{ $d < 30 ? 'danger' : ($d < 90 ? 'warning text-dark' : 'success') }}">
                                {{ $item->expiry_date->format('d M Y') }}
                            </span>
                        @else —
                        @endif
                    </td>
                    <td class="{{ $isLow ? 'text-danger fw-bold' : '' }}">{{ $item->qty }}</td>
                    <td>{{ config('app.currency') }} {{ number_format($item->price, 2) }}</td>
                    <td>{{ config('app.currency') }} {{ number_format($item->qty * $item->price, 2) }}</td>
                    <td>
                        @if($isLow)
                            <span class="badge bg-warning text-dark">Low Stock</span>
                        @else
                            <span class="badge bg-success">OK</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No inventory data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection