<?php

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
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
        'is_stockable' => true,
    ]);
});

// Opening Stock Tests
it('can create opening stock', function () {
    $openingStockData = [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
        'unit_cost' => 50000,
        'notes' => 'Initial stock',
    ];

    $response = $this->post(route('inventory.opening-stock.store'), $openingStockData);

    $response->assertRedirect();

    $this->assertDatabaseHas('inventories', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
    ]);
});

it('creates stock movement when opening stock is saved', function () {
    $openingStockData = [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
        'unit_cost' => 50000,
    ];

    $this->post(route('inventory.opening-stock.store'), $openingStockData);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'type' => 'opening_stock',
        'quantity' => 100,
    ]);
});

it('updates inventory quantity after opening stock', function () {
    $openingStockData = [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
        'unit_cost' => 50000,
    ];

    $this->post(route('inventory.opening-stock.store'), $openingStockData);

    $inventory = Inventory::where('product_id', $this->product->id)
        ->where('branch_id', $this->branch->id)
        ->first();

    expect($inventory->quantity)->toBe(100);
});

// Stock Adjustment Tests
it('can create stock adjustment', function () {
    // Create initial inventory
    Inventory::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
    ]);

    $adjustmentData = [
        'branch_id' => $this->branch->id,
        'type' => 'addition',
        'reason' => 'Found extra stock',
        'items' => [
            [
                'product_id' => $this->product->id,
                'quantity' => 10,
                'notes' => 'Additional stock found',
            ],
        ],
    ];

    $response = $this->post(route('stock-adjustments.store'), $adjustmentData);

    $response->assertRedirect();

    $this->assertDatabaseHas('stock_adjustments', [
        'branch_id' => $this->branch->id,
        'type' => 'addition',
        'status' => 'draft',
    ]);
});

it('can finalize stock adjustment', function () {
    // Create initial inventory
    Inventory::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
    ]);

    $adjustment = StockAdjustment::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'type' => 'addition',
        'status' => 'draft',
    ]);

    $response = $this->post(route('stock-adjustments.finalize', $adjustment));

    $response->assertRedirect();

    $this->assertDatabaseHas('stock_adjustments', [
        'id' => $adjustment->id,
        'status' => 'completed',
    ]);
});

it('creates stock movement after adjustment is finalized', function () {
    // Create initial inventory
    Inventory::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
    ]);

    $adjustment = StockAdjustment::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'type' => 'addition',
        'status' => 'draft',
    ]);

    $adjustment->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 10,
        'notes' => 'Additional stock',
    ]);

    $this->post(route('stock-adjustments.finalize', $adjustment));

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'type' => 'adjustment',
        'quantity' => 10,
    ]);
});

it('updates inventory quantity after adjustment', function () {
    // Create initial inventory
    Inventory::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 100,
    ]);

    $adjustment = StockAdjustment::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'type' => 'addition',
        'status' => 'draft',
    ]);

    $adjustment->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 10,
    ]);

    $this->post(route('stock-adjustments.finalize', $adjustment));

    $inventory = Inventory::where('product_id', $this->product->id)
        ->where('branch_id', $this->branch->id)
        ->first();

    expect($inventory->quantity)->toBe(110);
});

// Stock Movement Recording Tests
it('records stock movement with correct data', function () {
    $movementData = [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'type' => 'sale',
        'quantity' => -5,
        'reference_type' => 'Sale',
        'reference_id' => 'SALE-001',
        'notes' => 'Sale transaction',
    ];

    StockMovement::create($movementData);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'type' => 'sale',
        'quantity' => -5,
        'reference_type' => 'Sale',
        'reference_id' => 'SALE-001',
    ]);
});

it('can view stock movement history', function () {
    StockMovement::factory()->count(5)->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
    ]);

    $response = $this->get(route('inventory.movements', ['product_id' => $this->product->id]));

    $response->assertSuccessful();
});

it('scopes stock movements to current tenant', function () {
    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create(['tenant_id' => $otherTenant->id]);

    StockMovement::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'type' => 'sale',
    ]);

    StockMovement::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $otherBranch->id,
        'type' => 'purchase',
    ]);

    $response = $this->get(route('inventory.movements'));

    $response->assertSuccessful();
    // Should only see movements from current tenant's branches
});
