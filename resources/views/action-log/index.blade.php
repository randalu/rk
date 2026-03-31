@extends('layouts.app')
@section('title', 'Action Log')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Module</label>
                <select name="model" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach($models as $model)
                    <option value="{{ $model }}"
                        {{ request('model') == $model ? 'selected' : '' }}>
                        {{ $model }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    <option value="created"  {{ request('action') == 'created'  ? 'selected' : '' }}>Created</option>
                    <option value="updated"  {{ request('action') == 'updated'  ? 'selected' : '' }}>Updated</option>
                    <option value="deleted"  {{ request('action') == 'deleted'  ? 'selected' : '' }}>Deleted</option>
                    <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['model','user_id','action']))
            <div class="col-md-1">
                <a href="{{ route('action-log.index') }}"
                   class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history me-2"></i>Action Log
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Record</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $actionColors = [
                        'created'  => 'success',
                        'updated'  => 'primary',
                        'deleted'  => 'danger',
                        'approved' => 'info',
                        'rejected' => 'warning',
                        'released' => 'success',
                    ];
                    $color = $actionColors[$log->action] ?? 'secondary';
                @endphp
                <tr>
                    <td class="text-muted small">
                        {{ $log->created_at?->format('d M Y H:i') ?? '—' }}
                    </td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td>
                        <span class="badge bg-{{ $color }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td><code>{{ $log->model }}</code></td>
                    <td class="text-muted small">#{{ $log->record_id }}</td>
                    <td class="small">{{ $log->description ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection