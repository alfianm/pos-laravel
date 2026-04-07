<?php

use App\Models\Branch;
use App\Models\CashRegisterSession;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
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
        'active_branch_id' => $this->branch->id,
    ]);
    $this->actingAs($this->user);

    $this->category = ProductCategory::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->unit = Unit::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'unit_id' => $this->unit->id,
        'track_stock' => true,
        'selling_price' => 100000,
    ]);

    // Create inventory
    Inventory::factory()->create([
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'qty_on_hand' => 100,
        'qty_available' => 100,
    ]);

    $this->customer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
});

// Cash Register Tests
it('can open cash register', function () {
    $openData = [
        'opening_cash' => 500000,
        'notes' => 'Opening balance',
    ];

    $response = $this->post(route('cash-register.open'), $openData);

    $response->assertRedirect();

    $this->assertDatabaseHas('cash_register_sessions', [
        'branch_id' => $this->branch->id,
        'user_id' => $this->user->id,
        'opening_cash' => 500000,
        'status' => 'open',
    ]);
});

it('prevents opening new register when one is already open', function () {
    CashRegisterSession::factory()->create([
        'branch_id' => $this->branch->id,
        'user_id' => $this->user->id,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $response = $this->post(route('cash-register.open'), [
        'opening_cash' => 500000,
    ]);

    $response->assertSessionHasErrors();
});

it('can close cash register', function () {
    $session = CashRegisterSession::factory()->create([
        'branch_id' => $this->branch->id,
        'user_id' => $this->user->id,
        'status' => 'open',
        'opening_cash' => 500000,
        'opened_at' => now(),
    ]);

    $closeData = [
        'closing_cash' => 750000,
        'notes' => 'Closing balance',
    ];

    $response = $this->post(route('cash-register.close'), $closeData);

    $response->assertRedirect();

    $this->assertDatabaseHas('cash_register_sessions', [
        'id' => $session->id,
        'status' => 'closed',
        'closing_cash' => 750000,
    ]);
});

// POS Sale Tests
it('can create sale from pos', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 2,
                'unit_price' => 100000,
                'discount_amount' => 0,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 200000,
            ],
        ],
    ];

    $response = $this->post(route('pos.sale.store'), $saleData);

    $response->assertRedirect();

    $this->assertDatabaseHas('sales', [
        'customer_id' => $this->customer->id,
        'branch_id' => $this->branch->id,
        'grand_total' => 200000,
        'status' => 'completed',
    ]);
});

it('calculates sale totals correctly', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 3,
                'unit_price' => 100000,
                'discount_amount' => 10000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 290000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $sale = Sale::where('customer_id', $this->customer->id)->first();

    // Subtotal: 3 * 100000 = 300000
    // Discount: 10000
    // Total: 290000
    expect((float) $sale->subtotal)->toBe(300000.00);
    expect((float) $sale->discount_amount)->toBe(10000.00);
    expect((float) $sale->grand_total)->toBe(290000.00);
});

it('creates sale items when sale is created', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 2,
                'unit_price' => 100000,
                'discount_amount' => 0,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 200000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $sale = Sale::where('customer_id', $this->customer->id)->first();

    $this->assertDatabaseHas('sale_items', [
        'sale_id' => $sale->id,
        'product_id' => $this->product->id,
        'qty' => 2,
        'unit_price' => 100000,
        'line_total' => 200000,
    ]);
});

it('creates sale payments when sale is created', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 2,
                'unit_price' => 100000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 200000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $sale = Sale::where('customer_id', $this->customer->id)->first();

    $this->assertDatabaseHas('sale_payments', [
        'sale_id' => $sale->id,
        'payment_method' => 'cash',
        'amount' => 200000,
    ]);
});

it('updates inventory when sale is created', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 5,
                'unit_price' => 100000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 500000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $this->assertDatabaseHas('inventories', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'qty_on_hand' => 95, // 100 - 5
    ]);
});

it('creates stock movement when sale is created', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 5,
                'unit_price' => 100000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 500000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $sale = Sale::where('customer_id', $this->customer->id)->first();

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'movement_type' => 'sale',
        'qty' => -5,
        'reference_type' => 'Sale',
        'reference_id' => $sale->id,
    ]);
});

