<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AccountingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions for Chart of Accounts
        $chartPermissions = [
            'view chart of accounts',
            'create chart of accounts',
            'edit chart of accounts',
            'delete chart of accounts',
        ];

        // Permissions for Journal Entries
        $journalPermissions = [
            'view journal entries',
            'create journal entries',
            'edit journal entries',
            'delete journal entries',
            'post journal entries',
        ];

        // Permissions for AR/AP
        $arApPermissions = [
            'view accounts receivable',
            'create accounts receivable',
            'edit accounts receivable',
            'delete accounts receivable',
            'view accounts payable',
            'create accounts payable',
            'edit accounts payable',
            'delete accounts payable',
        ];

        // Permissions for Financial Reports
        $reportPermissions = [
            'view trial balance',
            'view income statement',
            'view balance sheet',
            'view cash flow',
        ];

        $allPermissions = array_merge($chartPermissions, $journalPermissions, $arApPermissions, $reportPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Assign to super-admin if exists
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($allPermissions);
        }

        // Assign to admin if exists
        $admin = Role::where('name', 'owner')->first();
        if ($admin) {
            $admin->givePermissionTo($allPermissions);
        }

        $this->command->info('Accounting permissions seeded successfully.');
    }
}
