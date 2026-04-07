<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\LoyaltyAccount;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RFMDashboard extends Component
{
    use WithPagination;

    public string $selectedSegment = 'all';
    public string $sortBy = 'rfm_score';
    public string $sortDirection = 'desc';
    
    public array $segmentStats = [];
    public array $distribution = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function updatedSelectedSegment(): void
    {
        $this->resetPage();
    }

    public function loadStats(): void
    {
        $tenantId = auth()->user()->tenant_id;

        // Fetch counts from database for each segment
        $counts = Customer::where('tenant_id', $tenantId)
            ->groupBy('rfm_segment')
            ->select('rfm_segment', DB::raw('count(*) as count'))
            ->get()
            ->pluck('count', 'rfm_segment')
            ->toArray();

        $segments = [
            'Champions' => ['color' => 'emerald', 'description' => 'Best customers, frequent big spenders.', 'action' => 'Reward with exclusive early access.'],
            'Loyal Customers' => ['color' => 'indigo', 'description' => 'Spend regularly, responsive to promos.', 'action' => 'Upsell higher-value products.'],
            'Potential Loyalists' => ['color' => 'blue', 'description' => 'Recent spenders with average frequency.', 'action' => 'Offer loyalty programs.'],
            'Recent Customers' => ['color' => 'cyan', 'description' => 'Bought recently, but not frequently.', 'action' => 'Provide onboarding support.'],
            'Promising' => ['color' => 'teal', 'description' => 'Recent buyers, spent a good amount.', 'action' => 'Create brand awareness.'],
            'Customers Needing Attention' => ['color' => 'amber', 'description' => 'Above average recency & frequency.', 'action' => 'Make limited time offers.'],
            'At Risk' => ['color' => 'orange', 'description' => 'Spent big, but long time ago.', 'action' => 'Send personalized emails.'],
            'Can\'t Lose Them' => ['color' => 'rose', 'description' => 'Made big purchases, but haven\'t returned.', 'action' => 'Win them back with renewals.'],
            'Should Recover' => ['color' => 'red', 'description' => 'Frequent spenders, now inactive.', 'action' => 'Re-connect immediately.'],
            'Hibernating / Lost' => ['color' => 'gray', 'description' => 'Lowest recency, frequency, monetary.', 'action' => 'Don\'t spend too much effort.'],
        ];

        $this->segmentStats = [];
        $totalCustomers = 0;
        $totalScores = 0;

        foreach ($segments as $name => $meta) {
            $count = $counts[$name] ?? 0;
            $this->segmentStats[$name] = array_merge($meta, ['count' => $count]);
            $totalCustomers += $count;
        }

        // Calculate average RFM combined score
        $avgScore = Customer::where('tenant_id', $tenantId)->avg(DB::raw('recency_score + frequency_score + monetary_score')) ?? 0;

        $this->distribution = [
            'total_customers' => $totalCustomers,
            'avg_rfm_score' => round((float)$avgScore, 1),
        ];
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Customer::where('tenant_id', $tenantId)
            ->with(['loyaltyAccount.membershipTier']);

        if ($this->selectedSegment !== 'all') {
            $query->where('rfm_segment', $this->selectedSegment);
        }

        // Handle sorting
        $column = match ($this->sortBy) {
            'name' => 'name',
            'recency' => 'last_purchase_date',
            'frequency' => 'frequency_score', // Use score as proxy for simplicity
            'monetary' => 'total_spent',
            'rfm_score' => DB::raw('recency_score + frequency_score + monetary_score'),
            default => 'id',
        };

        $customers = $query->orderBy($column, $this->sortDirection)
            ->paginate(15);

        return view('livewire.customer.rfm-dashboard', [
            'customers' => $customers,
        ]);
    }
}
