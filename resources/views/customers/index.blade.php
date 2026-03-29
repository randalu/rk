@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Customers</span>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Customer
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Default Term</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="text-muted small">{{ $customer->id }}</td>
                    <td class="fw-semibold">{{ $customer->name }}</td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td>{{ $customer->email ?? '—' }}</td>
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
                        <span class="badge bg-{{ $termColors[$customer->default_payment_term] }}">
                            {{ $termLabels[$customer->default_payment_term] }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('customers.show', $customer) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('customers.destroy', $customer) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this customer?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No customers yet.
                        <a href="{{ route('customers.create') }}">Add your first customer</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection