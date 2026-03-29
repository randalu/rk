@extends('layouts.app')
@section('title', 'Edit Purchase #' . $purchase->id)

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Edit Purchase #{{ $purchase->id }}
    </div>
    <div class="card-body">
        <form action="{{ route('purchases.update', $purchase) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">— No Supplier —</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Purchase Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="purchased_at" class="form-control"
                           value="{{ $purchase->purchased_at?->format('Y-m-d') }}">
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
                        @foreach($purchase->items as $i => $item)
                        <tr class="item-row">
                            <td>
                                <select name="items[{{ $i }}][inventory_id]"
                                        class="form-select form-select-sm" required>
                                    <option value="">— Select —</option>
                                    @foreach($inventory as $inv)
                                    <option value="{{ $inv->id }}"
                                        {{ $item->inventory_id == $inv->id ? 'selected' : '' }}>
                                        {{ $inv->name }} ({{ $inv->sku }})
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][batch_number]"
                                       class="form-control form-control-sm"
                                       value="{{ $item->batch_number }}" required>
                            </td>
                            <td>
                                <input type="date" name="items[{{ $i }}][expiry_date]"
                                       class="form-control form-control-sm"
                                       value="{{ $item->expiry_date?->format('Y-m-d') }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][qty]"
                                       class="form-control form-control-sm qty-input"
                                       value="{{ $item->qty }}" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][unit_cost]"
                                       class="form-control form-control-sm cost-input"
                                       value="{{ $item->unit_cost }}"
                                       step="0.01" min="0" required>
                            </td>
                            <td class="line-total fw-semibold align-middle">
                                {{ number_format($item->line_total, 2) }}
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-row">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end fw-bold">Grand Total:</td>
                            <td colspan="2" class="fw-bold" id="grand-total">
                                {{ config('app.currency') }} {{ number_format($purchase->total, 2) }}
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
                    <i class="bi bi-check-lg me-1"></i> Update Purchase
                </button>
                <a href="{{ route('purchases.show', $purchase) }}"
                   class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = {{ $purchase->items->count() }};
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
updateLineTotals();
</script>
@endsection