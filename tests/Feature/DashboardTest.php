<?php

use App\Livewire\Dashboard\Summary;
use App\Models\Tenant;
use App\Models\User;

test('dashboard page is displayed for verified users', function () {
    $tenant = Tenant::create([
        'name' => 'Retail Atlas',
        'code' => 'ATLAS',
        'currency' => 'IDR',
        'timezone' => 'Asia/Jakarta',
        'status' => 'active',
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response
        ->assertOk()
        ->assertSeeLivewire(Summary::class)
        ->assertSee('Retail Atlas');
});
