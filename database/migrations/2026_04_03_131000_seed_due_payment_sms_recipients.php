<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $recipients = [
            ['name' => 'Due Alert Recipient 1', 'phone' => '+94777404499'],
            ['name' => 'Due Alert Recipient 2', 'phone' => '+94776474542'],
        ];

        foreach ($recipients as $recipient) {
            $existing = DB::table('sms_recipients')->where('phone', $recipient['phone'])->first();

            DB::table('sms_recipients')->updateOrInsert(
                ['phone' => $recipient['phone']],
                [
                    'name' => $existing->name ?? $recipient['name'],
                    'notify_low_stock' => $existing->notify_low_stock ?? false,
                    'notify_due_payments' => true,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => $existing->created_at ?? now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('sms_recipients')
            ->whereIn('phone', ['+94777404499', '+94776474542'])
            ->delete();
    }
};
