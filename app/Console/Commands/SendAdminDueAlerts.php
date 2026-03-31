<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendAdminDueAlerts extends Command
{
    protected $signature   = 'sms:admin-due-alerts';
    protected $description = 'Send admin SMS for bills due in next 7 days';

    public function handle(): void
    {
        $sms = new SmsService();

        $bills = Bill::with('customer', 'payments')
            ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->get()
            ->filter(fn($b) => $b->payments->sum('amount') < $b->total);

        if ($bills->isEmpty()) {
            $this->info('No upcoming bills. No alert sent.');
            return;
        }

        $billData = $bills->map(fn($b) => [
            'id'      => $b->id,
            'balance' => $b->total - $b->payments->sum('amount'),
        ])->toArray();

        $sms->duePaymentAdminSms($billData);
        $this->info("Admin alert sent for {$bills->count()} upcoming bills.");
    }
}