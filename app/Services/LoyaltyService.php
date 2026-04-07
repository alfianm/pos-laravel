<?php

namespace App\Services;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\MembershipTier;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoyaltyService
{
    /**
     * Calculate and award points for a completed sale.
     */
    public function awardPointsForSale(Sale $sale)
    {
        if (!$sale->customer_id) {
            return null;
        }

        return DB::transaction(function () use ($sale) {
            $account = LoyaltyAccount::firstOrCreate(
                [
                    'tenant_id' => $sale->tenant_id,
                    'customer_id' => $sale->customer_id
                ],
                [
                    'points_balance' => 0
                ]
            );

            // 1. Determine Tier and Multiplier
            $tier = $account->membershipTier;
            if (!$tier) {
                $tier = MembershipTier::where('tenant_id', $sale->tenant_id)
                    ->orderBy('min_spending', 'asc')
                    ->first();
                
                if ($tier) {
                    $account->membership_tier_id = $tier->id;
                    $account->save();
                }
            }

            $multiplier = $tier ? (float)$tier->point_multiplier : 1.0;

            // 2. Calculate Points (Rule: configurable later, currently 1 point per 10k)
            $baseRate = config('loyalty.points_base_rate', 10000); 
            $earnedPoints = floor($sale->grand_total / $baseRate) * $multiplier;

            if ($earnedPoints > 0) {
                // 3. Update Balance
                $account->increment('points_balance', $earnedPoints);

                // 4. Record Transaction with Expiration (Default 1 year)
                $expiryMonths = config('loyalty.points_expiry_months', 12);
                
                LoyaltyTransaction::create([
                    'tenant_id' => $sale->tenant_id,
                    'loyalty_account_id' => $account->id,
                    'type' => 'earn',
                    'points' => $earnedPoints,
                    'remaining_points' => $earnedPoints,
                    'expires_at' => now()->addMonths($expiryMonths),
                    'reference_type' => 'Sale',
                    'reference_id' => $sale->id,
                ]);
            }

            // 5. Check for Tier Upgrade
            $this->checkAndUpgradeTier($account, $sale->customer);

            return $earnedPoints;
        });
    }

    /**
     * Check if customer is eligible for a tier upgrade based on total spent.
     */
    public function checkAndUpgradeTier(LoyaltyAccount $account, Customer $customer)
    {
        $totalSpent = (float)$customer->total_spent;

        $newTier = MembershipTier::where('tenant_id', $customer->tenant_id)
            ->where('min_spending', '<=', $totalSpent)
            ->orderBy('min_spending', 'desc')
            ->first();

        if ($newTier && $newTier->id !== $account->membership_tier_id) {
            $account->update([
                'membership_tier_id' => $newTier->id
            ]);
            
            return $newTier;
        }

        return null;
    }

    /**
     * Validate a voucher for a given sale context.
     */
    public function validateVoucher(string $code, string $tenantId, float $orderAmount, ?string $customerId = null, array $cartItems = [])
    {
        $voucher = \App\Models\Voucher::where('tenant_id', $tenantId)
            ->where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return ['valid' => false, 'message' => 'Voucher tidak ditemukan.'];
        }

        if ($voucher->starts_at && $voucher->starts_at->isFuture()) {
            return ['valid' => false, 'message' => 'Voucher belum berlaku.'];
        }

