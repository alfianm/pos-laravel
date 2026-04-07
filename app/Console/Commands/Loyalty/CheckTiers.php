<?php

namespace App\Console\Commands\Loyalty;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Models\LoyaltyAccount;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

#[Signature('loyalty:check-tiers')]
#[Description('Evaluasi ulang tier membership berdasarkan spending history.')]
class CheckTiers extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai mengevaluasi tier membership...');

        // 1. Get spending lookback period from config
        $lookbackMonths = config('loyalty.downgrade_check_months', 12);
        $sinceDate = now()->subMonths($lookbackMonths);

        // 2. Process in batches to save memory
        LoyaltyAccount::with(['customer', 'membershipTier', 'tenant'])->chunk(100, function ($accounts) use ($sinceDate) {
            foreach ($accounts as $account) {
                /** @var LoyaltyAccount $account */
                if (!$account->customer) continue;

                // Calculate spending in lookback period
                $spendingInPeriod = Sale::where('customer_id', $account->customer_id)
                    ->where('status', 'completed')
                    ->where('sale_date', '>=', $sinceDate)
                    ->sum('grand_total');

                // Find the best eligible tier for this spending
                $eligibleTier = MembershipTier::where('tenant_id', $account->tenant_id)
                    ->where('min_spending', '<=', $spendingInPeriod)
                    ->orderBy('min_spending', 'desc')
                    ->first();

                if ($eligibleTier && $eligibleTier->id !== $account->membership_tier_id) {
                    $oldTierName = $account->membershipTier?->name ?? 'None';
                    $account->update(['membership_tier_id' => $eligibleTier->id]);
                    $this->line("Customer {$account->customer->name}: {$oldTierName} -> {$eligibleTier->name}");
                }
            }
        });

        $this->info('Selesai mengevaluasi tier membership.');
    }
}
