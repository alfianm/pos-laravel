<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class PermissionAndUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'view owner dashboard',
            'access pos',
            'give discount',
            'open drawer',
            'view sales',
            'void sales',
            'refund sales',
            'view inventory',
            'manage stocks',
            'opening stock',
            'stock adjustment',
            'manage transfers',
            'view purchases',
            'create purchases',
            'approve purchases',
            'receive purchases',
            'view expenses',
            'manage expenses',
            'approve expenses',
            'view products',
            'manage products',
            'view customers',
            'manage customers',
            'view suppliers',
            'manage suppliers',
            'view categories',
            'manage categories',
            'view brands',
            'manage brands',
            'view units',
            'manage units',
            'view branches',
            'manage branches',
            'view users',
            'manage users',
            'view roles',
            'manage roles',
            'view leads',
            'manage leads',
            'convert leads',
            'view follow-ups',
            'manage follow-ups',
            'view proposals',
            'create proposals',
            'view loyalty',
            'manage loyalty',
            'view vouchers',
            'manage vouchers',
            'view marketplace',
            'manage marketplace accounts',
            'manage marketplace shops',
            'manage product mapping',
            'view sync logs',
            'sync orders',
            'sync stock',
            'view reports',
            'export reports',
            'manage settings',
            'manage tenants',
            'view audit_logs',
            'view subscription plans',
            'manage subscription plans',
            'view tenant subscriptions',
            'view payment methods',
            'manage payment methods',
            'view payments',
            'record payments',
            'view custom domain',
            'manage custom domain',
            'manage webhooks',
            'view returns',
            'manage returns',
            'manage bulk import',
            'view chart of accounts',
            'create chart of accounts',
            'edit chart of accounts',
            'delete chart of accounts',
            'manage chart of accounts',
            'view journal entries',
            'create journal entries',
            'edit journal entries',
            'delete journal entries',
            'post journal entries',
            'manage journal entries',
            'view accounts receivable',
            'create accounts receivable',
            'edit accounts receivable',
            'delete accounts receivable',
            'manage accounts receivable',
            'view accounts payable',
            'create accounts payable',
            'edit accounts payable',
            'delete accounts payable',
            'manage accounts payable',
        ];

        $now = now();
        foreach ($permissions as $name) {
            if (!DB::table('permissions')->where('name', $name)->where('guard_name', 'web')->exists()) {
                DB::table('permissions')->insert([
                    'id' => Str::uuid()->toString(),
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $roles = ['super_admin', 'owner', 'branch_manager', 'cashier', 'inventory_staff', 'purchasing_staff', 'crm_staff', 'omnichannel_staff'];

        foreach ($roles as $name) {
            if (!DB::table('roles')->where('name', $name)->where('guard_name', 'web')->exists()) {
                DB::table('roles')->insert([
                    'id' => Str::uuid()->toString(),
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $superAdminRoleId = DB::table('roles')->where('name', 'super_admin')->value('id');
        $ownerRoleId = DB::table('roles')->where('name', 'owner')->value('id');
        $managerRoleId = DB::table('roles')->where('name', 'branch_manager')->value('id');
        $cashierRoleId = DB::table('roles')->where('name', 'cashier')->value('id');
        $inventoryRoleId = DB::table('roles')->where('name', 'inventory_staff')->value('id');
        $purchasingRoleId = DB::table('roles')->where('name', 'purchasing_staff')->value('id');
        $crmRoleId = DB::table('roles')->where('name', 'crm_staff')->value('id');
        $omnichannelRoleId = DB::table('roles')->where('name', 'omnichannel_staff')->value('id');

        $allPermissionIds = DB::table('permissions')->where('guard_name', 'web')->pluck('id');
        foreach ($allPermissionIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permId,
                'role_id' => $superAdminRoleId,
            ]);
        }

        $ownerPerms = ['view dashboard', 'view owner dashboard', 'view sales', 'view inventory', 'view purchases', 'view expenses', 'view customers', 'view suppliers', 'view leads', 'view proposals', 'view loyalty', 'view marketplace', 'view reports', 'view audit_logs', 'view branches', 'view users', 'view tenant subscriptions', 'view payment methods', 'view payments', 'view custom domain', 'manage custom domain', 'manage webhooks', 'view returns', 'manage returns', 'manage bulk import', 'view chart of accounts', 'create chart of accounts', 'edit chart of accounts', 'delete chart of accounts', 'manage chart of accounts', 'view journal entries', 'create journal entries', 'edit journal entries', 'delete journal entries', 'post journal entries', 'manage journal entries', 'view accounts receivable', 'create accounts receivable', 'edit accounts receivable', 'delete accounts receivable', 'manage accounts receivable', 'view accounts payable', 'create accounts payable', 'edit accounts payable', 'delete accounts payable', 'manage accounts payable'];
        $ownerPermIds = DB::table('permissions')->whereIn('name', $ownerPerms)->pluck('id');
        foreach ($ownerPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $ownerRoleId]);
        }

        $managerPerms = ['view dashboard', 'access pos', 'give discount', 'view sales', 'void sales', 'view inventory', 'manage stocks', 'stock adjustment', 'view purchases', 'create purchases', 'receive purchases', 'view expenses', 'manage expenses', 'view products', 'manage products', 'view customers', 'manage customers', 'view leads', 'manage leads', 'convert leads', 'view follow-ups', 'manage follow-ups', 'view proposals', 'create proposals', 'view loyalty', 'view vouchers', 'view reports', 'view returns', 'manage returns', 'manage bulk import'];
        $managerPermIds = DB::table('permissions')->whereIn('name', $managerPerms)->pluck('id');
        foreach ($managerPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $managerRoleId]);
        }

        $cashierPerms = ['view dashboard', 'access pos', 'open drawer', 'view sales', 'view inventory', 'view returns', 'manage returns'];
        $cashierPermIds = DB::table('permissions')->whereIn('name', $cashierPerms)->pluck('id');
        foreach ($cashierPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $cashierRoleId]);
        }

        $inventoryPerms = ['view dashboard', 'view inventory', 'manage stocks', 'opening stock', 'stock adjustment', 'manage transfers', 'view products', 'manage products', 'view categories', 'manage categories', 'view brands', 'manage brands', 'view units', 'manage units'];
        $inventoryPermIds = DB::table('permissions')->whereIn('name', $inventoryPerms)->pluck('id');
        foreach ($inventoryPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $inventoryRoleId]);
        }

        $purchasingPerms = ['view dashboard', 'view purchases', 'create purchases', 'receive purchases', 'view inventory', 'view products', 'view suppliers', 'manage suppliers'];
        $purchasingPermIds = DB::table('permissions')->whereIn('name', $purchasingPerms)->pluck('id');
        foreach ($purchasingPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $purchasingRoleId]);
        }

        $crmPerms = ['view dashboard', 'view leads', 'manage leads', 'convert leads', 'view follow-ups', 'manage follow-ups', 'view proposals', 'create proposals', 'view customers', 'manage customers'];
        $crmPermIds = DB::table('permissions')->whereIn('name', $crmPerms)->pluck('id');
        foreach ($crmPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $crmRoleId]);
        }

        $omnichannelPerms = ['view dashboard', 'view marketplace', 'manage marketplace accounts', 'manage marketplace shops', 'manage product mapping', 'view sync logs', 'sync orders', 'sync stock'];
        $omnichannelPermIds = DB::table('permissions')->whereIn('name', $omnichannelPerms)->pluck('id');
        foreach ($omnichannelPermIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permId, 'role_id' => $omnichannelRoleId]);
        }

        $tenants = [
            ['name' => 'PT Makmur Sentosa', 'code' => 'MAK', 'slug' => 'pt-makmur-sentosa', 'admin_email' => 'admin@makmur.com', 'branch' => 'Makmur Pusat'],
            ['name' => 'Cafe Bintang Lima', 'code' => 'BIN', 'slug' => 'cafe-bintang-lima', 'admin_email' => 'owner@bintang.com', 'branch' => 'Bintang Jakarta'],
            ['name' => 'Toko Kelontong Berdikari', 'code' => 'BER', 'slug' => 'toko-kelontong-berdikari', 'admin_email' => 'admin@berdikari.com', 'branch' => 'Berdikari Bandung'],
        ];

        foreach ($tenants as $t) {
            $tenantSlug = $t['slug'];
            $branchSlug = Str::slug($t['branch']);
            $tenant = Tenant::firstOrCreate(['name' => $t['name']], ['id' => Str::uuid(), 'code' => $t['code'], 'slug' => $tenantSlug, 'status' => 'active']);
            
            $branchCode = strtoupper(substr($t['branch'], 0, 3));
            $branch = Branch::where('tenant_id', $tenant->id)->where('code', $branchCode)->first();
            
            if (!$branch) {
                $branch = Branch::create([
                    'id' => Str::uuid(),
                    'tenant_id' => $tenant->id,
                    'name' => $t['branch'],
                    'code' => $branchCode,
                    'slug' => $branchSlug,
                    'status' => 'active'
                ]);
            }

            $user = User::firstOrCreate(['email' => $t['admin_email']], [
                'name' => 'Admin ' . $t['name'],
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'active_branch_id' => $branch->id,
                'email_verified_at' => now()
            ]);
            
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $superAdminRoleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);
        }

        $this->command->info('Permissions seeded! Default password: password');
    }
}
