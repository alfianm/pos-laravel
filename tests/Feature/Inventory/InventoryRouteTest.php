<?php

use App\Livewire\Inventory\InventoryList;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

it('displays the inventory page for authorized users', function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $tenant = Tenant::factory()->create();
    $branch = Branch::factory()->create([
        'tenant_id' => $tenant->id,
    ]);
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'active_branch_id' => $branch->id,
    ]);

    Permission::findOrCreate('view inventory', 'web');
    $user->givePermissionTo('view inventory');

    $this->actingAs($user)
        ->get(route('inventory.index'))
        ->assertSuccessful()
        ->assertSeeLivewire(InventoryList::class);
});
