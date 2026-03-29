<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'permissions' => [
                    'create_bill', 'edit_bill', 'delete_bill',
                    'create_purchase', 'edit_purchase', 'delete_purchase',
                    'create_customer', 'edit_customer', 'delete_customer',
                    'create_supplier', 'edit_supplier', 'delete_supplier',
                    'create_inventory', 'edit_inventory', 'delete_inventory',
                    'approve_return', 'reject_return',
                    'release_commission', 'edit_commission_tiers',
                    'create_expense', 'edit_expense', 'delete_expense',
                    'manage_users', 'manage_roles',
                    'manage_sms_recipients', 'view_reports',
                    'view_action_log',
                ],
            ],
            [
                'name' => 'manager',
                'permissions' => [
                    'create_bill', 'edit_bill',
                    'create_purchase', 'edit_purchase',
                    'create_customer', 'edit_customer',
                    'create_supplier', 'edit_supplier',
                    'create_inventory', 'edit_inventory',
                    'approve_return', 'reject_return',
                    'release_commission',
                    'create_expense', 'edit_expense',
                    'view_reports',
                ],
            ],
            [
                'name' => 'cashier',
                'permissions' => [
                    'create_bill',
                    'create_customer',
                    'view_inventory',
                ],
            ],
            [
                'name' => 'salesman',
                'permissions' => [
                    'create_bill',
                    'create_customer',
                    'view_inventory',
                    'view_own_commissions',
                ],
            ],
        ];

        foreach ($roles as $role) {
            UserRole::create($role);
        }
    }
}