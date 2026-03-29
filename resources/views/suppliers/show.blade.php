@extends('layouts.app')
@section('title', $supplier->name)

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building me-2"></i>Supplier Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Company</dt>
                    <dd class="col-7 fw-semibold">{{ $supplier->name }}</dd>

                    <dt class="col-5 text-muted small">Contact</dt>
                    <dd class="col-7">{{ $supplier->contact ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Phone</dt>
                    <dd class="col-7">{{ $supplier->phone ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Email</dt>
                    <dd class="col-7">{{ $supplier->email ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Address</dt>
                    <dd class="col-7">{{ $supplier->address ?? '—' }}</dd>

                    <dt class="col-5 text-muted small">Since</dt>
                    <dd class="col-7">{{ $supplier->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-truck me-2"></i>Recent Purchases
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->purchases as $purchase)
                        <tr>
                            <td class="text-muted small">{{ $purchase->id }}</td>
                            <td>{{ $purchase->purchased_at?->format('d M Y') ?? '—' }}</td>
                            <td>{{ config('app.currency') }} {{ number_format($purchase->total, 2) }}</td>
                            <td>
                                @php $colors = ['pending'=>'warning','received'=>'success','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $colors[$purchase->status] }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No purchases yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection