<?php

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Marketplace\MarketplaceAccount;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\MarketplaceProductMap;
use App\Models\Marketplace\MarketplaceShop;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
    ]);
    $this->actingAs($this->user);

    $this->category = ProductCategory::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->unit = Unit::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'unit_id' => $this->unit->id,
    ]);

    Inventory::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
    ]);
});

// Marketplace Account Tests
it('can list marketplace accounts', function () {
    MarketplaceAccount::factory()->count(3)->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->get(route('marketplace.accounts.index'));

    $response->assertSuccessful();
});

it('can create marketplace account', function () {
    $accountData = [
        'name' => 'Tokopedia Main',
        'platform' => 'tokopedia',
        'api_key' => 'test_api_key_123',
        'api_secret' => 'test_secret_456',
        'shop_id' => 'shop_789',
        'is_active' => true,
    ];

    $response = $this->post(route('marketplace.accounts.store'), $accountData);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_accounts', [
        'tenant_id' => $this->tenant->id,
        'name' => 'Tokopedia Main',
        'platform' => 'tokopedia',
    ]);
});

it('validates required fields for marketplace account', function () {
    $response = $this->post(route('marketplace.accounts.store'), []);

    $response->assertSessionHasErrors(['name', 'platform']);
});

it('can update marketplace account', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Old Account',
    ]);

    $response = $this->put(route('marketplace.accounts.update', $account), [
        'name' => 'Updated Account',
        'platform' => 'shopee',
        'api_key' => 'new_key',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_accounts', [
        'id' => $account->id,
        'name' => 'Updated Account',
        'platform' => 'shopee',
    ]);
});

it('can delete marketplace account', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->delete(route('marketplace.accounts.destroy', $account));

    $response->assertRedirect();

    $this->assertDatabaseMissing('marketplace_accounts', [
        'id' => $account->id,
    ]);
});

it('supports multiple marketplace platforms', function () {
    $platforms = ['tokopedia', 'shopee', 'lazada', 'blibli', 'bukalapak'];

    foreach ($platforms as $platform) {
        MarketplaceAccount::factory()->create([
            'tenant_id' => $this->tenant->id,
            'platform' => $platform,
        ]);
    }

    $response = $this->get(route('marketplace.accounts.index'));

    $response->assertSuccessful();
});

// Marketplace Shop Tests
it('can list marketplace shops', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    MarketplaceShop::factory()->count(3)->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
    ]);

    $response = $this->get(route('marketplace.shops.index'));

    $response->assertSuccessful();
});

it('can create marketplace shop', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $shopData = [
        'account_id' => $account->id,
        'name' => 'Main Shop',
        'external_shop_id' => 'ext_shop_123',
        'branch_id' => $this->branch->id,
        'is_active' => true,
    ];

    $response = $this->post(route('marketplace.shops.store'), $shopData);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_shops', [
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'name' => 'Main Shop',
    ]);
});

it('can update marketplace shop', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $shop = MarketplaceShop::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'name' => 'Old Shop',
    ]);

    $response = $this->put(route('marketplace.shops.update', $shop), [
        'name' => 'Updated Shop',
        'is_active' => false,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_shops', [
        'id' => $shop->id,
        'name' => 'Updated Shop',
        'is_active' => false,
    ]);
});

// Product Mapping Tests
it('can map product to marketplace', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $mapData = [
        'product_id' => $this->product->id,
        'account_id' => $account->id,
        'external_product_id' => 'ext_prod_123',
        'external_sku' => 'SKU-EXT-001',
        'is_active' => true,
    ];

    $response = $this->post(route('marketplace.product-maps.store'), $mapData);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_product_maps', [
        'tenant_id' => $this->tenant->id,
        'product_id' => $this->product->id,
        'external_product_id' => 'ext_prod_123',
    ]);
});

it('can list product mappings', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    MarketplaceProductMap::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
        'product_id' => $this->product->id,
        'account_id' => $account->id,
    ]);

    $response = $this->get(route('marketplace.product-maps.index'));

    $response->assertSuccessful();
});

