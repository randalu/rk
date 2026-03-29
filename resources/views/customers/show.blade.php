@extends('layouts.app')
@section('title', $customer->name)

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Customer Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Name</dt>
                    <dd class="col-7 fw-semibold">{{ $customer->name }}</dd>

                    <dt class="col-5 text-muted small">Phone</dt>
                    <dd class="col-7">{{ $customer->phone ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Email</dt>
                    <dd class="col-7">{{ $customer->email ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Address</dt>
                    <dd class="col-7">{{ $customer->address ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Default Term</dt>
                    <dd class="col-7">
                        <span class="badge bg-primary">
                            {{ str_replace('_', ' ', strtoupper($customer->default_payment_term)) }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted small">Since</dt>
                    <dd class="col-7">{{ $customer->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt me-2"></i>Recent Bills
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Term</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->bills as $bill)
                        <tr>
                            <td class="text-muted small">{{ $bill->id }}</td>
                            <td>{{ $bill->created_at->format('d M Y') }}</td>
                            <td>{{ config('app.currency') }} {{ number_format($bill->total, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ str_replace('_', ' ', $bill->payment_term) }}</span></td>
                            <td>{{ $bill->due_date?->format('d M Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No bills yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection