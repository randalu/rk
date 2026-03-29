@extends('layouts.app')
@section('title', 'New Purchase')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-truck me-2"></i>New Purchase Order
    </div>
    <div class="card-body">
        <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
            @csrf

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">— No Supplier —</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Purchase Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="purchased_at"
                           class="form-control @error('purchased_at') is-invalid @enderror"
                           value="{{ old('purchased_at', date('Y-m-d')) }}">
                    @error('purchased_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h6 class="fw-semibold mb-3">Purchase Items</h6>

            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:180px">Product</th>
                            <th style="min-width:130px">Batch Number</th>
                            <th style="min-width:140px">Expiry Date</th>
                            <th style="min-width:80px">Qty</th>
                            <th style="min-width:120px">Unit Cost ({{ config('app.currency') }})</th>
                            <th style="min-width:100px">Line Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <tr class="item-row">
                            <td>
                                <select name="items[0][inventory_id]"
                                        class="form-select form-select-sm" required>
                                    <option value="">— Select —</option>
                                    @foreach($inventory as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }} ({{ $item->sku }})
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[0][batch_number]"
                                       class="form-control form-control-sm"
                                       required placeholder="e.g. BT-2025-001">
                            </td>
                            <td>
                                <input type="date" name="items[0][expiry_date]"
                                       class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][qty]"
                                       class="form-control form-control-sm qty-input"
                                       value="1" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][unit_cost]"
                                       class="form-control form-control-sm cost-input"
                                       value="0.00" step="0.01" min="0" required>
                            </td>
                            <td class="line-total fw-semibold align-middle">0.00</td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-row">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end fw-bold">Grand Total:</td>
                            <td colspan="2" class="fw-bold" id="grand-total">
                                {{ config('app.currency') }} 0.00
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="add-row">
                <i class="bi bi-plus-lg me-1"></i> Add Item
            </button>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Purchase
                </button>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = 1;
const currency = '{{ config('app.currency') }}';
const inventoryOptions = `{!! $inventory->map(fn($i) => '<option value="'.$i->id.'">'.$i->name.' ('.$i->sku.')</option>')->join('') !!}`;

function updateLineTotals() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty  = parseFloat(row.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
        const line = qty * cost;
        row.querySelector('.line-total').textContent = line.toFixed(2);
        grand += line;
    });
    document.getElementById('grand-total').textContent = currency + ' ' + grand.toFixed(2);
}

function buildRow(index) {
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td>
            <select name="items[${index}][inventory_id]" class="form-select form-select-sm" required>
                <option value="">— Select —</option>${inventoryOptions}
            </select>
        </td>
        <td>
            <input type="text" name="items[${index}][batch_number]"
                   class="form-control form-control-sm" required placeholder="e.g. BT-001">
        </td>
        <td>
            <input type="date" name="items[${index}][expiry_date]"
                   class="form-control form-control-sm" required>
        </td>
        <td>
            <input type="number" name="items[${index}][qty]"
                   class="form-control form-control-sm qty-input" value="1" min="1" required>
        </td>
        <td>
            <input type="number" name="items[${index}][unit_cost]"
                   class="form-control form-control-sm cost-input"
                   value="0.00" step="0.01" min="0" required>
        </td>
        <td class="line-total fw-semibold align-middle">0.00</td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    return tr;
}

function bindRowEvents(row) {
    row.querySelector('.qty-input').addEventListener('input', updateLineTotals);
    row.querySelector('.cost-input').addEventListener('input', updateLineTotals);
    row.querySelector('.remove-row').addEventListener('click', () => {
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
            updateLineTotals();
        }
    });
}

document.getElementById('add-row').addEventListener('click', () => {
    const row = buildRow(rowIndex++);
    document.getElementById('items-body').appendChild(row);
    bindRowEvents(row);
});

document.querySelectorAll('.item-row').forEach(bindRowEvents);
</script>
@endsection