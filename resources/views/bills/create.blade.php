@extends('layouts.app')
@section('title', 'New Bill')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-receipt me-2"></i>New Bill
    </div>
    <div class="card-body">
        <form action="{{ route('bills.store') }}" method="POST" id="bill-form">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Customer <span class="text-danger">*</span>
                    </label>
                    <select name="customer_id" id="customer_id"
                            class="form-select @error('customer_id') is-invalid @enderror"
                            required>
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                                data-term="{{ $customer->default_payment_term }}"
                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                            @if($customer->phone) ({{ $customer->phone }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Salesperson <span class="text-danger">*</span>
                    </label>
                    <select name="salesperson_id"
                            class="form-select @error('salesperson_id') is-invalid @enderror"
                            required>
                        <option value="">— Select Salesperson —</option>
                        @foreach($salespeople as $sp)
                        <option value="{{ $sp->id }}"
                            {{ old('salesperson_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }}
                            @if(!$sp->user_id) (Direct) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('salesperson_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Payment Type <span class="text-danger">*</span>
                    </label>
                    <select name="payment_type" class="form-select" required>
                        <option value="cash"   {{ old('payment_type') == 'cash'   ? 'selected' : '' }}>Cash</option>
                        <option value="card"   {{ old('payment_type') == 'card'   ? 'selected' : '' }}>Card</option>
                        <option value="online" {{ old('payment_type') == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Payment Term <span class="text-danger">*</span>
                    </label>
                    <select name="payment_term" id="payment_term" class="form-select" required>
                        <option value="cash"      {{ old('payment_term') == 'cash'      ? 'selected' : '' }}>Cash</option>
                        <option value="credit_30" {{ old('payment_term') == 'credit_30' ? 'selected' : '' }}>Credit 30 Days</option>
                        <option value="credit_45" {{ old('payment_term') == 'credit_45' ? 'selected' : '' }}>Credit 45 Days</option>
                        <option value="credit_60" {{ old('payment_term') == 'credit_60' ? 'selected' : '' }}>Credit 60 Days</option>
                    </select>
                    <div class="form-text text-muted">
                        Auto-filled from customer default. Can be overridden.
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Advance Payment ({{ config('app.currency') }})
                    </label>
                    <input type="number" name="advance_payment"
                           class="form-control" value="{{ old('advance_payment', '0.00') }}"
                           step="0.01" min="0">
                </div>
            </div>

            <h6 class="fw-semibold mb-3 border-top pt-3">Bill Items</h6>

            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:220px">Product / Batch</th>
                            <th style="min-width:60px">Available</th>
                            <th style="min-width:80px">Qty</th>
                            <th style="min-width:130px">Unit Price ({{ config('app.currency') }})</th>
                            <th style="min-width:110px">Line Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <tr class="item-row">
                            <td>
                                <select name="items[0][inventory_id]"
                                        class="form-select form-select-sm inv-select" required>
                                    <option value="">— Select Product/Batch —</option>
                                    @foreach($inventory->groupBy('name') as $name => $batches)
                                    <optgroup label="{{ $name }}">
                                        @foreach($batches as $item)
                                        <option value="{{ $item->id }}"
                                                data-price="{{ $item->price }}"
                                                data-qty="{{ $item->qty }}">
                                            {{ $item->batch_number ?? 'No batch' }}
                                            — Exp: {{ $item->expiry_date?->format('M Y') ?? 'N/A' }}
                                            ({{ $item->qty }} available)
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </td>
                            <td class="avail-qty align-middle text-muted small">—</td>
                            <td>
                                <input type="number" name="items[0][qty]"
                                       class="form-control form-control-sm qty-input"
                                       value="1" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][unit_price]"
                                       class="form-control form-control-sm price-input"
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
                            <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                            <td colspan="2" class="fw-bold fs-6" id="grand-total">
                                {{ config('app.currency') }} 0.00
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="add-row">
                <i class="bi bi-plus-lg me-1"></i> Add Item
            </button>

            <div class="d-flex gap-2 border-top pt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Create Bill
                </button>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = 1;
const currency = '{{ config('app.currency') }}';

// Build inventory options grouped by product name
const inventoryOptions = `
    @foreach($inventory->groupBy('name') as $name => $batches)
    <optgroup label="{{ $name }}">
        @foreach($batches as $item)
        <option value="{{ $item->id }}"
                data-price="{{ $item->price }}"
                data-qty="{{ $item->qty }}">
            {{ $item->batch_number ?? 'No batch' }} — Exp: {{ $item->expiry_date?->format('M Y') ?? 'N/A' }} ({{ $item->qty }} available)
        </option>
        @endforeach
    </optgroup>
    @endforeach
`;

// Auto-fill payment term when customer is selected
document.getElementById('customer_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const term = selected.getAttribute('data-term');
    if (term) {
        document.getElementById('payment_term').value = term;
    }
});

function updateLineTotals() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const line  = qty * price;
        row.querySelector('.line-total').textContent = line.toFixed(2);
        grand += line;
    });
    document.getElementById('grand-total').textContent = currency + ' ' + grand.toFixed(2);
}

function bindSelectEvents(select, row) {
    select.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const price = opt.getAttribute('data-price') || '0';
        const qty   = opt.getAttribute('data-qty') || '—';
        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        row.querySelector('.avail-qty').textContent = qty + ' units';
        updateLineTotals();
    });
}

function buildRow(index) {
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td>
            <select name="items[${index}][inventory_id]" class="form-select form-select-sm inv-select" required>
                <option value="">— Select Product/Batch —</option>
                ${inventoryOptions}
            </select>
        </td>
        <td class="avail-qty align-middle text-muted small">—</td>
        <td><input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input" value="1" min="1" required></td>
        <td><input type="number" name="items[${index}][unit_price]" class="form-control form-control-sm price-input" value="0.00" step="0.01" min="0" required></td>
        <td class="line-total fw-semibold align-middle">0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
    `;
    return tr;
}

function bindRowEvents(row) {
    const select = row.querySelector('.inv-select');
    bindSelectEvents(select, row);
    row.querySelector('.qty-input').addEventListener('input', updateLineTotals);
    row.querySelector('.price-input').addEventListener('input', updateLineTotals);
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