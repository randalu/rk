@extends('layouts.app')
@section('title', 'Bills')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>Bills</span>
        <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Bill
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Salesperson</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Term</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                @php
                    $paid    = $bill->payments->sum('amount');
                    $balance = $bill->total - $paid;
                    $isOverdue = $balance > 0 && $bill->due_date && $bill->due_date->isPast();
                @endphp
                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                    <td class="text-muted small">{{ $bill->id }}</td>
                    <td>{{ $bill->created_at->format('d M Y') }}</td>
                    <td class="fw-semibold">{{ $bill->customer?->name ?? '—' }}</td>
                    <td>{{ $bill->salesperson?->name ?? '—' }}</td>
                    <td class="fw-semibold">
                        {{ config('app.currency') }} {{ number_format($bill->total, 2) }}
                    </td>
                    <td class="text-success">
                        {{ config('app.currency') }} {{ number_format($paid, 2) }}
                    </td>
                    <td class="{{ $balance > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                        {{ config('app.currency') }} {{ number_format($balance, 2) }}
                    </td>
                    <td>
                        @php
                            $termColors = [
                                'cash'      => 'success',
                                'credit_30' => 'info',
                                'credit_45' => 'warning',
                                'credit_60' => 'danger',
                            ];
                            $termLabels = [
                                'cash'      => 'Cash',
                                'credit_30' => 'Credit 30',
                                'credit_45' => 'Credit 45',
                                'credit_60' => 'Credit 60',
                            ];
                        @endphp
                        <span class="badge bg-{{ $termColors[$bill->payment_term] }}">
                            {{ $termLabels[$bill->payment_term] }}
                        </span>
                    </td>
                    <td>
                        @if($bill->due_date)
                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                {{ $bill->due_date->format('d M Y') }}
                                @if($isOverdue)
                                    <i class="bi bi-exclamation-circle"></i>
                                @endif
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('bills.show', $bill) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($bill->payments->count() === 0)
                        <a href="{{ route('bills.edit', $bill) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No bills yet.
                        <a href="{{ route('bills.create') }}">Create your first bill</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
    <div class="card-footer">{{ $bills->links() }}</div>
    @endif
</div>
@endsection