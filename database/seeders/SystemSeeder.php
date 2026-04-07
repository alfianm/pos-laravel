<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CustomerGroup;
use App\Models\ExpenseCategory;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\ProductCategory;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // Seed permissions and roles first
        $this->call(PermissionAndUserSeeder::class);

        // Get first tenant and branch for seeding
        $tenant = Tenant::first();
        $branch = $tenant ? Branch::where('tenant_id', $tenant->id)->first() : null;

        if (!$tenant || !$branch) {
            $this->command->warn('No tenant or branch found. Skipping master data seeding.');
            return;
        }

        // Seed Product Categories
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik'],
            ['name' => 'Pakaian', 'slug' => 'pakaian'],
            ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman'],
            ['name' => 'Peralatan Rumah Tangga', 'slug' => 'peralatan-rumah-tangga'],
            ['name' => 'Kesehatan & Kecantikan', 'slug' => 'kesehatan-kecantikan'],
        ];

        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $cat['slug']],
                ['id' => Str::uuid(), 'name' => $cat['name']]
            );
        }

        // Seed Units
        $units = [
            ['name' => 'Pieces', 'short_name' => 'pcs'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Liter', 'short_name' => 'ltr'],
            ['name' => 'Pack', 'short_name' => 'pack'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $unit['name']],
                [
                    'id' => Str::uuid(),
                    'short_name' => $unit['short_name'],
                ]
            );
        }

        // Seed Customer Groups
        $groups = [
            ['name' => 'Retail', 'discount_percentage' => 0],
            ['name' => 'Wholesale', 'discount_percentage' => 10],
            ['name' => 'Member', 'discount_percentage' => 5],
            ['name' => 'VIP', 'discount_percentage' => 15],
        ];

        foreach ($groups as $group) {
            CustomerGroup::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $group['name']],
                [
                    'id' => Str::uuid(),
                    'discount_percentage' => $group['discount_percentage'],
                ]
            );
        }

        // Seed Expense Categories
        $expenseCats = [
            ['name' => 'Operasional'],
            ['name' => 'Utilitas'],
            ['name' => 'Gaji Karyawan'],
            ['name' => 'Pemasaran'],
            ['name' => 'Perawatan'],
        ];

        foreach ($expenseCats as $cat) {
            ExpenseCategory::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $cat['name']],
                ['id' => Str::uuid()]
            );
        }

        // Seed Lead Sources
        $sources = [
            ['name' => 'Website'],
            ['name' => 'Instagram'],
            ['name' => 'Facebook'],
            ['name' => 'Referral'],
            ['name' => 'Walk-in'],
        ];

        foreach ($sources as $source) {
            LeadSource::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $source['name']],
                ['id' => Str::uuid()]
            );
        }

        // Seed Lead Stages
        $stages = [
            ['name' => 'New', 'sort_order' => 1],
            ['name' => 'Contacted', 'sort_order' => 2],
            ['name' => 'Qualified', 'sort_order' => 3],
            ['name' => 'Proposal', 'sort_order' => 4],
            ['name' => 'Negotiation', 'sort_order' => 5],
            ['name' => 'Won', 'sort_order' => 6],
            ['name' => 'Lost', 'sort_order' => 7],
        ];

        foreach ($stages as $stage) {
            LeadStage::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $stage['name']],
                [
                    'id' => Str::uuid(),
                    'sort_order' => $stage['sort_order'],
                ]
            );
        }

        $this->command->info('System seeded with master data!');
    }
}
