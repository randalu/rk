@extends('layouts.app')
@section('title', 'Bill #' . $bill->id)

@section('content')
@php
    $paid    = $bill->payments->sum('amount');
    $balance = $bill->total - $paid;
    $isFullyPaid = $balance <= 0;
    $termLabels = [
        'cash'      => 'Cash',
        'credit_30' => 'Credit 30 Days',
        'credit_45' => 'Credit 45 Days',
        'credit_60' => 'Credit 60 Days',
    ];
@endphp

<div class="row g-4">

    {{-- Bill Summary --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-list-ul me-2"></i>Items</span>
    <div class="d-flex gap-2">
        <a href="{{ route('bills.print', $bill) }}" target="_blank"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Print
        </a>
        <a href="{{ route('bills.pdf', $bill) }}"
           class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        @if($bill->payments->count() === 0)
        <a href="{{ route('bills.edit', $bill) }}"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Bill
        </a>
        @endif
    </div>
</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Customer</dt>
                    <dd class="col-7 fw-semibold">{{ $bill->customer?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Phone</dt>
                    <dd class="col-7">{{ $bill->customer?->phone ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Salesperson</dt>
                    <dd class="col-7">{{ $bill->salesperson?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Created By</dt>
                    <dd class="col-7">{{ $bill->createdBy?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Bill Date</dt>
                    <dd class="col-7">{{ $bill->created_at->format('d M Y H:i') }}</dd>

                    <dt class="col-5 text-muted small">Payment Type</dt>
                    <dd class="col-7">{{ ucfirst($bill->payment_type) }}</dd>

                    <dt class="col-5 text-muted small">Payment Term</dt>
                    <dd class="col-7">{{ $termLabels[$bill->payment_term] }}</dd>

                    <dt class="col-5 text-muted small">Due Date</dt>
                    <dd class="col-7 {{ $bill->due_date?->isPast() && !$isFullyPaid ? 'text-danger fw-bold' : '' }}">
                        {{ $bill->due_date?->format('d M Y') ?? '—' }}
                    </dd>

                    <dt class="col-5 text-muted small">Bill Total</dt>
                    <dd class="col-7 fw-bold fs-6">
                        {{ config('app.currency') }} {{ number_format($bill->total, 2) }}
                    </dd>

                    <dt class="col-5 text-muted small">Advance</dt>
                    <dd class="col-7">
                        {{ config('app.currency') }} {{ number_format($bill->advance_payment, 2) }}
                    </dd>

                    <dt class="col-5 text-muted small">Total Paid</dt>
                    <dd class="col-7 text-success fw-bold">
                        {{ config('app.currency') }} {{ number_format($paid, 2) }}
                    </dd>

                    <dt class="col-5 text-muted small">Balance</dt>
                    <dd class="col-7 {{ $balance > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                        {{ config('app.currency') }} {{ number_format($balance, 2) }}
                    </dd>
                </dl>
            </div>
            @if(!$isFullyPaid)
            <div class="card-footer">
                <button class="btn btn-success btn-sm w-100"
                        data-bs-toggle="collapse"
                        data-bs-target="#payment-form">
                    <i class="bi bi-cash me-1"></i> Record Payment
                </button>
                <div class="collapse mt-3" id="payment-form">
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="bill_id" value="{{ $bill->id }}">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Amount</label>
                            <input type="number" name="amount"
                                   class="form-control form-control-sm"
                                   value="{{ number_format($balance, 2, '.', '') }}"
                                   step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Payment Type</label>
                            <select name="payment_type" class="form-select form-select-sm">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Notes</label>
                            <input type="text" name="notes"
                                   class="form-control form-control-sm"
                                   placeholder="Optional notes">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-check-lg me-1"></i> Save Payment
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- Commission summary --}}
        @if($bill->commission)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up-arrow me-2"></i>Commission
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-6 text-muted small">Rate</dt>
                    <dd class="col-6">{{ $bill->commission->commission_rate }}%</dd>

                    <dt class="col-6 text-muted small">Gross</dt>
                    <dd class="col-6">
                        {{ config('app.currency') }} {{ number_format($bill->commission->commission_amount, 2) }}
                    </dd>

                    <dt class="col-6 text-muted small">Deductions</dt>
                    <dd class="col-6 text-danger">
                        - {{ config('app.currency') }} {{ number_format($bill->commission->deducted_returns, 2) }}
                    </dd>

                    <dt class="col-6 text-muted small">Net</dt>
                    <dd class="col-6 fw-bold">
                        {{ config('app.currency') }} {{ number_format($bill->commission->net_commission, 2) }}
                    </dd>

                    <dt class="col-6 text-muted small">Status</dt>
                    <dd class="col-6">
                        @php
                            $commColors = [
                                'pending'   => 'warning text-dark',
                                'payable'   => 'info',
                                'paid'      => 'success',
                                'cancelled' => 'secondary',
                            ];
                        @endphp
                        <span class="badge bg-{{ $commColors[$bill->commission->status] }}">
                            {{ ucfirst($bill->commission->status) }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        {{-- Bill Items --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2"></i>Items</span>
                @if($bill->payments->count() === 0)
                <a href="{{ route('bills.edit', $bill) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit Bill
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill->items as $item)
                        <tr>
                            <td>{{ $item->inventory?->name ?? '—' }}</td>
                            <td><code>{{ $item->batch_number }}</code></td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ config('app.currency') }} {{ number_format($item->unit_price, 2) }}</td>
                            <td class="fw-semibold">
                                {{ config('app.currency') }} {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td class="fw-bold">
                                {{ config('app.currency') }} {{ number_format($bill->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-stack me-2"></i>Payment History
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Received By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bill->payments as $payment)
                        <tr>
                            <td class="text-muted small">{{ $payment->id }}</td>
                            <td>{{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y H:i') }}</td>
                            <td class="fw-semibold text-success">
                                {{ config('app.currency') }} {{ number_format($payment->amount, 2) }}
                            </td>
                            <td>{{ ucfirst($payment->payment_type) }}</td>
                            <td>{{ $payment->receivedBy?->name ?? '—' }}</td>
                            <td class="text-muted">{{ $payment->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No payments recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($bill->payments->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Total Paid</td>
                            <td class="fw-bold text-success">
                                {{ config('app.currency') }} {{ number_format($paid, 2) }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection