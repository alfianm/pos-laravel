<?php

use App\Livewire\Reports\PurchaseSummaryReport;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
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

    $this->actingAs($this->user);
});

it('renders the purchase summary report page without referencing missing payment columns', function (): void {
    $supplier = Supplier::create([
        'tenant_id' => $this->tenant->id,
        'code' => 'SUP-RPT-001',
        'name' => 'Supplier Report',
        'status' => 'active',
    ]);

    PurchaseOrder::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $supplier->id,
        'purchase_no' => 'PO-RPT-001',
        'order_date' => now()->subDay()->toDateString(),
        'status' => 'submitted',
        'subtotal' => 100000,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'shipping_amount' => 0,
        'grand_total' => 100000,
        'payment_status' => 'unpaid',
        'created_by' => $this->user->id,
    ]);

    $this->get(route('reports.purchases.summary'))
        ->assertSuccessful()
        ->assertSee('Laporan Pembelian');
});

it('summarizes purchase totals and payments from purchase payments only for the active tenant', function (): void {
    $supplierA = Supplier::create([
        'tenant_id' => $this->tenant->id,
        'code' => 'SUP-RPT-101',
        'name' => 'Supplier A',
        'status' => 'active',
    ]);
    $supplierB = Supplier::create([
        'tenant_id' => $this->tenant->id,
        'code' => 'SUP-RPT-102',
        'name' => 'Supplier B',
        'status' => 'active',
    ]);

    $purchaseOrderA = PurchaseOrder::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $supplierA->id,
        'purchase_no' => 'PO-RPT-101',
        'order_date' => now()->subDays(2)->toDateString(),
        'status' => 'submitted',
        'subtotal' => 200000,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'shipping_amount' => 0,
        'grand_total' => 200000,
        'payment_status' => 'partial',
        'created_by' => $this->user->id,
    ]);

    $purchaseOrderB = PurchaseOrder::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'supplier_id' => $supplierB->id,
        'purchase_no' => 'PO-RPT-102',
        'order_date' => now()->subDay()->toDateString(),
        'status' => 'completed',
        'subtotal' => 50000,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'shipping_amount' => 0,
        'grand_total' => 50000,
        'payment_status' => 'paid',
        'created_by' => $this->user->id,
    ]);

    PurchasePayment::create([
        'tenant_id' => $this->tenant->id,
        'purchase_order_id' => $purchaseOrderA->id,
        'branch_id' => $this->branch->id,
        'payment_no' => 'PAY-RPT-101',
        'payment_date' => now()->subDay(),
        'amount' => 75000,
        'payment_method' => 'transfer',
        'created_by' => $this->user->id,
    ]);

    PurchasePayment::create([
        'tenant_id' => $this->tenant->id,
        'purchase_order_id' => $purchaseOrderB->id,
        'branch_id' => $this->branch->id,
        'payment_no' => 'PAY-RPT-102',
        'payment_date' => now()->subDay(),
        'amount' => 50000,
        'payment_method' => 'transfer',
        'created_by' => $this->user->id,
    ]);

    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create([
        'tenant_id' => $otherTenant->id,
    ]);
    $otherUser = User::factory()->create([
        'tenant_id' => $otherTenant->id,
        'active_branch_id' => $otherBranch->id,
    ]);
    $otherSupplier = Supplier::create([
        'tenant_id' => $otherTenant->id,
        'code' => 'SUP-RPT-999',
        'name' => 'Other Tenant Supplier',
        'status' => 'active',
    ]);
    $otherPurchaseOrder = PurchaseOrder::create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'supplier_id' => $otherSupplier->id,
        'purchase_no' => 'PO-RPT-999',
        'order_date' => now()->subDay()->toDateString(),
        'status' => 'completed',
        'subtotal' => 999999,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'shipping_amount' => 0,
        'grand_total' => 999999,
        'payment_status' => 'paid',
        'created_by' => $otherUser->id,
    ]);

    PurchasePayment::create([
        'tenant_id' => $otherTenant->id,
        'purchase_order_id' => $otherPurchaseOrder->id,
        'branch_id' => $otherBranch->id,
        'payment_no' => 'PAY-RPT-999',
        'payment_date' => now()->subDay(),
        'amount' => 999999,
        'payment_method' => 'transfer',
        'created_by' => $otherUser->id,
    ]);

    $component = Livewire::test(PurchaseSummaryReport::class)
        ->set('date_from', now()->subDays(7)->format('Y-m-d'))
        ->set('date_to', now()->addDay()->format('Y-m-d'));

    $grandTotals = $component->get('grandTotals');
    $supplierSummary = $component->get('supplierSummary');

    expect((int) $grandTotals->total_orders)->toBe(2)
        ->and((float) $grandTotals->total_amount)->toBe(250000.0)
        ->and((float) $grandTotals->total_paid)->toBe(125000.0)
        ->and((float) $grandTotals->total_due)->toBe(125000.0)
        ->and($supplierSummary)->toHaveCount(2)
        ->and($supplierSummary->first()->supplier->name)->toBe('Supplier A');

    $component->assertSee('Supplier A')
        ->assertSee('Supplier B')
        ->assertDontSee('Other Tenant Supplier');
});
