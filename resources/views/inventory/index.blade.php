@extends('layouts.app')
@section('title', 'Inventory')

@section('content')

@if($lowStockCount > 0)
<div class="alert alert-warning d-flex align-items-center mb-4">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>{{ $lowStockCount }} batch(es)</strong>&nbsp;are running low on stock.
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-seam me-2"></i>Inventory</span>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:220px">Product</th>
                    <th>SKU</th>
                    <th>Batch</th>
                    <th>Expiry</th>
                    <th>Stock</th>
                    <th>Sell Price</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Group all inventory rows by SKU
                    $grouped = $inventory->groupBy('sku');
                @endphp

                @forelse($grouped as $sku => $batches)
                    @foreach($batches as $index => $item)
                    @php $isLow = $item->qty <= $item->low_stock_threshold; @endphp
                    <tr class="{{ $isLow ? 'table-warning' : '' }}">

                        {{-- Product name only on first row of each group --}}
                        @if($index === 0)
                        <td rowspan="{{ $batches->count() }}"
                            class="fw-semibold align-middle border-end"
                            style="border-left: 3px solid {{ $isLow ? '#ffc107' : '#198754' }}; padding-left: 12px;">
                            {{ $item->name }}
                            @if($batches->where('qty', '<=', $batches->first()->low_stock_threshold)->count() > 0)
                                <div class="mt-1">
                                    <span class="badge bg-warning text-dark" style="font-size:9px">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        {{ $batches->where('qty', '<=', $item->low_stock_threshold)->count() }} low batch(es)
                                    </span>
                                </div>
                            @endif
                            <div class="text-muted small mt-1">
                                {{ $batches->count() }} batch(es) ·
                                {{ $batches->sum('qty') }} total units
                            </div>
                        </td>

                        {{-- SKU only on first row --}}
                        <td rowspan="{{ $batches->count() }}"
                            class="align-middle border-end text-center">
                            <code>{{ $sku }}</code>
                        </td>
                        @endif

                        {{-- Batch specific columns --}}
                        <td>
                            @if($item->batch_number)
                                <span class="badge bg-secondary">{{ $item->batch_number }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($item->expiry_date)
                                @php $daysLeft = now()->diffInDays($item->expiry_date, false); @endphp
                                <span class="badge bg-{{ $daysLeft < 30 ? 'danger' : ($daysLeft < 90 ? 'warning text-dark' : 'success') }}">
                                    {{ $item->expiry_date->format('d M Y') }}
                                </span>
                                @if($daysLeft < 90)
                                    <div class="text-muted" style="font-size:10px">
                                        {{ $daysLeft > 0 ? $daysLeft . 'd left' : 'Expired' }}
                                    </div>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold {{ $isLow ? 'text-danger' : 'text-success' }}">
                                {{ $item->qty }}
                            </span>
                            <span class="text-muted small">/ {{ $item->low_stock_threshold }}</span>
                            @if($isLow)
                                <div style="font-size:10px" class="text-danger">Low stock</div>
                            @endif
                        </td>
                        <td>{{ config('app.currency') }} {{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->supplier?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('inventory.show', $item) }}"
                               class="btn btn-sm btn-outline-secondary"
                               title="View batch">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('inventory.edit', $item) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Edit batch">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('inventory.destroy', $item) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete batch {{ $item->batch_number }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete batch">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Divider row between product groups --}}
                    @if($index === $batches->count() - 1 && !$loop->parent->last)
                    <tr style="height:6px;background:#f8f9fa">
                        <td colspan="8" class="p-0"></td>
                    </tr>
                    @endif

                    @endforeach
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No inventory yet.
                        <a href="{{ route('inventory.create') }}">Add your first product</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($inventory->hasPages())
    <div class="card-footer">{{ $inventory->links() }}</div>
    @endif
</div>
@endsection