it('prevents duplicate product mapping for same account', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    MarketplaceProductMap::factory()->create([
        'tenant_id' => $this->tenant->id,
        'product_id' => $this->product->id,
        'account_id' => $account->id,
        'external_product_id' => 'ext_123',
    ]);

    $response = $this->post(route('marketplace.product-maps.store'), [
        'product_id' => $this->product->id,
        'account_id' => $account->id,
        'external_product_id' => 'ext_456',
    ]);

    $response->assertSessionHasErrors();
});

// Marketplace Order Tests
it('can list marketplace orders', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    MarketplaceOrder::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
    ]);

    $response = $this->get(route('marketplace.orders.index'));

    $response->assertSuccessful();
});

it('can view marketplace order details', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $order = MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'external_order_id' => 'ORDER-123',
    ]);

    $response = $this->get(route('marketplace.orders.show', $order));

    $response->assertSuccessful();
    $response->assertSee('ORDER-123');
});

it('can sync marketplace orders', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'platform' => 'tokopedia',
    ]);

    $response = $this->post(route('marketplace.orders.sync'), [
        'account_id' => $account->id,
        'start_date' => now()->subDays(7)->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d'),
    ]);

    $response->assertRedirect();
});

it('can update marketplace order status', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $order = MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'status' => 'pending',
    ]);

    $response = $this->put(route('marketplace.orders.update-status', $order), [
        'status' => 'processing',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_orders', [
        'id' => $order->id,
        'status' => 'processing',
    ]);
});

it('can filter orders by status', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'status' => 'pending',
        'external_order_id' => 'PENDING-001',
    ]);

    MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'status' => 'completed',
        'external_order_id' => 'COMPLETED-001',
    ]);

    $response = $this->get(route('marketplace.orders.index', ['status' => 'pending']));

    $response->assertSuccessful();
    $response->assertSee('PENDING-001');
    $response->assertDontSee('COMPLETED-001');
});

it('can filter orders by marketplace account', function () {
    $account1 = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Account 1',
    ]);

    $account2 = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Account 2',
    ]);

    MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account1->id,
        'external_order_id' => 'ORDER-ACC1',
    ]);

    MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account2->id,
        'external_order_id' => 'ORDER-ACC2',
    ]);

    $response = $this->get(route('marketplace.orders.index', ['account_id' => $account1->id]));

    $response->assertSuccessful();
    $response->assertSee('ORDER-ACC1');
    $response->assertDontSee('ORDER-ACC2');
});

it('scopes marketplace data to current tenant', function () {
    $otherTenant = Tenant::factory()->create();

    $myAccount = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'My Account',
    ]);

    MarketplaceAccount::factory()->create([
        'tenant_id' => $otherTenant->id,
        'name' => 'Other Account',
    ]);

    $response = $this->get(route('marketplace.accounts.index'));

    $response->assertSuccessful();
    $response->assertSee('My Account');
    $response->assertDontSee('Other Account');
});

it('can process marketplace order to local sale', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $order = MarketplaceOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'status' => 'pending',
        'external_order_id' => 'MP-ORDER-001',
        'total_amount' => 500000,
    ]);

    $response = $this->post(route('marketplace.orders.process', $order));

    $response->assertRedirect();

    // Should create local sale record
    $this->assertDatabaseHas('sales', [
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'total' => 500000,
        'source' => 'marketplace',
    ]);
});

it('can sync product stock to marketplace', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'platform' => 'shopee',
    ]);

    MarketplaceProductMap::factory()->create([
        'tenant_id' => $this->tenant->id,
        'product_id' => $this->product->id,
        'account_id' => $account->id,
        'external_product_id' => 'SHOPEE-PROD-123',
    ]);

    $response = $this->post(route('marketplace.products.sync-stock'), [
        'product_ids' => [$this->product->id],
    ]);

    $response->assertRedirect();
});

it('tracks marketplace sync logs', function () {
    $account = MarketplaceAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->post(route('marketplace.sync'), [
        'account_id' => $account->id,
        'sync_type' => 'orders',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('marketplace_sync_logs', [
        'tenant_id' => $this->tenant->id,
        'account_id' => $account->id,
        'sync_type' => 'orders',
    ]);
});
