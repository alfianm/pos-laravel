<?php

namespace App\Livewire\Settings;

use App\Models\WebhookDelivery;
use App\Services\TenantManager;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WebhookLogs extends Component
{
    use WithPagination;

    public function render()
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        
        $logs = WebhookDelivery::whereHas('webhook', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.settings.webhook-logs', [
            'logs' => $logs,
        ]);
    }
}
