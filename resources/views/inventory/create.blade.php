@extends('layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-seam me-2"></i>Add Product to Inventory
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-1"></i>
                    This creates a <strong>product record</strong>. Batch numbers and expiry dates
                    are assigned automatically when a <strong>purchase is received</strong>.
                </div>

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Paracetamol 500mg">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku"
                                   class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku') }}"
                                   placeholder="e.g. PCM-500">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Opening Qty <span class="text-danger">*</span></label>
                            <input type="number" name="qty"
                                   class="form-control @error('qty') is-invalid @enderror"
                                   value="{{ old('qty', 0) }}" min="0">
                            @error('qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Low Stock Alert At <span class="text-danger">*</span></label>
                            <input type="number" name="low_stock_threshold"
                                   class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                   value="{{ old('low_stock_threshold', 10) }}" min="1">
                            @error('low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Supplier</label>
                            <select name="supplier_id"
                                    class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">— None —</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Selling Price ({{ config('app.currency') }}) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="price" step="0.01"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', '0.00') }}" min="0">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Cost Price ({{ config('app.currency') }}) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="cost" step="0.01"
                                   class="form-control @error('cost') is-invalid @enderror"
                                   value="{{ old('cost', '0.00') }}" min="0">
                            @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Product
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection