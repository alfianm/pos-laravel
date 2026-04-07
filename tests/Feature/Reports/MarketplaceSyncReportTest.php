<?php

use App\Livewire\Reports\MarketplaceSyncReport;
use App\Models\Branch;
use App\Models\MarketplaceAccount;
use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MarketplaceService;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    Permission::findOrCreate('view reports', 'web');
    $this->user->givePermissionTo('view reports');

    $this->account = MarketplaceAccount::create([
        'tenant_id' => $this->tenant->id,
        'marketplace' => 'tokopedia',
        'name' => 'Tokopedia Account',
        'status' => 'active',
    ]);

    $this->shop = MarketplaceShop::create([
        'tenant_id' => $this->tenant->id,
        'marketplace_account_id' => $this->account->id,
        'branch_id' => $this->branch->id,
        'external_shop_id' => 'SHOP-001',
        'marketplace' => 'tokopedia',
        'name' => 'Main Tokopedia Shop',
        'status' => 'active',
    ]);

    $this->actingAs($this->user);
});

it('displays the marketplace sync report page for authorized users', function (): void {
    $this->get(route('reports.marketplace.sync'))
        ->assertSuccessful()
        ->assertSee('Marketplace Sync Report');
});

it('summarizes marketplace sync metrics for the active tenant only', function (): void {
    MarketplaceSyncLog::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'marketplace_shop_id' => $this->shop->id,
        'marketplace' => 'tokopedia',
        'sync_type' => 'order_import',
        'status' => 'success',
        'error_message' => 'Imported 12 orders',
        'synced_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    MarketplaceSyncLog::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'marketplace_shop_id' => $this->shop->id,
        'marketplace' => 'tokopedia',
        'sync_type' => 'stock_sync',
        'status' => 'failed',
        'error_message' => 'Stock sync failed',
        'synced_at' => now(),
        'created_at' => now()->subMinute(),
        'updated_at' => now()->subMinute(),
    ]);

    MarketplaceSyncLog::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'marketplace_shop_id' => $this->shop->id,
        'marketplace' => 'shopee',
        'sync_type' => 'reconnect',
        'status' => 'pending',
        'error_message' => 'Waiting for reconnect confirmation',
        'synced_at' => now(),
        'created_at' => now()->subMinutes(2),
        'updated_at' => now()->subMinutes(2),
    ]);

    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create([
        'tenant_id' => $otherTenant->id,
    ]);
    $otherAccount = MarketplaceAccount::create([
        'tenant_id' => $otherTenant->id,
        'marketplace' => 'lazada',
        'name' => 'Other Account',
        'status' => 'active',
    ]);
    $otherShop = MarketplaceShop::create([
        'tenant_id' => $otherTenant->id,
        'marketplace_account_id' => $otherAccount->id,
        'branch_id' => $otherBranch->id,
        'external_shop_id' => 'SHOP-OTHER-001',
        'marketplace' => 'lazada',
        'name' => 'Other Shop',
        'status' => 'active',
    ]);

    MarketplaceSyncLog::create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'marketplace_shop_id' => $otherShop->id,
        'marketplace' => 'lazada',
        'sync_type' => 'order_import',
        'status' => 'failed',
        'error_message' => 'Other tenant failure',
        'synced_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $component = Livewire::test(MarketplaceSyncReport::class)
        ->set('date_from', now()->subDay()->format('Y-m-d'))
        ->set('date_to', now()->addDay()->format('Y-m-d'));

    $grandTotals = $component->get('grandTotals');
    $marketplaceSummary = $component->get('marketplaceSummary');
    $syncTypeSummary = $component->get('syncTypeSummary');
    $recentLogs = $component->get('recentLogs');

    expect((int) $grandTotals->total_syncs)->toBe(3)
        ->and((int) $grandTotals->success_count)->toBe(1)
        ->and((int) $grandTotals->failed_count)->toBe(1)
        ->and((int) $grandTotals->pending_count)->toBe(1)
        ->and((int) $marketplaceSummary->firstWhere('marketplace', 'tokopedia')->total)->toBe(2)
        ->and((int) $marketplaceSummary->firstWhere('marketplace', 'shopee')->total)->toBe(1)
        ->and((int) $syncTypeSummary->firstWhere('sync_type', 'order_import')->total)->toBe(1)
        ->and($recentLogs->total())->toBe(3);

    $component->assertSee('Imported 12 orders')
        ->assertSee('Order Import')
        ->assertDontSee('Other tenant failure');
});

it('logs marketplace sync events using the current schema', function (): void {
    $service = app(MarketplaceService::class);

    $service->logSync(
        $this->account,
        'reconnect',
        'success',
        'Account reconnected successfully',
        ['attempt' => 1]
    );

    $log = MarketplaceSyncLog::query()
        ->where('tenant_id', $this->tenant->id)
        ->latest()
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->sync_type)->toBe('reconnect')
        ->and($log->status)->toBe('success')
        ->and($log->error_message)->toBe('Account reconnected successfully')
        ->and($log->payload['attempt'])->toBe(1)
        ->and($log->synced_at)->not->toBeNull();
});
