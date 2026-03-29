@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Product — {{ $inventory->name }}
            </div>
            <div class="card-body">

                @if($inventory->batch_number)
                <div class="alert alert-warning small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    This is a <strong>batch record</strong> created from a purchase.
                    Edit with caution — qty and cost changes will affect stock accuracy.
                </div>
                @endif

                <form action="{{ route('inventory.update', $inventory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $inventory->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku"
                                   class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku', $inventory->sku) }}">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty"
                                   class="form-control @error('qty') is-invalid @enderror"
                                   value="{{ old('qty', $inventory->qty) }}" min="0">
                            @error('qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Low Stock Alert At <span class="text-danger">*</span></label>
                            <input type="number" name="low_stock_threshold"
                                   class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                   value="{{ old('low_stock_threshold', $inventory->low_stock_threshold) }}" min="1">
                            @error('low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Supplier</label>
                            <select name="supplier_id"
                                    class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">— None —</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $inventory->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                                   value="{{ old('price', $inventory->price) }}" min="0">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Cost Price ({{ config('app.currency') }}) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="cost" step="0.01"
                                   class="form-control @error('cost') is-invalid @enderror"
                                   value="{{ old('cost', $inventory->cost) }}" min="0">
                            @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Product
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