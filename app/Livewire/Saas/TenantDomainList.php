<?php

namespace App\Livewire\Saas;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TenantDomainList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    public $showModal = false;
    public $editId = null;

    // Form fields
    public $tenant_id = '';
    public $domain = '';
    public $is_primary = false;
    public $is_verified = false;
    public $is_active = true;
    public $ssl_status = 'pending';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editId = $id;
        $domain = TenantDomain::findOrFail($id);

        $this->tenant_id = $domain->tenant_id;
        $this->domain = $domain->domain;
        $this->is_primary = $domain->is_primary;
        $this->is_verified = $domain->is_verified;
        $this->is_active = $domain->is_active;
        $this->ssl_status = $domain->ssl_status;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->tenant_id = '';
        $this->domain = '';
        $this->is_primary = false;
        $this->is_verified = false;
        $this->is_active = true;
        $this->ssl_status = 'pending';
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'domain' => 'required|string|max:255|unique:tenant_domains,domain,' . $this->editId,
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'ssl_status' => 'required|in:pending,active,expired,failed',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tenant_id' => $this->tenant_id,
            'domain' => strtolower($this->domain),
            'is_primary' => $this->is_primary,
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'ssl_status' => $this->ssl_status,
        ];

        if ($this->is_primary) {
            // Unset other primary domains for this tenant
            TenantDomain::where('tenant_id', $this->tenant_id)
                ->where('id', '!=', $this->editId)
                ->update(['is_primary' => false]);
        }

        if ($this->editId) {
            $domain = TenantDomain::findOrFail($this->editId);
            $domain->update($data);
            session()->flash('message', 'Domain tenant berhasil diperbarui.');
        } else {
            $data['id'] = Str::uuid();
            TenantDomain::create($data);
            session()->flash('message', 'Domain tenant berhasil ditambahkan.');
        }

        // Clear cache for this domain
        \Illuminate\Support\Facades\Cache::forget('tenant_domain_' . strtolower($this->domain));

        $this->closeModal();
    }

    public function delete($id)
    {
        $domain = TenantDomain::findOrFail($id);
        $domainName = $domain->domain;
        $domain->delete();

        \Illuminate\Support\Facades\Cache::forget('tenant_domain_' . strtolower($domainName));

        session()->flash('message', 'Domain tenant berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $domain = TenantDomain::findOrFail($id);
        $domain->is_active = !$domain->is_active;
        $domain->save();

        \Illuminate\Support\Facades\Cache::forget('tenant_domain_' . strtolower($domain->domain));
    }

    public function render()
    {
        $domains = TenantDomain::query()
            ->with('tenant')
            ->when($this->search, function ($query) {
                $query->where('domain', 'like', '%' . $this->search . '%')
                    ->orWhereHas('tenant', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        $tenants = Tenant::orderBy('name')->get();

        return view('livewire.saas.tenant-domain-list', [
            'domains' => $domains,
            'tenants' => $tenants,
        ]);
    }
}
