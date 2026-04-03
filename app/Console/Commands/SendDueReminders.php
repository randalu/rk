<?php

namespace App\Console\Commands;

use App\Mail\DueReminderMail;
use App\Models\Bill;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDueReminders extends Command
{
    protected $signature   = 'sms:due-reminders';
    protected $description = 'Send SMS and email reminders for bills due in 7 days';

    public function handle(): void
    {
        $sms = new SmsService();

        // Find bills due exactly 7 days from today
        $targetDate = Carbon::today()->addDays(7)->toDateString();

        $bills = Bill::with('customer', 'salesperson', 'payments')
            ->whereDate('due_date', $targetDate)
            ->get()
            ->filter(fn($b) => $b->payments->sum('amount') < $b->total);

        foreach ($bills as $bill) {
            $sms->dueDateReminderSms($bill);
            $this->sendReminderEmail($bill);
            $this->info("Reminder sent for Bill #{$bill->id}");
        }

        $this->info("Done. {$bills->count()} reminders sent.");
    }

    private function sendReminderEmail(Bill $bill): void
    {
        if (!$bill->customer?->email) {
            return;
        }

        $balance = $bill->total - $bill->payments->sum('amount');

        if ($balance <= 0) {
            return;
        }

        $variables = [
            'customer_name' => $bill->customer->name,
            'invoice_number' => str_pad((string) $bill->id, 5, '0', STR_PAD_LEFT),
            'balance' => number_format($balance, 2),
            'due_date' => $bill->due_date?->format('d M Y') ?? '',
            'company_name' => systemSetting('company_name', config('app.name')),
            'system_name' => systemSetting('system_name', config('app.name')),
            'currency' => config('app.currency'),
        ];

        $subject = systemTemplateRender(
            systemSetting('due_reminder_email_subject', 'Reminder: Invoice #{invoice_number} due on {due_date}'),
            $variables
        );

        $body = systemTemplateRender(
            systemSetting('due_reminder_email_body'),
            $variables
        );

        Mail::to($bill->customer->email)->send(
            new DueReminderMail($bill, $subject, $body)
        );
    }
}
