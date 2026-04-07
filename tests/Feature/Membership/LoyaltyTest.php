<?php

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTier;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Voucher;
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

    $this->customer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $this->tier = LoyaltyTier::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Gold',
        'min_points' => 1000,
        'discount_percent' => 10,
    ]);
});

// Loyalty Account Tests
it('creates loyalty account for customer automatically', function () {
    $customer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $this->assertDatabaseHas('loyalty_accounts', [
        'customer_id' => $customer->id,
        'tenant_id' => $this->tenant->id,
        'points_balance' => 0,
    ]);
});

it('can view loyalty account details', function () {
    $account = LoyaltyAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'customer_id' => $this->customer->id,
        'points_balance' => 500,
    ]);

    $response = $this->get(route('loyalty.accounts.show', $account));

    $response->assertSuccessful();
    $response->assertSee('500');
});

it('can add points to loyalty account', function () {
    $account = LoyaltyAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'customer_id' => $this->customer->id,
        'points_balance' => 100,
    ]);

    $response = $this->post(route('loyalty.accounts.add-points', $account), [
        'points' => 50,
        'reason' => 'Purchase bonus',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('loyalty_accounts', [
        'id' => $account->id,
        'points_balance' => 150,
    ]);
});

it('can redeem points from loyalty account', function () {
    $account = LoyaltyAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'customer_id' => $this->customer->id,
        'points_balance' => 500,
    ]);

    $response = $this->post(route('loyalty.accounts.redeem-points', $account), [
        'points' => 100,
        'reason' => 'Redemption',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('loyalty_accounts', [
        'id' => $account->id,
        'points_balance' => 400,
    ]);
});

it('prevents redeeming more points than available', function () {
    $account = LoyaltyAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'customer_id' => $this->customer->id,
        'points_balance' => 50,
    ]);

    $response = $this->post(route('loyalty.accounts.redeem-points', $account), [
        'points' => 100,
        'reason' => 'Redemption',
    ]);

    $response->assertSessionHasErrors();
});

// Loyalty Tier Tests
it('can list loyalty tiers', function () {
    LoyaltyTier::factory()->count(3)->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->get(route('loyalty.tiers.index'));

    $response->assertSuccessful();
});

it('can create loyalty tier', function () {
    $tierData = [
        'name' => 'Platinum',
        'min_points' => 5000,
        'discount_percent' => 15,
        'description' => 'Premium tier',
    ];

    $response = $this->post(route('loyalty.tiers.store'), $tierData);

    $response->assertRedirect();

    $this->assertDatabaseHas('loyalty_tiers', [
        'tenant_id' => $this->tenant->id,
        'name' => 'Platinum',
        'min_points' => 5000,
    ]);
});

it('can update loyalty tier', function () {
    $tier = LoyaltyTier::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Silver',
    ]);

    $response = $this->put(route('loyalty.tiers.update', $tier), [
        'name' => 'Silver Plus',
        'min_points' => 2000,
        'discount_percent' => 8,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('loyalty_tiers', [
        'id' => $tier->id,
        'name' => 'Silver Plus',
    ]);
});

it('can delete loyalty tier', function () {
    $tier = LoyaltyTier::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->delete(route('loyalty.tiers.destroy', $tier));

    $response->assertRedirect();

    $this->assertDatabaseMissing('loyalty_tiers', [
        'id' => $tier->id,
    ]);
});

it('automatically upgrades customer tier based on points', function () {
    $account = LoyaltyAccount::factory()->create([
        'tenant_id' => $this->tenant->id,
        'customer_id' => $this->customer->id,
        'points_balance' => 500,
        'tier_id' => null,
    ]);

    // Add points to reach Gold tier (1000 min_points)
    $this->post(route('loyalty.accounts.add-points', $account), [
        'points' => 600,
        'reason' => 'Purchase bonus',
    ]);

    $this->assertDatabaseHas('loyalty_accounts', [
        'id' => $account->id,
        'tier_id' => $this->tier->id,
    ]);
});

// Voucher Tests
it('can create voucher', function () {
    $voucherData = [
        'code' => 'DISCOUNT10',
        'type' => 'percentage',
        'value' => 10,
        'min_purchase' => 100000,
        'max_discount' => 50000,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'usage_limit' => 100,
    ];

    $response = $this->post(route('vouchers.store'), $voucherData);

    $response->assertRedirect();

    $this->assertDatabaseHas('vouchers', [
        'tenant_id' => $this->tenant->id,
        'code' => 'DISCOUNT10',
        'type' => 'percentage',
        'value' => 10,
    ]);
});

