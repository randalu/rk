<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')
                  ->constrained('returns')
                  ->cascadeOnDelete();
            $table->foreignId('bill_item_id')
                  ->nullable()
                  ->constrained('bill_items')
                  ->nullOnDelete();
            $table->foreignId('inventory_id')
                  ->nullable()
                  ->constrained('inventory')
                  ->nullOnDelete();
            $table->string('batch_number');
            $table->integer('qty')->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};