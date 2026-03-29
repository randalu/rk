<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = UserRole::where('name', 'admin')->first();

        User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@raphakallos.com',
            'password' => Hash::make('Admin@1234'),
            'role_id'  => $adminRole->id,
        ]);
    }
}