<?php

namespace App\Livewire\Saas;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SubscriptionPlanList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $code = '';

    public $name = '';

    public $description = '';

    public $billing_cycle = 'monthly';

    public $price_monthly = 0;

    public $price_yearly = 0;

    public $max_branches = 1;

    public $max_products = 100;

    public $max_users = 2;

    public $max_monthly_transactions = 500;

    public $storage_mb = 100;

    public $modules = '';

    public $support = 'email';

    public $api_access = false;

    public $custom_domain = false;

    public $white_label = false;

    public $is_active = true;

    public $is_public = true;

    public $sort_order = 0;

    protected $queryString = ['search'];

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
        $plan = SubscriptionPlan::findOrFail($id);

        $this->code = $plan->code;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->billing_cycle = $plan->billing_cycle;
        $this->price_monthly = $plan->price_monthly;
        $this->price_yearly = $plan->price_yearly;
        $this->max_branches = $plan->features['max_branches'] ?? 1;
        $this->max_products = $plan->features['max_products'] ?? 100;
        $this->max_users = $plan->features['max_users'] ?? 2;
        $this->max_monthly_transactions = $plan->features['max_monthly_transactions'] ?? 500;
        $this->storage_mb = $plan->features['storage_mb'] ?? 100;
        $this->modules = isset($plan->features['modules']) ? implode(', ', $plan->features['modules']) : '';
        $this->support = $plan->features['support'] ?? 'email';
        $this->api_access = $plan->features['api_access'] ?? false;
        $this->custom_domain = $plan->features['custom_domain'] ?? false;
        $this->white_label = $plan->features['white_label'] ?? false;
        $this->is_active = $plan->is_active;
        $this->is_public = $plan->is_public;
        $this->sort_order = $plan->sort_order;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->billing_cycle = 'monthly';
        $this->price_monthly = 0;
        $this->price_yearly = 0;
        $this->max_branches = 1;
        $this->max_products = 100;
        $this->max_users = 2;
        $this->max_monthly_transactions = 500;
        $this->storage_mb = 100;
        $this->modules = '';
        $this->support = 'email';
        $this->api_access = false;
        $this->custom_domain = false;
        $this->white_label = false;
        $this->is_active = true;
        $this->is_public = true;
        $this->sort_order = SubscriptionPlan::max('sort_order') + 1;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function prepareFeatures(): array
    {
        $moduleArray = $this->modules ? array_map('trim', explode(',', $this->modules)) : [];

        return [
            'max_branches' => (int) $this->max_branches,
            'max_products' => (int) $this->max_products,
            'max_users' => (int) $this->max_users,
            'max_monthly_transactions' => (int) $this->max_monthly_transactions,
            'storage_mb' => (int) $this->storage_mb,
            'modules' => $moduleArray,
            'support' => $this->support,
            'api_access' => (bool) $this->api_access,
            'custom_domain' => (bool) $this->custom_domain,
            'white_label' => (bool) $this->white_label,
        ];
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'billing_cycle' => 'required|in:monthly,yearly',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_branches' => 'required|integer|min:-1',
            'max_products' => 'required|integer|min:-1',
            'max_users' => 'required|integer|min:-1',
            'max_monthly_transactions' => 'required|integer|min:-1',
            'storage_mb' => 'required|integer|min:0',
            'modules' => 'nullable|string',
            'support' => 'required|string|max:50',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ];

        if ($this->editId) {
            $rules['code'] = 'required|string|max:50|unique:subscription_plans,code,'.$this->editId;
        } else {
            $rules['code'] = 'required|string|max:50|unique:subscription_plans,code';
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'code' => Str::slug($this->code),
            'name' => $this->name,
            'description' => $this->description,
            'billing_cycle' => $this->billing_cycle,
            'price_monthly' => $this->price_monthly,
            'price_yearly' => $this->price_yearly,
            'features' => $this->prepareFeatures(),
            'is_active' => $this->is_active,
            'is_public' => $this->is_public,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editId) {
            $plan = SubscriptionPlan::findOrFail($this->editId);
            $plan->update($data);
            session()->flash('message', 'Paket langganan berhasil diperbarui.');
        } else {
            $data['id'] = Str::uuid();
            SubscriptionPlan::create($data);
            session()->flash('message', 'Paket langganan berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        if (SubscriptionPlan::count() <= 1) {
            session()->flash('error', 'Tidak dapat menghapus paket terakhir.');

            return;
        }

        if ($plan->subscriptions()->exists()) {
            session()->flash('error', 'Paket tidak dapat dihapus karena masih digunakan oleh tenant.');

            return;
        }

        $plan->delete();
        session()->flash('message', 'Paket langganan berhasil dihapus.');
    }

    public function formatPrice($price): string
    {
        if ($price == 0) {
            return 'Gratis';
        }

        return 'Rp '.number_format($price, 0, ',', '.');
    }

    public function formatLimit($value): string
    {
        return $value == -1 ? '∞' : number_format($value);
    }

    public function render()
    {
        $plans = SubscriptionPlan::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('code', 'like', '%'.$this->search.'%')
            ->orderBy('sort_order')
            ->paginate(10);

        return view('livewire.saas.subscription-plan-list', [
            'plans' => $plans,
        ]);
    }
}
