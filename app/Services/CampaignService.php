<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignService
{
    /**
     * Launch/Execute a campaign across targeted customers.
     */
    public function execute(Campaign $campaign): array
    {
        if ($campaign->status !== 'running') {
            return ['success' => false, 'message' => 'Campaign must be in running status to execute.'];
        }

        $tenantId = $campaign->tenant_id;
        $query = Customer::where('tenant_id', $tenantId);

        if ($campaign->target_segment) {
            $query->where('rfm_segment', $campaign->target_segment);
        }

        $customers = $query->get();
        $reachCount = 0;

        DB::beginTransaction();
        try {
            $customers->each(function (Customer $customer) use ($campaign, &$reachCount) {
                if ($campaign->type === 'voucher' && $campaign->voucher_id) {
                    $this->createPersonalVoucher($campaign, $customer);
                    $reachCount++;
                } elseif ($campaign->type === 'loyalty_bonus' && $campaign->bonus_points > 0) {
                    $this->awardBonusPoints($campaign, $customer);
                    $reachCount++;
                } else {
                    $reachCount++;
                }
            });

            $campaign->update([
                'reach_count' => $reachCount,
                'status' => 'completed'
            ]);

            DB::commit();
            return ['success' => true, 'reach' => $reachCount];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function createPersonalVoucher(Campaign $campaign, Customer $customer)
    {
        $template = $campaign->voucher;
        if (!$template) return;

        // Clone the voucher specifically for this customer
        Voucher::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $campaign->tenant_id,
            'name' => "CAMPAIGN: {$campaign->name}",
            'code' => strtoupper($template->code . '-' . Str::random(4)),
            'type' => $template->type,
            'discount_value' => $template->discount_value,
            'min_spend' => $template->min_spend,
            'max_discount' => $template->max_discount,
            'starts_at' => now(),
            'expires_at' => $campaign->ends_at ?? now()->addDays(30),
            'usage_limit' => 1,
            'is_active' => true,
            'is_redeemable' => true,
            'customer_id' => $customer->id, // Targeted voucher
        ]);
    }

    protected function awardBonusPoints(Campaign $campaign, Customer $customer)
    {
        $loyaltyService = app(LoyaltyService::class);
        $loyaltyService->earnPoints(
            $customer,
            $campaign->bonus_points,
            'Campaign Bonus: ' . $campaign->name,
            'campaign',
            $campaign->id
        );
    }
}
