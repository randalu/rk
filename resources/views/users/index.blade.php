@extends('layouts.app')
@section('title', 'Users')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-gear me-2"></i>Users</span>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New User
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Salesperson</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $roleColors = [
                        'admin'    => 'purple',
                        'manager'  => 'primary',
                        'cashier'  => 'info',
                        'salesman' => 'success',
                    ];
                    $color = $roleColors[$user->role?->name] ?? 'secondary';
                @endphp
                <tr>
                    <td class="text-muted small">{{ $user->id }}</td>
                    <td class="fw-semibold">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="badge bg-secondary ms-1">You</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-{{ $color }}">
                            {{ $user->role?->name ?? 'No role' }}
                        </span>
                    </td>
                    <td>
                        @if($user->salesperson)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ $user->salesperson->name }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this user?')">
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
                    <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection