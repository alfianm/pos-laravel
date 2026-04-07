<?php

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PermissionAndUserSeeder;
use Database\Seeders\RealisticDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('seeds realistic cross-module demo data for the main tenant', function (): void {
    $this->seed(DatabaseSeeder::class);

    $tenantId = DB::table('tenants')->where('code', 'MAK')->value('id');

    expect($tenantId)->not->toBeNull();

    $this->assertDatabaseHas('branches', [
        'tenant_id' => $tenantId,
        'name' => 'Makmur Pusat Jakarta',
    ]);

    $this->assertDatabaseHas('products', [
        'tenant_id' => $tenantId,
        'name' => 'Indomie Mi Goreng 85 g',
    ]);

    $this->assertDatabaseHas('customers', [
        'tenant_id' => $tenantId,
        'name' => 'Rina Maharani',
    ]);

    $this->assertDatabaseHas('marketplace_accounts', [
        'tenant_id' => $tenantId,
        'name' => 'Tokopedia Makmur Official',
    ]);

    $this->assertDatabaseHas('proposals', [
        'tenant_id' => $tenantId,
        'proposal_no' => 'PRP-MAK-202603-001',
        'status' => 'accepted',
    ]);

    expect(DB::table('purchase_orders')->where('tenant_id', $tenantId)->count())->toBe(2);
    expect(DB::table('sales')->where('tenant_id', $tenantId)->count())->toBe(4);
    expect(DB::table('stock_transfers')->where('tenant_id', $tenantId)->count())->toBe(2);
    expect(DB::table('stock_adjustments')->where('tenant_id', $tenantId)->count())->toBe(1);
    expect(DB::table('marketplace_orders')->where('tenant_id', $tenantId)->count())->toBe(2);
    expect(DB::table('follow_ups')->where('tenant_id', $tenantId)->count())->toBe(3);
});

it('can rerun the realistic demo seeder without duplicating core data', function (): void {
    $this->seed(PermissionAndUserSeeder::class);
    $this->seed(RealisticDemoSeeder::class);
    $this->seed(RealisticDemoSeeder::class);

    $tenantId = DB::table('tenants')->where('code', 'MAK')->value('id');

    expect($tenantId)->not->toBeNull();
    expect(DB::table('products')->where('tenant_id', $tenantId)->count())->toBe(12);
    expect(DB::table('product_variants')->where('tenant_id', $tenantId)->count())->toBe(4);
    expect(DB::table('sales')->where('tenant_id', $tenantId)->count())->toBe(4);
    expect(DB::table('purchase_orders')->where('tenant_id', $tenantId)->count())->toBe(2);
    expect(DB::table('proposals')->where('tenant_id', $tenantId)->count())->toBe(2);
});
