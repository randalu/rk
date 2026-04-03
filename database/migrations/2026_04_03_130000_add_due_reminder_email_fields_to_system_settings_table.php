<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('due_reminder_email_subject')->nullable()->after('invoice_footer_notes');
            $table->text('due_reminder_email_body')->nullable()->after('due_reminder_email_subject');
        });

        DB::table('system_settings')
            ->whereNull('due_reminder_email_subject')
            ->update([
                'due_reminder_email_subject' => 'Reminder: Invoice #{invoice_number} due on {due_date}',
                'due_reminder_email_body' => "Dear {customer_name},\n\nThis is a reminder that invoice #{invoice_number} has an outstanding balance of {currency} {balance} and is due on {due_date}.\n\nPlease find the invoice PDF attached for your reference.\n\nThank you,\n{company_name}",
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['due_reminder_email_subject', 'due_reminder_email_body']);
        });
    }
};
