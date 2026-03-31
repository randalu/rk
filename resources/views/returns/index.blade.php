@extends('layouts.app')
@section('title', 'Returns')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-arrow-return-left me-2"></i>Returns</span>
        <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Return
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Bill #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Approved By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                @php
                    $statusColors = [
                        'pending'  => 'warning text-dark',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    ];
                @endphp
                <tr>
                    <td class="text-muted small">{{ $return->id }}</td>
                    <td>{{ $return->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('bills.show', $return->bill_id) }}"
                           class="text-decoration-none fw-semibold">
                            #{{ $return->bill_id }}
                        </a>
                    </td>
                    <td>{{ $return->bill?->customer?->name ?? '—' }}</td>
                    <td class="fw-semibold">
                        {{ config('app.currency') }} {{ number_format($return->total, 2) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusColors[$return->status] }}">
                            {{ ucfirst($return->status) }}
                        </span>
                    </td>
                    <td>{{ $return->approvedBy?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('returns.show', $return) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($return->status === 'pending')
                        <form action="{{ route('returns.destroy', $return) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this return?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No returns yet.
                        <a href="{{ route('returns.create') }}">Create your first return</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())
    <div class="card-footer">{{ $returns->links() }}</div>
    @endif
</div>
@endsection