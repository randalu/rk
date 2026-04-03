<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name');
            $table->string('logo_path')->nullable();
            $table->string('company_name');
            $table->string('company_tagline')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_registration_no')->nullable();
            $table->string('invoice_footer_heading')->nullable();
            $table->text('invoice_footer_notes')->nullable();
            $table->timestamps();
        });

        DB::table('system_settings')->insert([
            'id' => 1,
            'system_name' => config('app.name'),
            'logo_path' => file_exists(public_path('RK_logo.PNG')) ? 'RK_logo.PNG' : null,
            'company_name' => config('app.name'),
            'company_tagline' => 'Medical Sales & Distribution',
            'company_phone' => null,
            'company_email' => null,
            'company_website' => null,
            'company_address' => null,
            'company_registration_no' => null,
            'invoice_footer_heading' => 'Thank you for your business',
            'invoice_footer_notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
