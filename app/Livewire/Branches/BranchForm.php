<?php

namespace App\Livewire\Branches;

use App\Exceptions\QuotaExceededException;
use App\Models\Branch;
use App\Models\Tenant;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BranchForm extends Component
{
    public ?Branch $branch = null;

    public string $tenant_id = '';

    public string $name = '';

    public string $code = '';

    public string $address = '';

    public string $phone = '';

    public bool $is_main_warehouse = false;

    public string $status = 'active';

    public function mount(?Branch $branch = null)
    {
        if ($branch && $branch->exists) {
            $this->branch = $branch;
            $this->tenant_id = $branch->tenant_id;
            $this->name = $branch->name;
            $this->code = $branch->code;
            $this->address = $branch->address ?? '';
            $this->phone = $branch->phone ?? '';
            $this->is_main_warehouse = (bool) $branch->is_main_warehouse;
            $this->status = $branch->status;
        } else {
            if (! Auth::user()->hasRole('super_admin')) {
                $this->tenant_id = Auth::user()->tenant_id;
            }
        }
    }

    public function rules()
    {
        return [
            'tenant_id' => 'required|uuid|exists:tenants,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('branches', 'code')
                    ->where('tenant_id', $this->tenant_id)
                    ->ignore($this->branch?->id),
            ],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_main_warehouse' => 'boolean',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function save()
    {
        if (! Auth::user()->hasRole('super_admin')) {
            $this->tenant_id = Auth::user()->tenant_id;
        }

        $this->validate();

        if (! $this->branch || ! $this->branch->exists) {
            try {
                $quotaService = app(QuotaService::class);
                $quotaService->enforceQuota($this->tenant_id, Subscription::QUOTA_BRANCHES, 1);
            } catch (QuotaExceededException $e) {
                session()->flash('error', $e->getMessage());

                return;
            }
        }

        $data = [
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'address' => $this->address,
            'phone' => $this->phone,
            'is_main_warehouse' => $this->is_main_warehouse,
            'status' => $this->status,
        ];

        if ($this->branch && $this->branch->exists) {
            $oldValues = $this->branch->toArray();
            $this->branch->update($data);
            AuditLogService::log('update', $this->branch, $oldValues, $this->branch->fresh()->toArray(), 'branch-mgmt');
        } else {
            $this->branch = Branch::create($data);
            AuditLogService::log('create', $this->branch, null, $this->branch->toArray(), 'branch-mgmt');
        }

        session()->flash('message', 'Cabang berhasil disimpan.');

        return $this->redirect(route('branches.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.branches.branch-form', [
            'tenants' => Auth::user()->hasRole('super_admin') ? Tenant::all() : [],
        ]);
    }
}