        if ($voucher->ends_at && $voucher->ends_at->isPast()) {
            return ['valid' => false, 'message' => 'Voucher sudah kadaluwarsa.'];
        }

        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            return ['valid' => false, 'message' => 'Voucher sudah mencapai batas penggunaan.'];
        }

        if ($orderAmount < $voucher->min_order_amount) {
            return ['valid' => false, 'message' => 'Minimal order Rp' . number_format($voucher->min_order_amount, 0, ',', '.') . ' tidak tercapai.'];
        }

        // Tier check
        if ($voucher->membership_tier_id && $customerId) {
            $account = LoyaltyAccount::where('customer_id', $customerId)->first();
            if (!$account || $account->membership_tier_id !== $voucher->membership_tier_id) {
                return ['valid' => false, 'message' => 'Voucher ini hanya untuk member tier tertentu.'];
            }
        }

        // Advanced Rules Check (Phase 14)
        if (!empty($cartItems)) {
            $promotionService = app(PromotionService::class);
            $ruleResult = $promotionService->validateRules($voucher, $cartItems);
            
            if (!$ruleResult['valid']) {
                return $ruleResult;
            }
        }

        return ['valid' => true, 'voucher' => $voucher];
    }

    /**
     * Calculate discount amount for a voucher.
     */
    public function calculateVoucherDiscount(\App\Models\Voucher $voucher, float $subtotal)
    {
        $discount = 0;
        if ($voucher->type === 'fixed') {
            $discount = (float)$voucher->value;
        } else {
            $discount = $subtotal * ($voucher->value / 100);
        }

        if ($voucher->max_discount_amount && $discount > $voucher->max_discount_amount) {
            $discount = $voucher->max_discount_amount;
        }

        return $discount;
    }

    /**
     * Redeem points for a discount using FIFO logic for expiration.
     */
    public function redeemPoints(Sale $sale, float $points)
    {
        if ($points <= 0 || !$sale->customer_id) return null;

        return DB::transaction(function () use ($sale, $points) {
            $account = LoyaltyAccount::where('customer_id', $sale->customer_id)->lockForUpdate()->first();
            
            if (!$account || $account->points_balance < $points) {
                throw new \Exception('Saldo poin tidak mencukupi.');
            }

            // FIFO Deduction logic
            $remainingToDeduct = $points;
            $earningTransactions = LoyaltyTransaction::where('loyalty_account_id', $account->id)
                ->where('type', 'earn')
                ->where('remaining_points', '>', 0)
                ->where('is_expired', false)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($earningTransactions as $txn) {
                /** @var LoyaltyTransaction $txn */
                if ($remainingToDeduct <= 0) break;

                $deductFromThis = min($txn->remaining_points, $remainingToDeduct);
                $txn->decrement('remaining_points', $deductFromThis);
                $remainingToDeduct -= $deductFromThis;
            }

            // Update Global Balance
            $account->decrement('points_balance', $points);

            // Record Redemption
            return LoyaltyTransaction::create([
                'tenant_id' => $sale->tenant_id,
                'loyalty_account_id' => $account->id,
                'type' => 'redeem',
                'points' => $points,
                'reference_type' => 'Sale',
                'reference_id' => $sale->id,
            ]);
        });
    }

    /**
     * Transfer points from one customer to another.
     */
    public function transferPoints(string $tenantId, string $fromCustomerId, string $toCustomerId, float $points)
    {
        if ($points <= 0 || $fromCustomerId === $toCustomerId) {
            throw new \Exception('Parameter transfer tidak valid.');
        }

        return DB::transaction(function () use ($tenantId, $fromCustomerId, $toCustomerId, $points) {
            $fromAccount = LoyaltyAccount::where('tenant_id', $tenantId)
                ->where('customer_id', $fromCustomerId)
                ->lockForUpdate()
                ->first();

            $toAccount = LoyaltyAccount::firstOrCreate(
                ['tenant_id' => $tenantId, 'customer_id' => $toCustomerId],
                ['points_balance' => 0]
            );

            if (!$fromAccount || $fromAccount->points_balance < $points) {
                throw new \Exception('Saldo poin pengirim tidak mencukupi.');
            }

            // 1. Deduct from sender (FIFO)
            $remainingToDeduct = $points;
            $earningTransactions = LoyaltyTransaction::where('loyalty_account_id', $fromAccount->id)
                ->where('type', 'earn')
                ->where('remaining_points', '>', 0)
                ->where('is_expired', false)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($earningTransactions as $txn) {
                /** @var LoyaltyTransaction $txn */
                if ($remainingToDeduct <= 0) break;
                $deductFromThis = min($txn->remaining_points, $remainingToDeduct);
                $txn->decrement('remaining_points', $deductFromThis);
                $remainingToDeduct -= $deductFromThis;
            }

            $fromAccount->decrement('points_balance', $points);

            // 2. Add to recipient
            $toAccount->increment('points_balance', $points);

            // 3. Record Transactions
            LoyaltyTransaction::create([
                'tenant_id' => $tenantId,
                'loyalty_account_id' => $fromAccount->id,
                'type' => 'redeem',
                'points' => $points,
                'reference_type' => 'TransferOut',
                'reference_id' => $toCustomerId,
            ]);

            LoyaltyTransaction::create([
                'tenant_id' => $tenantId,
                'loyalty_account_id' => $toAccount->id,
                'type' => 'earn',
                'points' => $points,
                'remaining_points' => $points,
                'expires_at' => now()->addMonths(config('loyalty.points_expiry_months', 12)),
                'reference_type' => 'TransferIn',
                'reference_id' => $fromCustomerId,
            ]);

            return true;
        });
    }

    /**
     * Award points manually for a customer.
     */
    public function earnPoints(Customer $customer, float $points, string $reason = 'Manual Reward', ?string $refType = null, ?string $refId = null)
    {
        if ($points <= 0) return null;

        return DB::transaction(function () use ($customer, $points, $reason, $refType, $refId) {
            $account = LoyaltyAccount::firstOrCreate(
                ['tenant_id' => $customer->tenant_id, 'customer_id' => $customer->id],
                ['points_balance' => 0]
            );

            // 1. Update Balance
            $account->increment('points_balance', $points);

            // 2. Record Transaction with Expiration
            $expiryMonths = config('loyalty.points_expiry_months', 12);
            
            return LoyaltyTransaction::create([
                'tenant_id' => $customer->tenant_id,
                'loyalty_account_id' => $account->id,
                'type' => 'earn',
                'points' => $points,
                'remaining_points' => $points,
                'expires_at' => now()->addMonths($expiryMonths),
                'reference_type' => $refType ?? 'Manual',
                'reference_id' => $refId ?? Str::uuid()->toString(),
                'description' => $reason,
            ]);
        });
    }
}
