@extends('layouts.app')
@section('title', 'Edit Salesperson')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Salesperson — {{ $salesperson->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('salespeople.update', $salesperson) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $salesperson->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone"
                               class="form-control"
                               value="{{ old('phone', $salesperson->phone) }}"
                               placeholder="For SMS due date notifications">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Commission Type</label>
                            <select name="commission_type" class="form-select">
                                <option value="value_based"
                                    {{ $salesperson->commission_type === 'value_based' ? 'selected' : '' }}>
                                    Value Based
                                </option>
                                <option value="qty_based"
                                    {{ $salesperson->commission_type === 'qty_based' ? 'selected' : '' }}>
                                    Quantity Based
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Target Period</label>
                            <select name="target_period" class="form-select">
                                <option value="monthly"
                                    {{ $salesperson->target_period === 'monthly' ? 'selected' : '' }}>
                                    Monthly
                                </option>
                                <option value="quarterly"
                                    {{ $salesperson->target_period === 'quarterly' ? 'selected' : '' }}>
                                    Quarterly
                                </option>
                                <option value="yearly"
                                    {{ $salesperson->target_period === 'yearly' ? 'selected' : '' }}>
                                    Yearly
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="is_active" value="1" id="is_active"
                                   {{ $salesperson->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">
                                Active
                            </label>
                        </div>
                        <div class="form-text text-muted">
                            Inactive salespeople are hidden from bill creation but their history is preserved.
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                        <a href="{{ route('salespeople.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection