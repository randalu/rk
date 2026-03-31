@extends('layouts.app')
@section('title', 'Salespeople')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>Salespeople</span>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add via Users
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Linked User</th>
                    <th>Commission Type</th>
                    <th>Target Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salespeople as $sp)
                <tr>
                    <td class="text-muted small">{{ $sp->id }}</td>
                    <td class="fw-semibold">{{ $sp->name }}</td>
                    <td>{{ $sp->phone ?? '—' }}</td>
                    <td>{{ $sp->user?->name ?? '<span class="text-muted">Direct Sale</span>' }}</td>
                    <td>
                        <span class="badge bg-info text-dark">
                            {{ $sp->commission_type === 'value_based' ? 'Value Based' : 'Qty Based' }}
                        </span>
                    </td>
                    <td>{{ ucfirst($sp->target_period) }}</td>
                    <td>
                        <span class="badge bg-{{ $sp->is_active ? 'success' : 'secondary' }}">
                            {{ $sp->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        @if($sp->user_id)
                        <a href="{{ route('salespeople.edit', $sp) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('salespeople.destroy', $sp) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this salesperson?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @else
                        <span class="text-muted small">System record</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No salespeople yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($salespeople->hasPages())
    <div class="card-footer">{{ $salespeople->links() }}</div>
    @endif
</div>
@endsection