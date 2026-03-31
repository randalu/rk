<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDueReminders extends Command
{
    protected $signature   = 'sms:due-reminders';
    protected $description = 'Send SMS reminders for bills due in 7 days';

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
            $this->info("Reminder sent for Bill #{$bill->id}");
        }

        $this->info("Done. {$bills->count()} reminders sent.");
    }
}