it('can list vouchers', function () {
    Voucher::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->get(route('vouchers.index'));

    $response->assertSuccessful();
});

it('can update voucher', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'OLD10',
    ]);

    $response = $this->put(route('vouchers.update', $voucher), [
        'code' => 'NEW10',
        'type' => 'percentage',
        'value' => 15,
        'min_purchase' => 150000,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('vouchers', [
        'id' => $voucher->id,
        'code' => 'NEW10',
        'value' => 15,
    ]);
});

it('can delete voucher', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->delete(route('vouchers.destroy', $voucher));

    $response->assertRedirect();

    $this->assertDatabaseMissing('vouchers', [
        'id' => $voucher->id,
    ]);
});

it('validates voucher code uniqueness', function () {
    Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'UNIQUE10',
    ]);

    $response = $this->post(route('vouchers.store'), [
        'code' => 'UNIQUE10',
        'type' => 'percentage',
        'value' => 10,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors('code');
});

it('can validate voucher for use', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'VALID10',
        'type' => 'percentage',
        'value' => 10,
        'min_purchase' => 100000,
        'start_date' => now()->subDay()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'is_active' => true,
    ]);

    $response = $this->post(route('vouchers.validate'), [
        'code' => 'VALID10',
        'purchase_amount' => 150000,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['valid' => true]);
});

it('rejects invalid voucher', function () {
    $response = $this->post(route('vouchers.validate'), [
        'code' => 'INVALIDCODE',
        'purchase_amount' => 150000,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['valid' => false]);
});

it('rejects expired voucher', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'EXPIRED10',
        'type' => 'percentage',
        'value' => 10,
        'start_date' => now()->subDays(30)->format('Y-m-d'),
        'end_date' => now()->subDay()->format('Y-m-d'),
        'is_active' => true,
    ]);

    $response = $this->post(route('vouchers.validate'), [
        'code' => 'EXPIRED10',
        'purchase_amount' => 150000,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['valid' => false, 'reason' => 'expired']);
});

it('rejects voucher below minimum purchase', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'MIN100',
        'type' => 'percentage',
        'value' => 10,
        'min_purchase' => 100000,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'is_active' => true,
    ]);

    $response = $this->post(route('vouchers.validate'), [
        'code' => 'MIN100',
        'purchase_amount' => 50000,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['valid' => false, 'reason' => 'below_minimum']);
});

it('calculates discount correctly for percentage voucher', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'PERCENT10',
        'type' => 'percentage',
        'value' => 10,
        'max_discount' => 50000,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'is_active' => true,
    ]);

    $response = $this->post(route('vouchers.calculate'), [
        'code' => 'PERCENT10',
        'purchase_amount' => 300000,
    ]);

    $response->assertSuccessful();
    // 10% of 300000 = 30000, but max is 50000, so 30000
    $response->assertJson(['discount' => 30000]);
});

it('calculates discount correctly for fixed amount voucher', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'FIXED50K',
        'type' => 'fixed',
        'value' => 50000,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'is_active' => true,
    ]);

    $response = $this->post(route('vouchers.calculate'), [
        'code' => 'FIXED50K',
        'purchase_amount' => 200000,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['discount' => 50000]);
});

it('tracks voucher usage', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'TRACK10',
        'usage_limit' => 10,
        'used_count' => 0,
    ]);

    // Simulate voucher usage
    $voucher->increment('used_count');

    $this->assertDatabaseHas('vouchers', [
        'id' => $voucher->id,
        'used_count' => 1,
    ]);
});

it('prevents using voucher beyond usage limit', function () {
    $voucher = Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'LIMITED10',
        'usage_limit' => 5,
        'used_count' => 5,
        'is_active' => true,
    ]);

    $response = $this->post(route('vouchers.validate'), [
        'code' => 'LIMITED10',
        'purchase_amount' => 150000,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['valid' => false, 'reason' => 'usage_limit_reached']);
});

it('scopes vouchers to current tenant', function () {
    $otherTenant = Tenant::factory()->create();

    Voucher::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'MY-VOUCHER',
    ]);

    Voucher::factory()->create([
        'tenant_id' => $otherTenant->id,
        'code' => 'OTHER-VOUCHER',
    ]);

    $response = $this->get(route('vouchers.index'));

    $response->assertSuccessful();
    $response->assertSee('MY-VOUCHER');
    $response->assertDontSee('OTHER-VOUCHER');
});
