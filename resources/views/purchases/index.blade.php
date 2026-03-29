@extends('layouts.app')
@section('title', 'Purchases')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-truck me-2"></i>Purchases</span>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Purchase
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                @php
                    $statusColors = [
                        'pending'   => 'warning text-dark',
                        'received'  => 'success',
                        'cancelled' => 'danger',
                    ];
                @endphp
                <tr>
                    <td class="text-muted small">{{ $purchase->id }}</td>
                    <td>{{ $purchase->purchased_at?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $purchase->supplier?->name ?? '—' }}</td>
                    <td class="fw-semibold">
                        {{ config('app.currency') }} {{ number_format($purchase->total, 2) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusColors[$purchase->status] }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td>
                    <td>{{ $purchase->createdBy?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($purchase->status === 'pending')
                        <a href="{{ route('purchases.edit', $purchase) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No purchases yet.
                        <a href="{{ route('purchases.create') }}">Create your first purchase</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection