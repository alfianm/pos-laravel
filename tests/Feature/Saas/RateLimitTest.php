<?php

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Constants\SubscriptionTier;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ============================================================================
// BASIC RATE LIMITING
// ============================================================================

test('tenant-api rate limiter uses tenant id as key', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::BASIC]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'api.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // First request
    $this->get('http://api.test/api/heartbeat')
        ->assertStatus(200);

    // Make 100 more (Basic tier = 100 req/min)
    for ($i = 0; $i < 100; $i++) {
        $this->get('http://api.test/api/heartbeat');
    }

    // 102nd request should fail with 429
    $this->get('http://api.test/api/heartbeat')
        ->assertStatus(429)
        ->assertJsonStructure(['message', 'retry_after']);
});

test('rate limit is independent per tenant', function () {
    $tenant1 = Tenant::factory()->create(['tier' => SubscriptionTier::BASIC]);
    TenantDomain::create(['id' => \Illuminate\Support\Str::uuid(), 'tenant_id' => $tenant1->id, 'domain' => 't1.test', 'is_active' => true, 'is_verified' => true]);

    $tenant2 = Tenant::factory()->create(['tier' => SubscriptionTier::BASIC]);
    TenantDomain::create(['id' => \Illuminate\Support\Str::uuid(), 'tenant_id' => $tenant2->id, 'domain' => 't2.test', 'is_active' => true, 'is_verified' => true]);

    // Exhaust t1 (Basic = 100 req/min)
    for ($i = 0; $i < 101; $i++) {
        $this->get('http://t1.test/api/heartbeat');
    }
    $this->get('http://t1.test/api/heartbeat')->assertStatus(429);

    // t2 should still be fine
    $this->get('http://t2.test/api/heartbeat')->assertStatus(200);
});

// ============================================================================
// TIER-BASED RATE LIMITS
// ============================================================================

test('free tier has 30 requests per minute limit', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::FREE]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'free.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Make 30 requests
    for ($i = 0; $i < 30; $i++) {
        $this->get('http://free.test/api/heartbeat');
    }

    // 31st should be blocked
    $this->get('http://free.test/api/heartbeat')
        ->assertStatus(429);
});

test('basic tier has 100 requests per minute limit', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::BASIC]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'basic.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Make 100 requests
    for ($i = 0; $i < 100; $i++) {
        $this->get('http://basic.test/api/heartbeat');
    }

    // 101st should be blocked
    $this->get('http://basic.test/api/heartbeat')
        ->assertStatus(429);
});

test('business tier has 500 requests per minute limit', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::BUSINESS]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'business.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Make 500 requests
    for ($i = 0; $i < 500; $i++) {
        $this->get('http://business.test/api/heartbeat');
    }

    // 501st should be blocked
    $this->get('http://business.test/api/heartbeat')
        ->assertStatus(429);
});

test('enterprise tier has 2000 requests per minute limit', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::ENTERPRISE]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'enterprise.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Make 2000 requests
    for ($i = 0; $i < 2000; $i++) {
        $this->get('http://enterprise.test/api/heartbeat');
    }

    // 2001st should be blocked
    $this->get('http://enterprise.test/api/heartbeat')
        ->assertStatus(429);
});

// ============================================================================
// WEBHOOK RATE LIMITS
// ============================================================================

test('webhook endpoints have separate rate limit of 1000 per minute', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::BASIC]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'webhook.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Make 1000 webhook requests
    for ($i = 0; $i < 1000; $i++) {
        $this->postJson("http://webhook.test/api/webhook/stock-sync/{$tenant->id}", []);
    }

    // 1001st should be blocked
    $this->postJson("http://webhook.test/api/webhook/stock-sync/{$tenant->id}", [])
        ->assertStatus(429);
});

// ============================================================================
// RATE LIMIT RESPONSE HEADERS
// ============================================================================

test('rate limit responses include retry-after header', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::FREE]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'headers.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Exhaust the limit
    for ($i = 0; $i < 31; $i++) {
        $this->get('http://headers.test/api/heartbeat');
    }

    $response = $this->get('http://headers.test/api/heartbeat');

    $response->assertStatus(429)
        ->assertHeader('Retry-After')
        ->assertHeader('X-RateLimit-Limit', '30')
        ->assertHeader('X-RateLimit-Remaining', '0');
});

// ============================================================================
// RATE LIMIT BYPASS FOR INTERNAL
// ============================================================================

test('internal ips can bypass rate limits with secret header', function () {
    $tenant = Tenant::factory()->create(['tier' => SubscriptionTier::FREE]);
    TenantDomain::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'tenant_id' => $tenant->id,
        'domain' => 'internal.test',
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Make many requests with internal bypass header
    for ($i = 0; $i < 100; $i++) {
        $this->withHeaders([
            'X-Internal-Bypass' => 'internal-secret-key',
        ])->get('http://internal.test/api/heartbeat');
    }

    // Should still work (bypassed rate limit)
    $this->withHeaders([
        'X-Internal-Bypass' => 'internal-secret-key',
    ])->get('http://internal.test/api/heartbeat')
        ->assertStatus(200);
});

