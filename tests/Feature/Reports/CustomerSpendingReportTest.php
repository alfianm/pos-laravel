<?php

use App\Livewire\Reports\CustomerSpendingReport;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Sale;
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

it('renders the customer spending report page for authorized users without postgres grouping errors', function (): void {
    $customer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'name' => 'Customer Report',
    ]);

    Sale::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'customer_id' => $customer->id,
        'sale_no' => 'SAL-CSR-001',
        'sale_date' => now()->subDay(),
        'status' => 'completed',
        'payment_status' => 'paid',
        'subtotal' => 100000,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'grand_total' => 100000,
        'paid_amount' => 100000,
        'due_amount' => 0,
    ]);

    $this->get(route('reports.customers.spending'))
        ->assertSuccessful()
        ->assertSee('Laporan Spending Customer');
});

it('summarizes customer spending totals for the active tenant', function (): void {
    $customerA = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'name' => 'Customer A',
    ]);
    $customerB = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'name' => 'Customer B',
    ]);

    Sale::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'customer_id' => $customerA->id,
        'sale_no' => 'SAL-CSR-101',
        'sale_date' => now()->subDays(2),
        'status' => 'completed',
        'payment_status' => 'paid',
        'subtotal' => 150000,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'grand_total' => 150000,
        'paid_amount' => 150000,
        'due_amount' => 0,
    ]);

    Sale::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'customer_id' => $customerB->id,
        'sale_no' => 'SAL-CSR-102',
        'sale_date' => now()->subDay(),
        'status' => 'completed',
        'payment_status' => 'paid',
        'subtotal' => 50000,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'grand_total' => 50000,
        'paid_amount' => 50000,
        'due_amount' => 0,
    ]);

    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create([
        'tenant_id' => $otherTenant->id,
    ]);
    $otherCustomer = Customer::factory()->create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'name' => 'Other Tenant Customer',
    ]);

    Sale::create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'customer_id' => $otherCustomer->id,
        'sale_no' => 'SAL-CSR-999',
        'sale_date' => now()->subDay(),
        'status' => 'completed',
        'payment_status' => 'paid',
        'subtotal' => 999999,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'grand_total' => 999999,
        'paid_amount' => 999999,
        'due_amount' => 0,
    ]);

    $component = Livewire::test(CustomerSpendingReport::class)
        ->set('date_from', now()->subDays(7)->format('Y-m-d'))
        ->set('date_to', now()->addDay()->format('Y-m-d'));

    $allTransactions = $component->get('allTransactions');
    $topCustomers = $component->get('topCustomers');

    expect((int) $allTransactions->total_transactions)->toBe(2)
        ->and((float) $allTransactions->total_spent)->toBe(200000.0)
        ->and($topCustomers)->toHaveCount(2)
        ->and($topCustomers->first()->customer->name)->toBe('Customer A');

    $component->assertSee('Customer A')
        ->assertSee('Customer B')
        ->assertDontSee('Other Tenant Customer');
});
