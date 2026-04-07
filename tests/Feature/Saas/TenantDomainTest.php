<?php

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Cache;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('middleware resolves tenant from domain', function () {
    $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'shop.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    $this->get('http://shop.test/up')
        ->assertStatus(200);

    $tenantManager = app(TenantManager::class);
    expect($tenantManager->getTenantId())->toBe($tenant->id);
    expect(app(\App\Models\Tenant::class)->id)->toBe($tenant->id);
});

test('middleware skips disabled domains', function () {
    $tenant = Tenant::factory()->create();
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'inactive.test',
        'is_active' => false,
        'is_verified' => true,
    ]);

    $this->get('http://inactive.test/up');

    $tenantManager = app(TenantManager::class);
    expect($tenantManager->getTenantId())->toBeNull();
});

test('middleware skips unverified domains', function () {
    $tenant = Tenant::factory()->create();
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'unverified.test',
        'is_active' => true,
        'is_verified' => false,
    ]);

    $this->get('http://unverified.test/up');

    $tenantManager = app(TenantManager::class);
    expect($tenantManager->getTenantId())->toBeNull();
});

test('it caches the domain resolution', function () {
    $tenant = Tenant::factory()->create();
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'cached.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    $this->get('http://cached.test/up');
    
    expect(Cache::has('tenant_domain_cached.test'))->toBeTrue();
});
