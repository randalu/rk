@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit User — {{ $user->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-semibold mb-3 text-muted">Account Details</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                New Password
                                <span class="text-muted small">(leave blank to keep current)</span>
                            </label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role_id"
                                class="form-select @error('role_id') is-invalid @enderror"
                                id="role_select" required>
                            <option value="">— Select Role —</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                    data-name="{{ $role->name }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Salesperson fields --}}
                    <div id="salesperson-fields"
                         style="display:{{ $user->salesperson ? 'block' : 'none' }}">
                        <hr>
                        <h6 class="fw-semibold mb-3 text-muted">Salesperson Details</h6>
                        <input type="hidden" name="is_salesperson" value="1">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Display Name</label>
                                <input type="text" name="salesperson_name"
                                       class="form-control"
                                       value="{{ old('salesperson_name', $user->salesperson?->name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="salesperson_phone"
                                       class="form-control"
                                       value="{{ old('salesperson_phone', $user->salesperson?->phone) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Commission Type</label>
                                <select name="commission_type" class="form-select">
                                    <option value="value_based"
                                        {{ ($user->salesperson?->commission_type ?? 'value_based') === 'value_based' ? 'selected' : '' }}>
                                        Value Based
                                    </option>
                                    <option value="qty_based"
                                        {{ ($user->salesperson?->commission_type) === 'qty_based' ? 'selected' : '' }}>
                                        Quantity Based
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Target Period</label>
                                <select name="target_period" class="form-select">
                                    <option value="monthly"
                                        {{ ($user->salesperson?->target_period ?? 'monthly') === 'monthly' ? 'selected' : '' }}>
                                        Monthly
                                    </option>
                                    <option value="quarterly"
                                        {{ ($user->salesperson?->target_period) === 'quarterly' ? 'selected' : '' }}>
                                        Quarterly
                                    </option>
                                    <option value="yearly"
                                        {{ ($user->salesperson?->target_period) === 'yearly' ? 'selected' : '' }}>
                                        Yearly
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role_select').addEventListener('change', function() {
    const roleName = this.options[this.selectedIndex].getAttribute('data-name');
    const spFields = document.getElementById('salesperson-fields');
    spFields.style.display = roleName === 'salesman' ? 'block' : 'none';
});
</script>
@endsection