<?php

namespace App\Livewire\Crm;

use App\Models\Customer;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerDetail extends Component
{
    use WithPagination;

    public Customer $customer;
    public ?LoyaltyAccount $loyaltyAccount = null;
    
    public string $activeTab = 'transactions';

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
        $this->loyaltyAccount = LoyaltyAccount::where('customer_id', $customer->id)->with('membershipTier')->first();
    }

    public function getLoyaltyTransactionsProperty()
    {
        if (!$this->loyaltyAccount) return collect();

        return LoyaltyTransaction::where('loyalty_account_id', $this->loyaltyAccount->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, pageName: 'loyaltyPage');
    }

    public function getSalesHistoryProperty()
    {
        return Sale::where('customer_id', $this->customer->id)
            ->orderBy('sale_date', 'desc')
            ->paginate(10, pageName: 'salesPage');
    }

    /**
     * Get RFM summary for visualization.
     */
    public function getRfmSummaryProperty()
    {
        return [
            ['name' => 'Recency', 'score' => $this->customer->recency_score, 'color' => 'rose'],
            ['name' => 'Frequency', 'score' => $this->customer->frequency_score, 'color' => 'indigo'],
            ['name' => 'Monetary', 'score' => $this->customer->monetary_score, 'color' => 'emerald'],
        ];
    }

    public function render()
    {
        return view('livewire.crm.customer-detail', [
            'loyaltyTransactions' => $this->loyaltyTransactions,
            'salesHistory' => $this->salesHistory,
            'rfmSummary' => $this->rfmSummary,
        ])->layout('layouts.app');
    }
}
