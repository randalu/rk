<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $companyName }} Due Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; background: #f8fafc; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <div style="padding: 20px 24px; background: #eef2ff; border-bottom: 1px solid #dbeafe;">
            <div style="font-size: 20px; font-weight: 700; color: #3730a3;">{{ $companyName }}</div>
            <div style="font-size: 13px; color: #6b7280;">Payment Reminder</div>
        </div>

        <div style="padding: 24px;">
            {!! nl2br(e($bodyText)) !!}

            <div style="margin-top: 24px; padding: 16px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px;">
                <div style="font-size: 13px; color: #6b7280;">Invoice</div>
                <div style="font-size: 18px; font-weight: 700; color: #111827;">#{{ str_pad((string) $bill->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size: 13px; color: #374151; margin-top: 8px;">Due date: {{ $bill->due_date?->format('d M Y') ?? 'N/A' }}</div>
            </div>

            <div style="margin-top: 24px; font-size: 12px; color: #6b7280;">
                The invoice PDF is attached for your reference.
            </div>
        </div>
    </div>
</body>
</html>
