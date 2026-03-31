@extends('layouts.app')
@section('title', 'Edit Expense')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Expense
            </div>
            <div class="card-body">
                <form action="{{ route('expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Category <span class="text-danger">*</span>
                        </label>
                        <select name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Amount ({{ config('app.currency') }}) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="amount" step="0.01"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $expense->amount) }}"
                               min="0.01" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Expense Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="expense_date"
                               class="form-control @error('expense_date') is-invalid @enderror"
                               value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                               required>
                        @error('expense_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="2"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $expense->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Expense
                        </button>
                        <a href="{{ route('expenses.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection