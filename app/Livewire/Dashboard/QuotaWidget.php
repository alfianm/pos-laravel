<?php

namespace App\Livewire\Dashboard;

use App\Services\QuotaService;
use Livewire\Component;

class QuotaWidget extends Component
{
    public $quotas = [];

    public $alerts = [];

    protected QuotaService $quotaService;

    public function boot(QuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    public function mount()
    {
        $tenantId = auth()->user()?->tenant_id;

        if ($tenantId) {
            $this->quotas = $this->quotaService->getQuotaSummary($tenantId);
            $this->alerts = $this->quotaService->getAlerts($tenantId);
        }
    }

    public function getProgressBarClass($percentage, $isExceeded): string
    {
        if ($isExceeded) {
            return 'bg-red-500';
        }

        if ($percentage >= 90) {
            return 'bg-red-400';
        }

        if ($percentage >= 80) {
            return 'bg-yellow-400';
        }

        return 'bg-emerald-500';
    }

    public function render()
    {
        return view('livewire.dashboard.quota-widget');
    }
}
