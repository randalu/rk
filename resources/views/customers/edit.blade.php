@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Customer — {{ $customer->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $customer->name) }}">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $customer->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $customer->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" rows="2"
                                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $customer->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Default Payment Term <span class="text-danger">*</span></label>
                        <select name="default_payment_term"
                                class="form-select @error('default_payment_term') is-invalid @enderror">
                            <option value="cash"      {{ old('default_payment_term', $customer->default_payment_term) == 'cash'      ? 'selected' : '' }}>Cash</option>
                            <option value="credit_30" {{ old('default_payment_term', $customer->default_payment_term) == 'credit_30' ? 'selected' : '' }}>Credit 30 Days</option>
                            <option value="credit_45" {{ old('default_payment_term', $customer->default_payment_term) == 'credit_45' ? 'selected' : '' }}>Credit 45 Days</option>
                            <option value="credit_60" {{ old('default_payment_term', $customer->default_payment_term) == 'credit_60' ? 'selected' : '' }}>Credit 60 Days</option>
                        </select>
                        @error('default_payment_term')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Customer
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection