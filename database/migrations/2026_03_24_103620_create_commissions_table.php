<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')
                  ->constrained('bills')
                  ->cascadeOnDelete();
            $table->foreignId('salesperson_id')
                  ->nullable()
                  ->constrained('salespeople')
                  ->nullOnDelete();
            $table->decimal('bill_total', 10, 2)->default(0);
            $table->enum('commission_type', ['qty_based', 'value_based'])
                  ->default('value_based');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('deducted_returns', 10, 2)->default(0);
            $table->decimal('net_commission', 10, 2)->default(0);
            $table->enum('status', ['pending', 'payable', 'paid', 'cancelled'])
                  ->default('pending');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};