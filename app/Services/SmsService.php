<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $userId;
    private string $apiKey;
    private string $senderId;
    private string $baseUrl;

    public function __construct()
    {
        $this->userId   = env('SMS_USER_ID');
        $this->apiKey   = env('SMS_API_KEY');
        $this->senderId = env('SMS_SENDER_ID', 'SMSlenzDEMO');
        $this->baseUrl  = env('SMS_BASE_URL', 'https://smslenz.lk/api');
    }

    /**
     * Send a single SMS and log it
     */
    public function send(
        string $phone,
        string $message,
        string $recipientType,
        string $smsType,
        ?int   $referenceId   = null,
        ?string $referenceType = null
    ): bool {
        // Format phone number
        $phone = $this->formatPhone($phone);

        // Log as pending first
        $log = SmsLog::create([
            'recipient_phone' => $phone,
            'recipient_type'  => $recipientType,
            'sms_type'        => $smsType,
            'message'         => $message,
            'reference_id'    => $referenceId,
            'reference_type'  => $referenceType,
            'status'          => 'pending',
        ]);

        try {
            $response = Http::post($this->baseUrl . '/send-sms', [
                'user_id'   => $this->userId,
                'api_key'   => $this->apiKey,
                'sender_id' => $this->senderId,
                'contact'   => $phone,
                'message'   => $message,
            ]);

            if ($response->successful()) {
                $log->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                ]);
                return true;
            }

            $log->update([
                'status'        => 'failed',
                'error_message' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error('SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk SMS to multiple numbers
     */
    public function sendBulk(
        array  $phones,
        string $message,
        string $recipientType,
        string $smsType
    ): bool {
        $phones = array_map([$this, 'formatPhone'], $phones);

        // Log each recipient
        foreach ($phones as $phone) {
            SmsLog::create([
                'recipient_phone' => $phone,
                'recipient_type'  => $recipientType,
                'sms_type'        => $smsType,
                'message'         => $message,
                'status'          => 'pending',
            ]);
        }

        try {
            $response = Http::post($this->baseUrl . '/send-bulk-sms', [
                'user_id'   => $this->userId,
                'api_key'   => $this->apiKey,
                'sender_id' => $this->senderId,
                'contacts'  => $phones,
                'message'   => $message,
            ]);

            $status = $response->successful() ? 'sent' : 'failed';
            $error  = $response->successful() ? null : $response->body();

            // Update all pending logs for this batch
            SmsLog::where('status', 'pending')
                  ->where('sms_type', $smsType)
                  ->whereIn('recipient_phone', $phones)
                  ->update([
                      'status'        => $status,
                      'sent_at'       => $response->successful() ? now() : null,
                      'error_message' => $error,
                  ]);

            return $response->successful();

        } catch (\Exception $e) {
            SmsLog::where('status', 'pending')
                  ->where('sms_type', $smsType)
                  ->whereIn('recipient_phone', $phones)
                  ->update([
                      'status'        => 'failed',
                      'error_message' => $e->getMessage(),
                  ]);

            Log::error('Bulk SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone to +94 format
     */
    private function formatPhone(string $phone): string
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);

        // Already in +94 format
        if (str_starts_with($phone, '+94')) {
            return $phone;
        }

        // Convert 07X to +947X
        if (str_starts_with($phone, '0')) {
            return '+94' . substr($phone, 1);
        }

        // Convert 94X to +94X
        if (str_starts_with($phone, '94')) {
            return '+' . $phone;
        }

        return $phone;
    }

    // ─── Convenience methods ───────────────────────────────────────────────

    public function newBillSms($bill): void
    {
        if (!$bill->customer?->phone) return;

        $termLabel = match($bill->payment_term) {
            'credit_30' => '30-day credit',
            'credit_45' => '45-day credit',
            'credit_60' => '60-day credit',
            default     => 'cash',
        };

        $message = "Dear {$bill->customer->name}, your invoice #{$bill->id} "
                 . "for " . config('app.currency') . " " . number_format($bill->total, 2)
                 . " has been created. Payment term: {$termLabel}."
                 . ($bill->due_date ? " Due date: {$bill->due_date->format('d M Y')}." : "")
                 . " Thank you - " . config('app.name');

        $this->send(
            $bill->customer->phone,
            $message,
            'customer',
            'new_bill',
            $bill->id,
            'Bill'
        );

        // Also notify salesperson if credit bill
        if ($bill->payment_term !== 'cash' && $bill->salesperson?->phone) {
            $spMessage = "New bill #{$bill->id} created for {$bill->customer->name}. "
                       . "Amount: " . config('app.currency') . " " . number_format($bill->total, 2)
                       . ". Due: {$bill->due_date?->format('d M Y')}."
                       . " - " . config('app.name');

            $this->send(
                $bill->salesperson->phone,
                $spMessage,
                'salesperson',
                'new_bill',
                $bill->id,
                'Bill'
            );
        }
    }

    public function dueDateReminderSms($bill): void
    {
        $balance = $bill->total - $bill->payments->sum('amount');
        if ($balance <= 0) return;

        $message = "Reminder: Invoice #{$bill->id} for "
                 . config('app.currency') . " " . number_format($balance, 2)
                 . " is due on {$bill->due_date->format('d M Y')}."
                 . " Please arrange payment. - " . config('app.name');

        // Notify customer
        if ($bill->customer?->phone) {
            $this->send(
                $bill->customer->phone,
                $message,
                'customer',
                'due_reminder',
                $bill->id,
                'Bill'
            );
        }

        // Notify salesperson
        if ($bill->salesperson?->phone) {
            $spMessage = "Payment due reminder: Bill #{$bill->id} ({$bill->customer?->name}) "
                       . "- " . config('app.currency') . " " . number_format($balance, 2)
                       . " due on {$bill->due_date->format('d M Y')}."
                       . " - " . config('app.name');

            $this->send(
                $bill->salesperson->phone,
                $spMessage,
                'salesperson',
                'due_reminder',
                $bill->id,
                'Bill'
            );
        }
    }

    public function lowStockSms($item): void
    {
        $recipients = \App\Models\SmsRecipient::where('notify_low_stock', true)
            ->where('is_active', true)
            ->pluck('phone')
            ->toArray();

        if (empty($recipients)) return;

        $message = "Low stock alert: {$item->name}"
                 . ($item->batch_number ? " (Batch: {$item->batch_number})" : "")
                 . " has only {$item->qty} units remaining."
                 . " - " . config('app.name');

        $this->sendBulk($recipients, $message, 'admin', 'low_stock');
    }

    public function duePaymentAdminSms(array $bills): void
    {
        $recipients = \App\Models\SmsRecipient::where('notify_due_payments', true)
            ->where('is_active', true)
            ->pluck('phone')
            ->toArray();

        if (empty($recipients) || empty($bills)) return;

        $count   = count($bills);
        $total   = array_sum(array_column($bills, 'balance'));
        $message = "{$count} bill(s) are due within 7 days. "
                 . "Total outstanding: " . config('app.currency') . " " . number_format($total, 2)
                 . ". - " . config('app.name');

        $this->sendBulk($recipients, $message, 'admin', 'admin_notification');
    }
}