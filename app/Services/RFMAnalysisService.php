<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * RFM Analysis Service
 * Analyzes customer value based on Recency, Frequency, and Monetary metrics
 *
 * Recency: Days since last purchase (lower = better)
 * Frequency: Number of orders in period (higher = better)
 * Monetary: Total purchase value (higher = better)
 */
class RFMAnalysisService
{
    protected const SEGMENT_CHAMPIONS = 'Champions';
    protected const SEGMENT_LOYAL = 'Loyal Customers';
    protected const SEGMENT_POTENTIAL = 'Potential Loyalist';
    protected const SEGMENT_NEW = 'New Customers';
    protected const SEGMENT_PROMISING = 'Promising';
    protected const SEGMENT_NEED_ATTENTION = 'Need Attention';
    protected const SEGMENT_ABOUT_TO_SLEEP = 'About to Sleep';
    protected const SEGMENT_AT_RISK = 'At Risk';
    protected const SEGMENT_CANNOT_LOSE = 'Cannot Lose Them';
    protected const SEGMENT_HIBERNATING = 'Hibernating';
    protected const SEGMENT_LOST = 'Lost';

    protected int $recencyDays;
    protected int $rfmPeriodDays;

    public function __construct(int $recencyDays = 365, int $rfmPeriodDays = 365)
    {
        $this->recencyDays = $recencyDays;
        $this->rfmPeriodDays = $rfmPeriodDays;
    }

    /**
     * Calculate RFM scores for a single customer
     */
    public function analyzeCustomer(Customer $customer): array
    {
        $sales = $this->getCustomerSales($customer);

        if ($sales->isEmpty()) {
            return $this->getEmptyRFMData();
        }

        $recency = $this->calculateRecency($sales);
        $frequency = $this->calculateFrequency($sales);
        $monetary = $this->calculateMonetary($sales);

        $rScore = $this->scoreRecency($recency);
        $fScore = $this->scoreFrequency($frequency);
        $mScore = $this->scoreMonetary($monetary);

        return [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'recency_days' => $recency,
            'frequency' => $frequency,
            'monetary' => $monetary,
            'r_score' => $rScore,
            'f_score' => $fScore,
            'm_score' => $mScore,
            'rfm_score' => "{$rScore}{$fScore}{$mScore}",
            'segment' => $this->determineSegment($rScore, $fScore, $mScore),
            'avg_order_value' => $frequency > 0 ? round($monetary / $frequency, 2) : 0,
            'last_purchase_date' => $sales->first()?->created_at,
        ];
    }

    /**
     * Analyze all customers and return segmented data
     */
    public function analyzeAllCustomers(): array
    {
        $customers = Customer::where('tenant_id', auth()->user()->current_tenant_id)
            ->has('sales')
            ->get();

        $analyzed = [];
        foreach ($customers as $customer) {
            $analyzed[] = $this->analyzeCustomer($customer);
        }

        return [
            'total_customers' => count($analyzed),
            'segments' => $this->groupBySegment($analyzed),
            'customers' => $analyzed,
            'summary' => $this->calculateSummary($analyzed),
        ];
    }

    /**
     * Get customers by segment
     */
    public function getCustomersBySegment(string $segment): array
    {
        $analysis = $this->analyzeAllCustomers();

        return array_filter(
            $analysis['customers'],
            fn($customer) =>
            $customer['segment'] === $segment
        );
    }

    /**
     * Get at-risk customers needing retention campaigns
     */
    public function getAtRiskCustomers(): array
    {
        return $this->getCustomersBySegment(self::SEGMENT_AT_RISK);
    }

    /**
     * Get champions for referral/loyalty programs
     */
    public function getChampions(): array
    {
        return $this->getCustomersBySegment(self::SEGMENT_CHAMPIONS);
    }

