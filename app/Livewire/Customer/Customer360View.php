<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Services\RFMAnalysisService;
use App\Services\CustomerTimelineService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Customer360View extends Component
{
    public Customer $customer;
    public array $rfmData = [];
    public Collection $timeline;
    public array $purchaseStats = [];
    public array $loyaltyData = [];

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
        $this->loadData();
    }

    public function loadData(): void
    {
        $rfmService = new RFMAnalysisService();
        $this->rfmData = $rfmService->analyzeCustomer($this->customer);

        $timelineService = new CustomerTimelineService();
        $this->timeline = $timelineService->getTimeline($this->customer);

        $this->purchaseStats = $this->calculatePurchaseStats();
        $this->loyaltyData = $this->loadLoyaltyData();
    }

    protected function calculatePurchaseStats(): array
    {
        $sales = $this->customer->sales()
            ->where('status', 'completed')
            ->get();

        if ($sales->isEmpty()) {
            return [
                'total_orders' => 0,
                'total_spent' => 0,
                'avg_order_value' => 0,
                'min_order' => 0,
                'max_order' => 0,
                'first_purchase' => null,
                'last_purchase' => null,
                'favorite_products' => [],
                'purchase_frequency_days' => 0,
            ];
        }

        $totals = $sales->pluck('total');
        $dates = $sales->pluck('created_at')->sort();

        // Calculate purchase frequency
        $frequencyDays = 0;
        if ($dates->count() > 1) {
            $dateDiffs = [];
            for ($i = 1; $i < $dates->count(); $i++) {
                $dateDiffs[] = $dates[$i]->diffInDays($dates[$i - 1]);
            }
            $frequencyDays = !empty($dateDiffs)
                ? round(array_sum($dateDiffs) / count($dateDiffs), 1)
                : 0;
        }

        // Get favorite products
        $favoriteProducts = $this->customer->sales()
            ->with('items.product')
            ->where('status', 'completed')
            ->get()
            ->flatMap(fn($sale) => $sale->items)
            ->groupBy('product_id')
            ->map(fn($items) => [
                'product' => $items->first()->product,
                'quantity' => $items->sum('quantity'),
                'revenue' => $items->sum(fn($i) => $i->quantity * $i->price),
            ])
            ->sortByDesc('quantity')
            ->take(5)
            ->values();

        return [
            'total_orders' => $sales->count(),
            'total_spent' => $totals->sum(),
            'avg_order_value' => round($totals->avg(), 2),
            'min_order' => $totals->min(),
            'max_order' => $totals->max(),
            'first_purchase' => $dates->first(),
            'last_purchase' => $dates->last(),
            'favorite_products' => $favoriteProducts,
            'purchase_frequency_days' => $frequencyDays,
        ];
    }

    protected function loadLoyaltyData(): array
    {
        $account = $this->customer->loyaltyAccount;

        if (!$account) {
            return [
                'has_account' => false,
                'points_balance' => 0,
                'tier' => null,
                'lifetime_earned' => 0,
                'lifetime_redeemed' => 0,
                'transactions' => [],
            ];
        }

        return [
            'has_account' => true,
            'points_balance' => $account->points_balance,
            'tier' => $account->membershipTier?->name ?? 'Default',
            'lifetime_earned' => $account->transactions()
                ->where('type', 'earned')
                ->sum('points'),
            'lifetime_redeemed' => abs($account->transactions()
                ->where('type', 'redeemed')
                ->sum('points')),
            'transactions' => $account->transactions()
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    public function render()
    {
        return view('livewire.customer.customer-360-view', [
            'segmentInfo' => $this->getSegmentInfo(),
        ]);
    }

    protected function getSegmentInfo(): array
    {
        $rfmService = new RFMAnalysisService();
        $definitions = $rfmService->getSegmentDefinitions();

        return $definitions[$this->rfmData['segment']] ?? [
            'description' => '-',
            'action' => '-',
            'color' => 'gray',
        ];
    }
}
