<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete();
            $table->foreignId('salesperson_id')
                  ->nullable()
                  ->constrained('salespeople')
                  ->nullOnDelete();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->enum('payment_type', ['cash', 'card', 'online'])
                  ->default('cash');
            $table->enum('payment_term', [
                'cash', 'credit_30', 'credit_45', 'credit_60'
            ])->default('cash');
            $table->date('due_date')->nullable();
            $table->decimal('advance_payment', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};