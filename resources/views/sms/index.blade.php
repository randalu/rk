@extends('layouts.app')
@section('title', 'SMS Recipients')

@section('content')
<div class="row g-4">

    {{-- Add Recipient --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-phone-fill me-2"></i>Add Recipient
            </div>
            <div class="card-body">
                <form action="{{ route('sms-recipients.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g. Warehouse Manager">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Phone <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}"
                               placeholder="e.g. +94771234567">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2">Notify For</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox"
                                   name="notify_low_stock" value="1"
                                   id="low_stock"
                                   {{ old('notify_low_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="low_stock">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                Low stock alerts
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="notify_due_payments" value="1"
                                   id="due_payments"
                                   {{ old('notify_due_payments') ? 'checked' : '' }}>
                            <label class="form-check-label" for="due_payments">
                                <i class="bi bi-calendar-event text-info me-1"></i>
                                Upcoming payment due dates
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> Add Recipient
                    </button>
                </form>
            </div>
        </div>

        {{-- SMS Info --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>SMS Notifications
            </div>
            <div class="card-body">
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <span class="badge bg-success me-2">Customer</span>
                        Receives SMS when a bill is created
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-primary me-2">Customer</span>
                        Receives reminder 7 days before due date
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-info me-2">Salesperson</span>
                        Receives due date reminders for their bills
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-warning text-dark me-2">Admin</span>
                        Low stock alerts when qty drops below threshold
                    </li>
                    <li>
                        <span class="badge bg-warning text-dark me-2">Admin</span>
                        Upcoming payment collection reminders
                    </li>
                </ul>
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    To activate SMS sending, configure your SMS API
                    provider credentials in <code>.env</code>.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Recipients List --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Admin Recipients
                <span class="badge bg-secondary ms-2">{{ $recipients->count() }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Low Stock</th>
                            <th>Due Payments</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $recipient)
                        <tr>
                            <td class="fw-semibold">{{ $recipient->name }}</td>
                            <td><code>{{ $recipient->phone }}</code></td>
                            <td>
                                @if($recipient->notify_low_stock)
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-muted"></i>
                                @endif
                            </td>
                            <td>
                                @if($recipient->notify_due_payments)
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-muted"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $recipient->is_active ? 'success' : 'secondary' }}">
                                    {{ $recipient->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editRecipient{{ $recipient->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('sms-recipients.destroy', $recipient) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this recipient?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editRecipient{{ $recipient->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Recipient</h5>
                                        <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('sms-recipients.update', $recipient) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Name</label>
                                                <input type="text" name="name"
                                                       class="form-control"
                                                       value="{{ $recipient->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Phone</label>
                                                <input type="text" name="phone"
                                                       class="form-control"
                                                       value="{{ $recipient->phone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold mb-2">Notify For</label>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="notify_low_stock" value="1"
                                                           {{ $recipient->notify_low_stock ? 'checked' : '' }}>
                                                    <label class="form-check-label">Low stock alerts</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="notify_due_payments" value="1"
                                                           {{ $recipient->notify_due_payments ? 'checked' : '' }}>
                                                    <label class="form-check-label">Due payment alerts</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="is_active" value="1"
                                                           {{ $recipient->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No recipients added yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SMS Log --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Recent SMS Log
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>To</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                        @php
                            $typeColors = [
                                'new_bill'           => 'primary',
                                'due_reminder'       => 'warning text-dark',
                                'low_stock'          => 'danger',
                                'admin_notification' => 'secondary',
                            ];
                            $typeLabels = [
                                'new_bill'           => 'New Bill',
                                'due_reminder'       => 'Due Reminder',
                                'low_stock'          => 'Low Stock',
                                'admin_notification' => 'Admin Alert',
                            ];
                        @endphp
                        <tr>
                            <td class="text-muted small">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>
                            <td><code>{{ $log->recipient_phone }}</code></td>
                            <td>
                                <span class="badge bg-{{ $typeColors[$log->sms_type] ?? 'secondary' }}">
                                    {{ $typeLabels[$log->sms_type] ?? $log->sms_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->status === 'sent' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning text-dark') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                No SMS sent yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection