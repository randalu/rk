<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salesperson;

class SalespersonSeeder extends Seeder
{
    public function run(): void
    {
        // Default "Direct Sale" record — no user account linked
        Salesperson::create([
            'user_id'         => null,
            'name'            => 'Direct Sale',
            'phone'           => null,
            'commission_type' => 'value_based',
            'target_period'   => 'monthly',
            'is_active'       => true,
        ]);
    }
}