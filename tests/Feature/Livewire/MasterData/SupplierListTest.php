<?php

use App\Livewire\MasterData\SupplierList;
use App\Models\Branch;
use App\Models\Supplier;
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

it('renders only supplier data from the authenticated tenant', function (): void {
    $visibleSupplier = Supplier::create([
        'tenant_id' => $this->tenant->id,
        'code' => 'VND-TENANT-001',
        'name' => 'Visible Supplier',
        'contact_person' => 'Visible PIC',
        'status' => 'active',
    ]);

    $otherTenant = Tenant::factory()->create();

    Supplier::create([
        'tenant_id' => $otherTenant->id,
        'code' => 'VND-TENANT-002',
        'name' => 'Hidden Supplier',
        'contact_person' => 'Hidden PIC',
        'status' => 'active',
    ]);

    Livewire::test(SupplierList::class)
        ->assertStatus(200)
        ->assertSee($visibleSupplier->name)
        ->assertDontSee('Hidden Supplier');
});

it('creates a supplier from the livewire form', function (): void {
    Livewire::test(SupplierList::class)
        ->call('openCreateModal')
        ->set('name', 'PT Supplier Baru')
        ->set('code', 'VND-CREATE-001')
        ->set('email', 'vendor@example.test')
        ->set('phone', '0211234567')
        ->set('contact_person', 'Andi Vendor')
        ->set('address', 'Jl. Industri No. 10')
        ->set('city', 'Jakarta')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $supplier = Supplier::query()->where('code', 'VND-CREATE-001')->first();

    expect($supplier)->not->toBeNull()
        ->and($supplier->tenant_id)->toBe($this->tenant->id)
        ->and($supplier->name)->toBe('PT Supplier Baru')
        ->and($supplier->email)->toBe('vendor@example.test')
        ->and($supplier->phone)->toBe('0211234567')
        ->and($supplier->contact_person)->toBe('Andi Vendor')
        ->and($supplier->address)->toBe('Jl. Industri No. 10')
        ->and($supplier->city)->toBe('Jakarta')
        ->and($supplier->status)->toBe('active');
});

it('updates an existing supplier from the livewire form', function (): void {
    $supplier = Supplier::create([
        'tenant_id' => $this->tenant->id,
        'code' => 'VND-UPDATE-001',
        'name' => 'Old Supplier',
        'email' => 'old-supplier@example.test',
        'phone' => '0217654321',
        'contact_person' => 'Old PIC',
        'address' => 'Jl. Lama No. 1',
        'city' => 'Bogor',
        'status' => 'active',
    ]);

    Livewire::test(SupplierList::class)
        ->call('openEditModal', $supplier->id)
        ->set('name', 'Updated Supplier')
        ->set('email', 'updated-supplier@example.test')
        ->set('phone', '0228888888')
        ->set('contact_person', 'New PIC')
        ->set('address', 'Jl. Baru No. 2')
        ->set('city', 'Bandung')
        ->set('status', 'inactive')
        ->call('save')
        ->assertHasNoErrors();

    $supplier->refresh();

    expect($supplier->name)->toBe('Updated Supplier')
        ->and($supplier->email)->toBe('updated-supplier@example.test')
        ->and($supplier->phone)->toBe('0228888888')
        ->and($supplier->contact_person)->toBe('New PIC')
        ->and($supplier->address)->toBe('Jl. Baru No. 2')
        ->and($supplier->city)->toBe('Bandung')
        ->and($supplier->status)->toBe('inactive');
});

it('soft deletes a supplier from the livewire list', function (): void {
    $supplier = Supplier::create([
        'tenant_id' => $this->tenant->id,
        'code' => 'VND-DELETE-001',
        'name' => 'Delete Supplier',
        'contact_person' => 'Delete PIC',
        'status' => 'active',
    ]);

    Livewire::test(SupplierList::class)
        ->call('delete', $supplier->id);

    $deletedSupplier = Supplier::withTrashed()->find($supplier->id);

    expect($deletedSupplier)->not->toBeNull()
        ->and($deletedSupplier->trashed())->toBeTrue();
});
