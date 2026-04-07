<?php

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\MembershipTier;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Services\LoyaltyService;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    $this->actingAs($this->user);
});

describe('LoyaltyService - Points Accrual', function () {
    it('creates loyalty account on first sale', function () {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $loyaltyService = app(LoyaltyService::class);
        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
        ]);

        $pointsAwarded = $loyaltyService->awardPointsForSale($sale);

        expect($pointsAwarded)->toBe(10)
            ->and(LoyaltyAccount::where('customer_id', $customer->id)->exists())->toBeTrue()
            ->and(LoyaltyTransaction::where('type', 'earn')->count())->toBe(1);
    });

    it('calculates points correctly based on spending amount', function () {
        MembershipTier::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Bronze',
            'min_spending' => 0,
            'point_multiplier' => 1.0,
        ]);

        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $loyaltyService = app(LoyaltyService::class);

        $sale1 = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 50000,
            'tax_amount' => 0,
            'grand_total' => 50000,
            'paid_amount' => 50000,
        ]);

        $points1 = $loyaltyService->awardPointsForSale($sale1);
        expect($points1)->toBe(5);

        $sale2 = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'sale_no' => 'INV/2024/002',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
        ]);

        $points2 = $loyaltyService->awardPointsForSale($sale2);
        expect($points2)->toBe(10);

        $account = LoyaltyAccount::where('customer_id', $customer->id)->first();
        expect((float) $account->points_balance)->toBe(15.0);
    });

    it('applies tier multiplier correctly', function () {
        $goldTier = MembershipTier::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Gold',
            'min_spending' => 0,
            'point_multiplier' => 2.0,
        ]);

        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
        $loyaltyAccount = LoyaltyAccount::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'membership_tier_id' => $goldTier->id,
            'points_balance' => 0,
        ]);

        $loyaltyService = app(LoyaltyService::class);

        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
        ]);

        $points = $loyaltyService->awardPointsForSale($sale);

        expect($points)->toBe(20);

        $loyaltyAccount->refresh();
        expect((float) $loyaltyAccount->points_balance)->toBe(20.0);
    });

    it('does not award points when no customer is attached', function () {
        $loyaltyService = app(LoyaltyService::class);

        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => null,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
        ]);

        $points = $loyaltyService->awardPointsForSale($sale);

        expect($points)->toBeNull()
            ->and(LoyaltyTransaction::count())->toBe(0);
    });
});

describe('LoyaltyService - Tier Upgrade', function () {
    it('upgrades tier when spending threshold is reached', function () {
        $bronzeTier = MembershipTier::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Bronze',
            'min_spending' => 0,
            'point_multiplier' => 1.0,
        ]);

        $silverTier = MembershipTier::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Silver',
            'min_spending' => 1000000,
            'point_multiplier' => 1.5,
        ]);

        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'total_spent' => 1500000,
        ]);

        $loyaltyAccount = LoyaltyAccount::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'membership_tier_id' => $bronzeTier->id,
            'points_balance' => 0,
        ]);

        $loyaltyService = app(LoyaltyService::class);
        $loyaltyService->checkAndUpgradeTier($loyaltyAccount, $customer);

        $loyaltyAccount->refresh();
        expect($loyaltyAccount->membership_tier_id)->toBe($silverTier->id);
    });

    it('does not downgrade tier when spending is below threshold', function () {
        $bronzeTier = MembershipTier::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Bronze',
            'min_spending' => 0,
            'point_multiplier' => 1.0,
        ]);

        $silverTier = MembershipTier::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Silver',
            'min_spending' => 1000000,
            'point_multiplier' => 1.5,
        ]);

        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'total_spent' => 500000,
        ]);

        $loyaltyAccount = LoyaltyAccount::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'membership_tier_id' => $silverTier->id,
            'points_balance' => 0,
        ]);

        $loyaltyService = app(LoyaltyService::class);
        $loyaltyService->checkAndUpgradeTier($loyaltyAccount, $customer);

        $loyaltyAccount->refresh();
        expect($loyaltyAccount->membership_tier_id)->toBe($silverTier->id);
    });
});

describe('LoyaltyService - Points Redemption', function () {
    it('redeems points successfully', function () {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
        $loyaltyAccount = LoyaltyAccount::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'points_balance' => 100,
        ]);

        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
            'points_redeemed' => 50,
            'points_value' => 500,
        ]);

        $loyaltyService = app(LoyaltyService::class);
        $result = $loyaltyService->redeemPoints($sale, 50);

        expect($result)->toBeInstanceOf(LoyaltyTransaction::class)
            ->and($result->type)->toBe('redeem')
            ->and((float) $result->points)->toBe(50.0);

        $loyaltyAccount->refresh();
        expect((float) $loyaltyAccount->points_balance)->toBe(50.0);
    });

    it('fails when insufficient points', function () {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
        $loyaltyAccount = LoyaltyAccount::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'points_balance' => 10,
        ]);

        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
        ]);

        $loyaltyService = app(LoyaltyService::class);

        expect(fn () => $loyaltyService->redeemPoints($sale, 50))
            ->toThrow(Exception::class, 'Saldo poin tidak mencukupi.');
    });

    it('does not redeem when no customer', function () {
        $sale = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'customer_id' => null,
            'sale_no' => 'INV/2024/001',
            'sale_date' => now(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'grand_total' => 100000,
            'paid_amount' => 100000,
        ]);

        $loyaltyService = app(LoyaltyService::class);
        $result = $loyaltyService->redeemPoints($sale, 50);

        expect($result)->toBeNull();
    });
});