    /**
     * Get customer sales within RFM period
     */
    protected function getCustomerSales(Customer $customer): Collection
    {
        return $customer->sales()
            ->where('created_at', '>=', now()->subDays($this->rfmPeriodDays))
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Calculate days since last purchase
     */
    protected function calculateRecency(Collection $sales): int
    {
        if ($sales->isEmpty()) {
            return $this->recencyDays;
        }

        return (int) $sales->first()->created_at->diffInDays(now());
    }

    /**
     * Calculate purchase frequency
     */
    protected function calculateFrequency(Collection $sales): int
    {
        return $sales->count();
    }

    /**
     * Calculate total monetary value
     */
    protected function calculateMonetary(Collection $sales): float
    {
        return (float) $sales->sum('total');
    }

    /**
     * Score recency (1-5, 5 = most recent)
     */
    protected function scoreRecency(int $days): int
    {
        return match (true) {
            $days <= 7 => 5,
            $days <= 30 => 4,
            $days <= 90 => 3,
            $days <= 180 => 2,
            default => 1,
        };
    }

    /**
     * Score frequency (1-5, 5 = highest frequency)
     * Uses quintile-based scoring
     */
    protected function scoreFrequency(int $frequency): int
    {
        return match (true) {
            $frequency >= 20 => 5,
            $frequency >= 10 => 4,
            $frequency >= 5 => 3,
            $frequency >= 2 => 2,
            default => 1,
        };
    }

    /**
     * Score monetary value (1-5, 5 = highest value)
     */
    protected function scoreMonetary(float $monetary): int
    {
        return match (true) {
            $monetary >= 5000000 => 5, // 5M+
            $monetary >= 2000000 => 4, // 2M+
            $monetary >= 1000000 => 3, // 1M+
            $monetary >= 500000 => 2,  // 500K+
            default => 1,
        };
    }

    /**
     * Determine customer segment based on RFM scores
     */
    protected function determineSegment(int $r, int $f, int $m): string
    {
        // Champions: R=5, F=5, M=4-5
        if ($r >= 4 && $f >= 4 && $m >= 4) {
            return self::SEGMENT_CHAMPIONS;
        }

        // Loyal Customers: R=3-5, F=3-5, M=3-5
        if ($r >= 3 && $f >= 3 && $m >= 3) {
            return self::SEGMENT_LOYAL;
        }

        // Cannot Lose Them: R=1-2, F=4-5, M=4-5
        if ($r <= 2 && $f >= 4 && $m >= 4) {
            return self::SEGMENT_CANNOT_LOSE;
        }

        // At Risk: R=1-2, F=2-5, M=2-5
        if ($r <= 2 && $f >= 2 && $m >= 2) {
            return self::SEGMENT_AT_RISK;
        }

        // Potential Loyalist: R=3-5, F=1-2, M=1-3
        if ($r >= 3 && $f <= 2 && $m <= 3) {
            return self::SEGMENT_POTENTIAL;
        }

        // New Customers: R=5, F=1, M=1
        if ($r === 5 && $f === 1 && $m === 1) {
            return self::SEGMENT_NEW;
        }

        // Promising: R=4-5, F=1, M=1-2
        if ($r >= 4 && $f === 1 && $m <= 2) {
            return self::SEGMENT_PROMISING;
        }

        // Need Attention: R=2-3, F=2-3, M=2-3
        if ($r >= 2 && $r <= 3 && $f >= 2 && $f <= 3 && $m >= 2 && $m <= 3) {
            return self::SEGMENT_NEED_ATTENTION;
        }

        // About to Sleep: R=2-3, F<=2, M<=2
        if ($r >= 2 && $r <= 3 && $f <= 2 && $m <= 2) {
            return self::SEGMENT_ABOUT_TO_SLEEP;
        }

        // Hibernating: R=1-2, F=1-2, M=1-2
        if ($r <= 2 && $f <= 2 && $m <= 2) {
            return self::SEGMENT_HIBERNATING;
        }

        // Lost: R=1, F=1, M=1
        if ($r === 1 && $f === 1 && $m === 1) {
            return self::SEGMENT_LOST;
        }

        return self::SEGMENT_NEED_ATTENTION;
    }

    /**
     * Group customers by segment
     */
    protected function groupBySegment(array $customers): array
    {
        $segments = [];

        foreach ($customers as $customer) {
            $segment = $customer['segment'];
            if (!isset($segments[$segment])) {
                $segments[$segment] = [
                    'name' => $segment,
                    'count' => 0,
                    'total_monetary' => 0,
                    'customers' => [],
                ];
            }

            $segments[$segment]['count']++;
            $segments[$segment]['total_monetary'] += $customer['monetary'];
            $segments[$segment]['customers'][] = $customer;
        }

        // Sort by count descending
        uasort($segments, fn($a, $b) => $b['count'] <=> $a['count']);

        return $segments;
    }

    /**
     * Calculate summary statistics
     */
    protected function calculateSummary(array $customers): array
    {
        if (empty($customers)) {
            return [
                'avg_recency' => 0,
                'avg_frequency' => 0,
                'avg_monetary' => 0,
                'total_monetary' => 0,
            ];
        }

        $count = count($customers);

        return [
            'avg_recency' => round(array_sum(array_column($customers, 'recency_days')) / $count, 1),
            'avg_frequency' => round(array_sum(array_column($customers, 'frequency')) / $count, 1),
            'avg_monetary' => round(array_sum(array_column($customers, 'monetary')) / $count, 2),
            'total_monetary' => round(array_sum(array_column($customers, 'monetary')), 2),
        ];
    }

    /**
     * Get empty RFM data structure
     */
    protected function getEmptyRFMData(): array
    {
        return [
            'customer_id' => null,
            'customer_name' => null,
            'recency_days' => $this->recencyDays,
            'frequency' => 0,
            'monetary' => 0,
            'r_score' => 1,
            'f_score' => 1,
            'm_score' => 1,
            'rfm_score' => '111',
            'segment' => self::SEGMENT_LOST,
            'avg_order_value' => 0,
            'last_purchase_date' => null,
        ];
    }

    /**
     * Get segment descriptions and recommended actions
     */
    public function getSegmentDefinitions(): array
    {
        return [
            self::SEGMENT_CHAMPIONS => [
                'description' => 'Pembeli terbaik dengan frekuensi dan nilai tinggi, belanja terbaru',
                'characteristics' => 'R=4-5, F=4-5, M=4-5',
                'action' => 'Reward dengan program loyalitas eksklusif, minta testimonial/referral',
                'color' => 'green',
            ],
            self::SEGMENT_LOYAL => [
                'description' => 'Pelanggan setia yang membeli secara konsisten',
                'characteristics' => 'R=3-5, F=3-5, M=3-5',
                'action' => 'Upsell produk premium, berikan penawaran personal',
                'color' => 'blue',
            ],
            self::SEGMENT_POTENTIAL => [
                'description' => 'Pelanggan baru dengan potensial menjadi loyal',
                'characteristics' => 'R=3-5, F=1-2, M=1-3',
                'action' => 'Kirim newsletter, tawarkan diskon kedua',
                'color' => 'cyan',
            ],
            self::SEGMENT_NEW => [
                'description' => 'Pelanggan yang baru pertama kali membeli',
                'characteristics' => 'R=5, F=1, M=1',
                'action' => 'Onboarding campaign, welcome series',
                'color' => 'teal',
            ],
            self::SEGMENT_PROMISING => [
                'description' => 'Pelanggan baru dengan pembelian terbaru',
                'characteristics' => 'R=4-5, F=1, M=1-2',
                'action' => 'Tawarkan free trial, diskon follow-up',
                'color' => 'emerald',
            ],
            self::SEGMENT_NEED_ATTENTION => [
                'description' => 'Pelanggan dengan aktivitas menurun',
                'characteristics' => 'R=2-3, F=2-3, M=2-3',
                'action' => 'Limited offer, re-engagement campaign',
                'color' => 'yellow',
            ],
            self::SEGMENT_ABOUT_TO_SLEEP => [
                'description' => 'Sedang kehilangan minat, perlu perhatian',
                'characteristics' => 'R=2-3, F<=2, M<=2',
                'action' => 'Diskon menarik, produk rekomendasi',
                'color' => 'orange',
            ],
            self::SEGMENT_AT_RISK => [
                'description' => 'Pelanggan berharga yang mulai pergi',
                'characteristics' => 'R=1-2, F=2-5, M=2-5',
                'action' => 'Retention campaign, survey kepuasan',
                'color' => 'red',
            ],
            self::SEGMENT_CANNOT_LOSE => [
                'description' => 'Pelanggan bernilai tinggi yang sangat berisiko pergi',
                'characteristics' => 'R=1-2, F=4-5, M=4-5',
                'action' => 'Hubungi personal, tawarkan exclusive deal',
                'color' => 'rose',
            ],
            self::SEGMENT_HIBERNATING => [
                'description' => 'Pelanggan yang "tidur", perlu diaktifkan kembali',
                'characteristics' => 'R=1-2, F=1-2, M=1-2',
                'action' => 'New catalog, win-back campaign',
                'color' => 'gray',
            ],
            self::SEGMENT_LOST => [
                'description' => 'Pelanggan yang sudah tidak aktif lama',
                'characteristics' => 'R=1, F=1, M=1',
                'action' => 'Survey exit, tawaran terakhir',
                'color' => 'zinc',
            ],
        ];
    }
}