it('validates sufficient stock before creating sale', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 200, // More than available stock
                'unit_price' => 100000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 20000000,
            ],
        ],
    ];

    $response = $this->post(route('pos.sale.store'), $saleData);

    $response->assertSessionHasErrors();
});

it('validates payment amount matches total', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 2,
                'unit_price' => 100000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 100000, // Less than total (200000)
            ],
        ],
    ];

    $response = $this->post(route('pos.sale.store'), $saleData);

    $response->assertSessionHasErrors();
});

it('supports multiple payment methods', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 3,
                'unit_price' => 100000,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 150000,
            ],
            [
                'payment_method' => 'debit_card',
                'amount' => 150000,
            ],
        ],
    ];

    $response = $this->post(route('pos.sale.store'), $saleData);

    $response->assertRedirect();

    $sale = Sale::where('customer_id', $this->customer->id)->first();

    $this->assertDatabaseHas('sale_payments', [
        'sale_id' => $sale->id,
        'payment_method' => 'cash',
        'amount' => 150000,
    ]);

    $this->assertDatabaseHas('sale_payments', [
        'sale_id' => $sale->id,
        'payment_method' => 'debit_card',
        'amount' => 150000,
    ]);
});

it('can search products in pos', function () {
    $response = $this->get(route('pos.products.search', ['query' => $this->product->name]));

    $response->assertSuccessful();
    $response->assertJsonFragment(['name' => $this->product->name]);
});

it('returns product with stock information in search', function () {
    $response = $this->get(route('pos.products.search', ['query' => $this->product->name]));

    $response->assertSuccessful();
    $response->assertJsonFragment(['qty_available' => "100.0000"]);
});

it('can generate sale receipt', function () {
    $sale = Sale::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'customer_id' => $this->customer->id,
        'grand_total' => 200000,
        'status' => 'completed',
        'sale_date' => now(),
        'sale_no' => 'SALE-123'
    ]);

    $sale->items()->create([
        'tenant_id' => $this->tenant->id,
        'product_id' => $this->product->id,
        'qty' => 2,
        'unit_price' => 100000,
        'line_total' => 200000,
    ]);

    $response = $this->get(route('pos.receipt', $sale));

    $response->assertSuccessful();
    $response->assertSee($sale->sale_no);
});

it('scopes pos sales to current branch', function () {
    $otherBranch = Branch::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Other Branch',
    ]);

    Sale::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'sale_no' => 'SALE-MY-BRANCH',
        'sale_date' => now(),
    ]);

    Sale::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $otherBranch->id,
        'sale_no' => 'SALE-OTHER-BRANCH',
        'sale_date' => now(),
    ]);

    $response = $this->get(route('sales.index'));

    $response->assertSuccessful();
    $response->assertSee('SALE-MY-BRANCH');
    $response->assertDontSee('SALE-OTHER-BRANCH');
});

it('creates customer timeline entry when sale is created', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 2,
                'unit_price' => 100000,
                'discount_amount' => 0,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 200000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $sale = Sale::where('customer_id', $this->customer->id)->first();

    $this->assertDatabaseHas('customer_timelines', [
        'customer_id' => $this->customer->id,
        'type' => 'sale',
        'sale_id' => $sale->id,
    ]);
});

it('stores correct metadata in customer timeline entry', function () {
    $saleData = [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'qty' => 2,
                'unit_price' => 100000,
                'discount_amount' => 0,
            ],
        ],
        'payments' => [
            [
                'payment_method' => 'cash',
                'amount' => 200000,
            ],
        ],
    ];

    $this->post(route('pos.sale.store'), $saleData);

    $sale = Sale::where('customer_id', $this->customer->id)->first();
    $timeline = \App\Models\CustomerTimeline::where('sale_id', $sale->id)->first();

    expect($timeline)->not->toBeNull();
    expect($timeline->type)->toBe('sale');
    expect($timeline->metadata)->toHaveKey('sale_no');
    expect($timeline->metadata)->toHaveKey('grand_total');
    expect($timeline->metadata)->toHaveKey('payment_method');
    expect($timeline->metadata)->toHaveKey('items');
    expect($timeline->metadata['grand_total'])->toBe(200000.0);
    expect($timeline->metadata['payment_method'])->toBe('cash');
});
