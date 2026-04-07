<?php

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use App\Models\Supplier;
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

    $this->supplier = Supplier::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $this->category = ProductCategory::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->unit = Unit::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'unit_id' => $this->unit->id,
        'is_stockable' => true,
    ]);
});

// Purchase Order Creation Tests
it('can create purchase order', function () {
    $poData = [
        'supplier_id' => $this->supplier->id,
        'branch_id' => $this->branch->id,
        'order_date' => now()->format('Y-m-d'),
        'expected_date' => now()->addDays(7)->format('Y-m-d'),
        'notes' => 'Test purchase order',
        'items' => [
            [
                'product_id' => $this->product->id,
                'quantity' => 10,
                'unit_price' => 50000,
                'discount' => 0,
            ],
        ],
    ];

    $response = $this->post(route('purchase-orders.store'), $poData);

    $response->assertRedirect();

    $this->assertDatabaseHas('purchase_orders', [
        'supplier_id' => $this->supplier->id,
        'branch_id' => $this->branch->id,
        'status' => 'draft',
    ]);
});

it('calculates totals correctly when creating purchase order', function () {
    $poData = [
        'supplier_id' => $this->supplier->id,
        'branch_id' => $this->branch->id,
        'order_date' => now()->format('Y-m-d'),
        'items' => [
            [
                'product_id' => $this->product->id,
                'quantity' => 10,
                'unit_price' => 50000,
                'discount' => 5000,
            ],
        ],
    ];

    $this->post(route('purchase-orders.store'), $poData);

    $po = PurchaseOrder::where('supplier_id', $this->supplier->id)->first();

    // Subtotal: 10 * 50000 = 500000
    // Discount: 5000
    // Total: 495000
    expect($po->subtotal)->toBe(500000.00);
    expect($po->discount_total)->toBe(5000.00);
    expect($po->total)->toBe(495000.00);
});

it('validates required fields when creating purchase order', function () {
    $response = $this->post(route('purchase-orders.store'), []);

    $response->assertSessionHasErrors(['supplier_id', 'branch_id', 'order_date']);
});

// Purchase Order Status Tests
it('can submit purchase order for approval', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'draft',
    ]);

    $response = $this->post(route('purchase-orders.submit', $po));

    $response->assertRedirect();

    $this->assertDatabaseHas('purchase_orders', [
        'id' => $po->id,
        'status' => 'pending_approval',
    ]);
});

it('can approve purchase order', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'pending_approval',
    ]);

    $response = $this->post(route('purchase-orders.approve', $po));

    $response->assertRedirect();

    $this->assertDatabaseHas('purchase_orders', [
        'id' => $po->id,
        'status' => 'approved',
        'approved_by' => $this->user->id,
    ]);
});

it('can reject purchase order', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'pending_approval',
    ]);

    $response = $this->post(route('purchase-orders.reject', $po), [
        'rejection_reason' => 'Budget exceeded',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('purchase_orders', [
        'id' => $po->id,
        'status' => 'rejected',
    ]);
});

// Purchase Order Receiving Tests
it('can receive purchase order items', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'approved',
    ]);

    $po->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 50000,
        'total' => 500000,
    ]);

    $receiveData = [
        'items' => [
            [
                'item_id' => $po->items->first()->id,
                'received_quantity' => 10,
                'notes' => 'All items received',
            ],
        ],
    ];

    $response = $this->post(route('purchase-orders.receive', $po), $receiveData);

    $response->assertRedirect();

    $this->assertDatabaseHas('purchase_order_items', [
        'id' => $po->items->first()->id,
        'received_quantity' => 10,
    ]);
});

it('updates inventory when purchase order is received', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'approved',
    ]);

    $po->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 50000,
        'total' => 500000,
    ]);

    $receiveData = [
        'items' => [
            [
                'item_id' => $po->items->first()->id,
                'received_quantity' => 10,
            ],
        ],
    ];

    $this->post(route('purchase-orders.receive', $po), $receiveData);

    $this->assertDatabaseHas('inventories', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'quantity' => 10,
    ]);
});

it('creates stock movement when purchase order is received', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'approved',
    ]);

    $po->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 50000,
        'total' => 500000,
    ]);

    $receiveData = [
        'items' => [
            [
                'item_id' => $po->items->first()->id,
                'received_quantity' => 10,
            ],
        ],
    ];

    $this->post(route('purchase-orders.receive', $po), $receiveData);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->product->id,
        'branch_id' => $this->branch->id,
        'type' => 'purchase',
        'quantity' => 10,
        'reference_type' => 'PurchaseOrder',
        'reference_id' => $po->id,
    ]);
});

// Purchase Payment Tests
it('can record purchase payment', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'partially_received',
        'total' => 500000,
        'paid_amount' => 0,
    ]);

    $paymentData = [
        'amount' => 250000,
        'payment_method' => 'bank_transfer',
        'payment_date' => now()->format('Y-m-d'),
        'reference_number' => 'TRF-001',
        'notes' => 'Partial payment',
    ];

    $response = $this->post(route('purchase-orders.payments.store', $po), $paymentData);

    $response->assertRedirect();

    $this->assertDatabaseHas('purchase_payments', [
        'purchase_order_id' => $po->id,
        'amount' => 250000,
        'payment_method' => 'bank_transfer',
    ]);
});

it('updates paid amount on purchase order after payment', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'partially_received',
        'total' => 500000,
        'paid_amount' => 0,
    ]);

    $paymentData = [
        'amount' => 250000,
        'payment_method' => 'bank_transfer',
        'payment_date' => now()->format('Y-m-d'),
    ];

    $this->post(route('purchase-orders.payments.store', $po), $paymentData);

    $this->assertDatabaseHas('purchase_orders', [
        'id' => $po->id,
        'paid_amount' => 250000,
    ]);
});

it('marks purchase order as paid when fully paid', function () {
    $po = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'partially_received',
        'total' => 500000,
        'paid_amount' => 0,
    ]);

    $paymentData = [
        'amount' => 500000,
        'payment_method' => 'bank_transfer',
        'payment_date' => now()->format('Y-m-d'),
    ];

    $this->post(route('purchase-orders.payments.store', $po), $paymentData);

    $this->assertDatabaseHas('purchase_orders', [
        'id' => $po->id,
        'payment_status' => 'paid',
    ]);
});

// Purchase Order Listing and Filtering Tests
it('can list purchase orders', function () {
    PurchaseOrder::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
    ]);

    $response = $this->get(route('purchase-orders.index'));

    $response->assertSuccessful();
});

it('can filter purchase orders by status', function () {
    PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'draft',
    ]);

    PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'status' => 'approved',
    ]);

    $response = $this->get(route('purchase-orders.index', ['status' => 'draft']));

    $response->assertSuccessful();
});

it('can filter purchase orders by supplier', function () {
    $otherSupplier = Supplier::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Other Supplier',
    ]);

    PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
    ]);

    PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $otherSupplier->id,
    ]);

    $response = $this->get(route('purchase-orders.index', ['supplier_id' => $this->supplier->id]));

    $response->assertSuccessful();
});

it('scopes purchase orders to current tenant', function () {
    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create(['tenant_id' => $otherTenant->id]);

    PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $this->supplier->id,
        'po_number' => 'PO-MY-TENANT',
    ]);

    PurchaseOrder::factory()->create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'supplier_id' => $this->supplier->id,
        'po_number' => 'PO-OTHER-TENANT',
    ]);

    $response = $this->get(route('purchase-orders.index'));

    $response->assertSuccessful();
    $response->assertSee('PO-MY-TENANT');
    $response->assertDontSee('PO-OTHER-TENANT');
});
