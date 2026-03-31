@extends('layouts.app')
@section('title', 'New Return')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-arrow-return-left me-2"></i>New Return Request
    </div>
    <div class="card-body">
        <form action="{{ route('returns.store') }}" method="POST" id="return-form">
            @csrf

            <div class="row mb-4">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        Select Bill <span class="text-danger">*</span>
                    </label>
                    <select name="bill_id" id="bill_select"
                            class="form-select @error('bill_id') is-invalid @enderror"
                            required>
                        <option value="">— Select a Bill —</option>
                        @foreach($bills as $bill)
                        <option value="{{ $bill->id }}"
                            {{ old('bill_id') == $bill->id ? 'selected' : '' }}>
                            #{{ $bill->id }} —
                            {{ $bill->customer?->name ?? 'No customer' }} —
                            {{ $bill->created_at->format('d M Y') }} —
                            {{ config('app.currency') }} {{ number_format($bill->total, 2) }}
                        </option>
                        @endforeach
                    </select>
                    @error('bill_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Reason</label>
                    <input type="text" name="reason"
                           class="form-control"
                           value="{{ old('reason') }}"
                           placeholder="e.g. Damaged product, wrong item...">
                </div>
            </div>

            {{-- Bill items load here --}}
            <div id="items-section" style="display:none">
                <h6 class="fw-semibold mb-3 border-top pt-3">Select Items to Return</h6>
                <div class="alert alert-info small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Only tick the items being returned and enter the quantity.
                    Return value is calculated at original sale price.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">Return</th>
                                <th>Product</th>
                                <th>Batch</th>
                                <th>Sold Qty</th>
                                <th>Unit Price</th>
                                <th style="width:100px">Return Qty</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Select a bill above to load items.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="6" class="text-end fw-bold">Return Total:</td>
                                <td class="fw-bold" id="return-total">
                                    {{ config('app.currency') }} 0.00
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                    <i class="bi bi-check-lg me-1"></i> Submit Return
                </button>
                <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const currency  = '{{ config('app.currency') }}';
const baseUrl   = '{{ url('returns/bill') }}';

document.getElementById('bill_select').addEventListener('change', function () {
    const billId = this.value;
    if (!billId) {
        document.getElementById('items-section').style.display = 'none';
        document.getElementById('submit-btn').disabled = true;
        return;
    }

    fetch(`${baseUrl}/${billId}/items`)
        .then(r => r.json())
        .then(items => {
            const tbody = document.getElementById('items-body');
            tbody.innerHTML = '';

            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No items on this bill.</td></tr>';
                return;
            }

            items.forEach((item, i) => {
                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.dataset.price = item.unit_price;
                tr.dataset.maxQty = item.qty;
                tr.innerHTML = `
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input item-check"
                               name="items[${i}][bill_item_id]"
                               value="${item.id}"
                               data-index="${i}">
                    </td>
                    <td class="fw-semibold">${item.inventory ? item.inventory.name : '—'}</td>
                    <td><code>${item.batch_number}</code></td>
                    <td>${item.qty}</td>
                    <td>${currency} ${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td>
                        <input type="number"
                               name="items[${i}][qty]"
                               class="form-control form-control-sm qty-input"
                               value="1"
                               min="1"
                               max="${item.qty}"
                               disabled>
                    </td>
                    <td class="line-total fw-semibold">—</td>
                `;
                tbody.appendChild(tr);

                // Bind checkbox
                const checkbox = tr.querySelector('.item-check');
                const qtyInput = tr.querySelector('.qty-input');

                checkbox.addEventListener('change', function () {
                    qtyInput.disabled = !this.checked;
                    if (!this.checked) {
                        tr.querySelector('.line-total').textContent = '—';
                    }
                    updateTotal();
                });

                qtyInput.addEventListener('input', updateTotal);
            });

            document.getElementById('items-section').style.display = 'block';
            document.getElementById('submit-btn').disabled = false;
            updateTotal();
        });
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const checkbox = row.querySelector('.item-check');
        const qty      = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price    = parseFloat(row.dataset.price) || 0;

        if (checkbox.checked) {
            const line = qty * price;
            row.querySelector('.line-total').textContent = currency + ' ' + line.toFixed(2);
            total += line;
        }
    });
    document.getElementById('return-total').textContent = currency + ' ' + total.toFixed(2);
}
</script>
@endsection