<?php

namespace App\Livewire\Settings;

use App\Models\Webhook;
use App\Services\TenantManager;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class WebhookSettings extends Component
{
    public $webhooks;
    public $webhookId;
    public $name, $url, $secret;
    public $selectedEvents = [];
    public $is_active = true;

    public $showModal = false;

    public function mount()
    {
        $this->loadWebhooks();
    }

    public function loadWebhooks()
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        $this->webhooks = Webhook::where('tenant_id', $tenantId)->get();
    }

    public function resetFields()
    {
        $this->webhookId = null;
        $this->name = '';
        $this->url = '';
        $this->secret = Str::random(32);
        $this->selectedEvents = [];
        $this->is_active = true;
    }

    public function create()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(Webhook $webhook)
    {
        $this->webhookId = $webhook->id;
        $this->name = $webhook->name;
        $this->url = $webhook->url;
        $this->secret = $webhook->secret;
        $this->selectedEvents = $webhook->monitored_events ?? [];
        $this->is_active = $webhook->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:255',
            'secret' => 'nullable|string|max:100',
            'selectedEvents' => 'array',
        ]);

        $tenantId = app(TenantManager::class)->getTenantId();

        Webhook::updateOrCreate(['id' => $this->webhookId], [
            'tenant_id' => $tenantId,
            'name' => $this->name,
            'url' => $this->url,
            'secret' => $this->secret,
            'monitored_events' => empty($this->selectedEvents) ? null : $this->selectedEvents,
            'is_active' => $this->is_active,
        ]);

        $this->showModal = false;
        $this->loadWebhooks();
        session()->flash('message', 'Webhook berhasil disimpan.');
    }

    public function toggleActive(Webhook $webhook)
    {
        $webhook->update(['is_active' => !$webhook->is_active]);
        $this->loadWebhooks();
    }

    public function delete(Webhook $webhook)
    {
        $webhook->delete();
        $this->loadWebhooks();
        session()->flash('message', 'Webhook berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.settings.webhook-settings', [
            'availableEvents' => Webhook::getAllEvents(),
        ]);
    }
}
