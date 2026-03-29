<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_log', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_phone');
            $table->enum('recipient_type', ['customer', 'salesperson', 'admin']);
            $table->enum('sms_type', [
                'new_bill',
                'due_reminder',
                'low_stock',
                'admin_notification'
            ]);
            $table->text('message');
            $table->nullableMorphs('reference');
            $table->enum('status', ['pending', 'sent', 'failed'])
                  ->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_log');
    }
};