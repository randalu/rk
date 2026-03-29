<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $bill->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #fff;
            padding: 18px 22px;
        }

        .invoice-wrapper {
            max-width: 520px;
            margin: 0 auto;
        }

        .print-bar {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-bottom: 14px;
        }

        .btn-print {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back {
            background: #f3f4f6;
            color: #333;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
        }

        /* Header */
        .invoice-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4f46e5;
        }

        .company-name {
            font-size: 17px;
            font-weight: 700;
            color: #4f46e5;
        }

        .company-tagline {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }

        .invoice-label {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: 2px;
        }

        .invoice-number {
            font-size: 11px;
            color: #4f46e5;
            font-weight: 600;
            margin-top: 2px;
        }

        .invoice-date {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid    { background: #d1fae5; color: #065f46; }
        .status-unpaid  { background: #fef3c7; color: #92400e; }
        .status-overdue { background: #fee2e2; color: #991b1b; }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .items-table thead tr {
            background: #4f46e5;
            color: #fff;
        }

        .items-table thead th {
            padding: 6px 8px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        .items-table thead th.right { text-align: right; }

        .items-table tbody tr {
            border-bottom: 1px solid #f0f0f8;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafe;
        }

        .items-table tbody td {
            padding: 5px 8px;
            font-size: 10px;
            color: #333;
            vertical-align: middle;
        }

        .items-table tbody td.right { text-align: right; }

        .product-name {
            font-weight: 600;
            font-size: 10px;
        }

        .batch-tag {
            display: inline-block;
            background: #ede9fe;
            color: #5b21b6;
            border-radius: 3px;
            padding: 1px 4px;
            font-size: 8px;
            font-weight: 600;
        }

        /* Totals */
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .totals-row:last-child { border-bottom: none; }

        .totals-row.grand {
            font-size: 12px;
            font-weight: 700;
            color: #4f46e5;
            border-top: 2px solid #4f46e5;
            padding-top: 6px;
            margin-top: 2px;
        }

        .totals-row .lbl  { color: #666; }
        .totals-row .val  { font-weight: 600; }
        .totals-row.grand .val   { color: #4f46e5; }
        .totals-row.paid .val    { color: #059669; }
        .totals-row.balance .val { color: #dc2626; }

        /* Payment History */
        .section-title {
            font-size: 8px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .payments-table thead tr { background: #f3f4ff; }

        .payments-table thead th {
            padding: 5px 8px;
            font-size: 8px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .payments-table td {
            padding: 4px 8px;
            font-size: 9px;
            border-bottom: 1px solid #f0f0f0;
            color: #444;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #e8e8f5;
            color: #aaa;
            font-size: 9px;
            line-height: 1.6;
        }

        @media print {
            .print-bar { display: none !important; }
            body { padding: 10px 14px; }
        }
    </style>
</head>
<body>

@php
    $paid        = $bill->payments->sum('amount');
    $balance     = $bill->total - $paid;
    $isFullyPaid = $balance <= 0;
    $isOverdue   = !$isFullyPaid && $bill->due_date && $bill->due_date->isPast();

    $termLabels = [
        'cash'      => 'Cash',
        'credit_30' => 'Credit 30 Days',
        'credit_45' => 'Credit 45 Days',
        'credit_60' => 'Credit 60 Days',
    ];

    $isPdf = $isPdf ?? false;
@endphp

<div class="invoice-wrapper">

    {{-- Print Bar — hidden in PDF --}}
    @if(!$isPdf)
    <div class="print-bar">
        <a href="{{ route('bills.show', $bill) }}" class="btn-back">← Back</a>
        <a href="{{ route('bills.pdf', $bill) }}" class="btn-print">⬇ PDF</a>
        <button onclick="window.print()" class="btn-print">🖨 Print</button>
    </div>
    @endif

    {{-- Header --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:14px; padding-bottom:10px; border-bottom:2px solid #4f46e5;">
        <tr>
            <td style="vertical-align:top;">
                <div class="company-name">{{ config('app.name') }}</div>
                <div class="company-tagline">Medical Sales &amp; Distribution</div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="invoice-label">INVOICE</div>
                <div class="invoice-number">#{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date">{{ $bill->created_at->format('d F Y') }}</div>
                <div>
                    @if($isFullyPaid)
                        <span class="status-badge status-paid">✓ Paid</span>
                    @elseif($isOverdue)
                        <span class="status-badge status-overdue">⚠ Overdue</span>
                    @else
                        <span class="status-badge status-unpaid">Pending</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Info Grid — table layout for dompdf compatibility --}}
    <table style="width:100%; border-collapse:separate; border-spacing:8px 0; margin-bottom:14px;">
        <tr>
            <td style="width:50%; vertical-align:top; background:#f8f8ff; border:1px solid #e8e8f5; border-radius:5px; padding:8px 10px;">
                <div style="font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4f46e5; margin-bottom:6px; border-bottom:1px solid #e8e8f5; padding-bottom:4px;">
                    Bill To
                </div>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="font-size:9px; color:#888; width:76px; padding:2px 0;">Customer</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->customer?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:76px; padding:2px 0;">Phone</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->customer?->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:76px; padding:2px 0;">Email</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->customer?->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:76px; padding:2px 0;">Address</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->customer?->address ?? '—' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; background:#f8f8ff; border:1px solid #e8e8f5; border-radius:5px; padding:8px 10px;">
                <div style="font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4f46e5; margin-bottom:6px; border-bottom:1px solid #e8e8f5; padding-bottom:4px;">
                    Invoice Details
                </div>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="font-size:9px; color:#888; width:84px; padding:2px 0;">Invoice Date</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:84px; padding:2px 0;">Due Date</td>
                        <td style="font-size:9px; font-weight:600; padding:2px 0; color:{{ $isOverdue ? '#dc2626' : '#1a1a2e' }};">
                            {{ $bill->due_date?->format('d M Y') ?? '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:84px; padding:2px 0;">Payment Term</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $termLabels[$bill->payment_term] }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:84px; padding:2px 0;">Payment Type</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ ucfirst($bill->payment_type) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:84px; padding:2px 0;">Salesperson</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->salesperson?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; color:#888; width:84px; padding:2px 0;">Prepared By</td>
                        <td style="font-size:9px; color:#1a1a2e; font-weight:600; padding:2px 0;">{{ $bill->createdBy?->name ?? '—' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:20px">#</th>
                <th>Product</th>
                <th class="right" style="width:30px">Qty</th>
                <th class="right" style="width:95px">Unit Price</th>
                <th class="right" style="width:95px">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="product-name">{{ $item->inventory?->name ?? '—' }}</div>
                    @if($item->batch_number && $item->batch_number !== '—')
                        <span class="batch-tag">Batch: {{ $item->batch_number }}</span>
                    @endif
                </td>
                <td class="right">{{ $item->qty }}</td>
                <td class="right">{{ config('app.currency') }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ config('app.currency') }} {{ number_format($item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
        <tr>
            <td style="width:55%;"></td>
            <td style="width:45%; vertical-align:top; padding-left:8px;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="font-size:10px; color:#666; padding:4px 0; border-bottom:1px solid #f0f0f0;">Subtotal</td>
                        <td style="font-size:10px; font-weight:600; text-align:right; padding:4px 0; border-bottom:1px solid #f0f0f0;">
                            {{ config('app.currency') }} {{ number_format($bill->total, 2) }}
                        </td>
                    </tr>
                    @if($bill->advance_payment > 0)
                    <tr>
                        <td style="font-size:10px; color:#666; padding:4px 0; border-bottom:1px solid #f0f0f0;">Advance</td>
                        <td style="font-size:10px; font-weight:600; color:#059669; text-align:right; padding:4px 0; border-bottom:1px solid #f0f0f0;">
                            - {{ config('app.currency') }} {{ number_format($bill->advance_payment, 2) }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-size:12px; font-weight:700; color:#4f46e5; padding:6px 0 4px; border-top:2px solid #4f46e5; border-bottom:1px solid #f0f0f0;">Total</td>
                        <td style="font-size:12px; font-weight:700; color:#4f46e5; text-align:right; padding:6px 0 4px; border-top:2px solid #4f46e5; border-bottom:1px solid #f0f0f0;">
                            {{ config('app.currency') }} {{ number_format($bill->total, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:10px; color:#666; padding:4px 0; border-bottom:1px solid #f0f0f0;">Amount Paid</td>
                        <td style="font-size:10px; font-weight:600; color:#059669; text-align:right; padding:4px 0; border-bottom:1px solid #f0f0f0;">
                            {{ config('app.currency') }} {{ number_format($paid, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:10px; color:#666; padding:4px 0;">Balance Due</td>
                        <td style="font-size:10px; font-weight:600; text-align:right; padding:4px 0; color:{{ $balance <= 0 ? '#059669' : '#dc2626' }};">
                            {{ config('app.currency') }} {{ number_format(max($balance, 0), 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Payment History --}}
    @if($bill->payments->count() > 0)
    <div class="section-title">Payment History</div>
    <table class="payments-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Received By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->payments as $payment)
            <tr>
                <td>{{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y H:i') }}</td>
                <td style="color:#059669; font-weight:600;">
                    {{ config('app.currency') }} {{ number_format($payment->amount, 2) }}
                </td>
                <td>{{ ucfirst($payment->payment_type) }}</td>
                <td>{{ $payment->receivedBy?->name ?? '—' }}</td>
                <td>{{ $payment->notes ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Footer --}}
    <div class="invoice-footer">
        <p>Thank you for your business &mdash; {{ config('app.name') }}</p>
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
        @if($balance > 0)
        <p style="margin-top:5px; color:#dc2626; font-weight:700; font-size:9px;">
            Outstanding balance of {{ config('app.currency') }} {{ number_format($balance, 2) }}
            due by {{ $bill->due_date?->format('d M Y') ?? 'due date' }}.
        </p>
        @endif
    </div>

</div>
</body>
</html>