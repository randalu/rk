@extends('layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
            <div class="small mb-1 opacity-75">This Month</div>
            <div class="fs-4 fw-bold">
                {{ config('app.currency') }} {{ number_format($totalThisMonth, 2) }}
            </div>
            <div class="small opacity-75 mt-1">
                <i class="bi bi-calendar"></i> {{ now()->format('F Y') }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-wallet2 me-2"></i>Expenses</span>
        <div class="d-flex gap-2">
            <a href="{{ route('expense-categories.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Expense
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Recorded By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td class="text-muted small">{{ $expense->id }}</td>
                    <td>{{ $expense->expense_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $expense->category?->name ?? '—' }}
                        </span>
                    </td>
                    <td>{{ $expense->description ?? '—' }}</td>
                    <td class="fw-semibold text-danger">
                        {{ config('app.currency') }} {{ number_format($expense->amount, 2) }}
                    </td>
                    <td>{{ $expense->createdBy?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('expenses.edit', $expense) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('expenses.destroy', $expense) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this expense?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No expenses yet.
                        <a href="{{ route('expenses.create') }}">Add your first expense</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="card-footer">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection