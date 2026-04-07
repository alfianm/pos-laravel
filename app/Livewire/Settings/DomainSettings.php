<?php

namespace App\Livewire\Settings;

use App\Models\TenantDomain;
use App\Services\TenantManager;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DomainSettings extends Component
{
    public $newDomain = '';
    public $serverIp = '';

    public function mount()
    {
        // For instructions, we might want to show the current server IP
        // In real production, this would be a config value or detected
        $this->serverIp = config('app.server_ip', '103.111.222.333'); 
    }

    public function addDomain()
    {
        $this->validate([
            'newDomain' => 'required|string|max:255|unique:tenant_domains,domain',
        ]);

        $tenantId = app(TenantManager::class)->getTenantId();

        if (!$tenantId) {
            session()->flash('error', 'Tenant context not found.');
            return;
        }

        TenantDomain::create([
            'id' => Str::uuid(),
            'tenant_id' => $tenantId,
            'domain' => strtolower($this->newDomain),
            'is_primary' => false,
            'is_verified' => false,
            'is_active' => true,
            'ssl_status' => 'pending',
        ]);

        $this->newDomain = '';
        session()->flash('message', 'Domain berhasil diajukan. Silakan ikuti instruksi DNS di bawah.');
    }

    public function render()
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        $domains = TenantDomain::where('tenant_id', $tenantId)->get();

        return view('livewire.settings.domain-settings', [
            'domains' => $domains,
        ]);
    }
}
