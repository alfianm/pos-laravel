<?php

use App\Livewire\MasterData\CustomerList;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    $this->actingAs($this->user);
});

it('renders only customer data from the authenticated tenant', function (): void {
    $visibleCustomer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'CST-TENANT-001',
        'name' => 'Visible Customer',
    ]);

    $otherTenant = Tenant::factory()->create();

    Customer::factory()->create([
        'tenant_id' => $otherTenant->id,
        'code' => 'CST-TENANT-002',
        'name' => 'Hidden Customer',
    ]);

    Livewire::test(CustomerList::class)
        ->assertStatus(200)
        ->assertSee($visibleCustomer->name)
        ->assertDontSee('Hidden Customer');
});

it('creates a customer from the livewire form', function (): void {
    $group = CustomerGroup::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Retail VIP',
        'discount_percentage' => 10,
    ]);

    Livewire::test(CustomerList::class)
        ->call('openCreateModal')
        ->set('name', 'Budi Santoso')
        ->set('code', 'CST-CREATE-001')
        ->set('customer_group_id', $group->id)
        ->set('email', 'budi@example.test')
        ->set('phone', '081111111111')
        ->set('address', 'Jl. Mawar No. 1')
        ->set('city', 'Jakarta')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $customer = Customer::query()->where('code', 'CST-CREATE-001')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->tenant_id)->toBe($this->tenant->id)
        ->and($customer->customer_group_id)->toBe($group->id)
        ->and($customer->name)->toBe('Budi Santoso')
        ->and($customer->email)->toBe('budi@example.test')
        ->and($customer->phone)->toBe('081111111111')
        ->and($customer->city)->toBe('Jakarta')
        ->and($customer->status)->toBe('active');
});

it('updates an existing customer from the livewire form', function (): void {
    $initialGroup = CustomerGroup::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Retail',
        'discount_percentage' => 0,
    ]);
    $updatedGroup = CustomerGroup::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Wholesale',
        'discount_percentage' => 5,
    ]);
    $customer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'customer_group_id' => $initialGroup->id,
        'code' => 'CST-UPDATE-001',
        'name' => 'Old Customer',
        'email' => 'old-customer@example.test',
        'phone' => '081222222222',
        'city' => 'Bogor',
        'status' => 'active',
    ]);

    Livewire::test(CustomerList::class)
        ->call('openEditModal', $customer->id)
        ->set('name', 'Updated Customer')
        ->set('email', 'updated-customer@example.test')
        ->set('phone', '082333333333')
        ->set('customer_group_id', $updatedGroup->id)
        ->set('address', 'Jl. Melati No. 2')
        ->set('city', 'Bandung')
        ->set('status', 'inactive')
        ->call('save')
        ->assertHasNoErrors();

    $customer->refresh();

    expect($customer->name)->toBe('Updated Customer')
        ->and($customer->email)->toBe('updated-customer@example.test')
        ->and($customer->phone)->toBe('082333333333')
        ->and($customer->customer_group_id)->toBe($updatedGroup->id)
        ->and($customer->address)->toBe('Jl. Melati No. 2')
        ->and($customer->city)->toBe('Bandung')
        ->and($customer->status)->toBe('inactive');
});

it('soft deletes a customer from the livewire list', function (): void {
    $customer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'CST-DELETE-001',
        'name' => 'Delete Customer',
    ]);

    Livewire::test(CustomerList::class)
        ->call('delete', $customer->id);

    $deletedCustomer = Customer::withTrashed()->find($customer->id);

    expect($deletedCustomer)->not->toBeNull()
        ->and($deletedCustomer->trashed())->toBeTrue();
});
