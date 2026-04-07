<?php

namespace App\Livewire\Tenants;

use App\Events\TenantCreated;
use App\Models\Tenant;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class TenantForm extends Component
{
    public ?Tenant $tenant = null;
    public string $name = '';
    public string $code = '';
    public string $currency = 'IDR';
    public string $timezone = 'Asia/Jakarta';
    public string $status = 'active';

    public function mount(?Tenant $tenant = null)
    {
        if ($tenant && $tenant->exists) {
            $this->tenant = $tenant;
            $this->name = $tenant->name;
            $this->code = $tenant->code;
            $this->currency = $tenant->currency;
            $this->timezone = $tenant->timezone;
            $this->status = $tenant->status;
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tenants', 'code')->ignore($this->tenant?->id),
            ],
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'status' => 'required|in:active,inactive,suspended',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'status' => $this->status,
        ];

        if ($this->tenant && $this->tenant->exists) {
            $oldValues = $this->tenant->toArray();
            $this->tenant->update($data);
            AuditLogService::log('update', $this->tenant, $oldValues, $this->tenant->fresh()->toArray(), 'tenant-mgmt');
        } else {
            $this->tenant = Tenant::create($data);
            event(new TenantCreated($this->tenant));
            AuditLogService::log('create', $this->tenant, null, $this->tenant->toArray(), 'tenant-mgmt');
        }

        session()->flash('message', 'Tenant berhasil disimpan.');
        return $this->redirect(route('tenants.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.tenants.tenant-form');
    }
}
