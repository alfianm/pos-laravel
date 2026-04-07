<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CustomerSegmentationService
{
    /**
     * Calculate and update RFM scores and segments for all customers.
     */
    public function updateAllRfmSegments(string $tenantId)
    {
        // 1. Get stats for all customers to determine quantiles
        $stats = Customer::where('tenant_id', $tenantId)
            ->select([
                'id',
                'total_spent',
                'last_purchase_date',
                DB::raw('(SELECT COUNT(*) FROM sales WHERE sales.customer_id = customers.id AND sales.status = \'completed\') as frequency')
            ])
            ->get();

        if ($stats->isEmpty()) return;

        // 2. Determine Quantiles for Scoring (1-5)
        $recencies = $stats->map(fn($s) => $s->last_purchase_date ? now()->diffInDays($s->last_purchase_date) : 999)->sort()->values();
        $frequencies = $stats->map(fn($s) => $s->frequency)->sort()->values();
        $monetaries = $stats->map(fn($s) => (float)$s->total_spent)->sort()->values();

        foreach ($stats as $customerStats) {
            $rScore = $this->calculateScore($customerStats->last_purchase_date ? now()->diffInDays($customerStats->last_purchase_date) : 999, $recencies, true);
            $fScore = $this->calculateScore($customerStats->frequency, $frequencies);
            $mScore = $this->calculateScore((float)$customerStats->total_spent, $monetaries);

            $segment = $this->determineSegment($rScore, $fScore, $mScore);

            Customer::where('id', $customerStats->id)->update([
                'recency_score' => $rScore,
                'frequency_score' => $fScore,
                'monetary_score' => $mScore,
                'rfm_segment' => $segment
            ]);
        }
    }

    private function calculateScore($value, $values, $inverse = false): int
    {
        $count = $values->count();
        if ($count === 0) return 1;

        $rank = $values->search($value);
        if ($rank === false) $rank = 0;

        $score = ceil(($rank + 1) / $count * 5);
        
        return $inverse ? (6 - $score) : $score;
    }

    private function determineSegment($r, $f, $m): string
    {
        $avgFM = ($f + $m) / 2;

        if ($r >= 4 && $avgFM >= 4) return 'Champions';
        if ($r >= 3 && $avgFM >= 4) return 'Loyal Customers';
        if ($r >= 4 && $avgFM >= 2) return 'Potential Loyalists';
        if ($r >= 5 && $avgFM <= 2) return 'Recent Customers';
        if ($r >= 3 && $r <= 4 && $avgFM <= 2) return 'Promising';
        if ($r >= 2 && $r <= 3 && $avgFM >= 2 && $avgFM <= 3) return 'Customers Needing Attention';
        if ($r <= 2 && $avgFM >= 4) return 'Can\'t Lose Them';
        if ($r <= 2 && $avgFM >= 2 && $avgFM <= 3) return 'At Risk';
        if ($r <= 1 && $avgFM >= 4) return 'Should Recover';
        
        return 'Hibernating / Lost';
    }
}
