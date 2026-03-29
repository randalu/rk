@extends('layouts.app')
@section('title', 'Payments')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-cash-stack me-2"></i>All Payments
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Bill #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Received By</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="text-muted small">{{ $payment->id }}</td>
                    <td>{{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('bills.show', $payment->bill_id) }}"
                           class="text-decoration-none fw-semibold">
                            #{{ $payment->bill_id }}
                        </a>
                    </td>
                    <td>{{ $payment->bill?->customer?->name ?? '—' }}</td>
                    <td class="fw-semibold text-success">
                        {{ config('app.currency') }} {{ number_format($payment->amount, 2) }}
                    </td>
                    <td>{{ ucfirst($payment->payment_type) }}</td>
                    <td>{{ $payment->receivedBy?->name ?? '—' }}</td>
                    <td class="text-muted">{{ $payment->notes ?? '—' }}</td>
                    <td>
                        <form action="{{ route('payments.destroy', $payment) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this payment?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        No payments recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
    @endif
</div>
@endsection