<?php

namespace App\Livewire\MasterData;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CustomerList extends Component
{
    use WithPagination;

    public $search = '';

    public $group_filter = '';

    public $showModal = false;

    public $editId = null;

    public $name;

    public $email;

    public $phone;

    public $code;

    public $customer_group_id;

    public $address;

    public $city;

    public $status = 'active';

    protected $queryString = ['search', 'group_filter'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:customers,email,'.$this->editId,
            'phone' => 'nullable|string|max:50',
            'code' => 'required|string|max:50|unique:customers,code,'.$this->editId,
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|string|max:30',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->code = 'CST-'.date('ymd').strtoupper(Str::random(4));
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editId = $id;
        $customer = Customer::findOrFail($id);

        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->code = $customer->code;
        $this->customer_group_id = $customer->customer_group_id;
        $this->address = $customer->address;
        $this->city = $customer->city;
        $this->status = $customer->status;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->code = '';
        $this->customer_group_id = null;
        $this->address = '';
        $this->city = '';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'code' => $this->code,
            'customer_group_id' => $this->customer_group_id,
            'address' => $this->address,
            'city' => $this->city,
            'status' => $this->status,
        ];

        if ($this->editId) {
            $customer = Customer::findOrFail($this->editId);
            $customer->update($data);
            session()->flash('message', 'Pelanggan Berhasil Diperbarui.');
        } else {
            Customer::create($data);
            session()->flash('message', 'Pelanggan Berhasil Ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        session()->flash('message', 'Pelanggan Berhasil Dihapus.');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $customers = Customer::query()
            ->where('tenant_id', $tenantId)
            ->with('group:id,name')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%'.$this->search.'%')
                        ->orWhere('code', 'ilike', '%'.$this->search.'%')
                        ->orWhere('phone', 'ilike', '%'.$this->search.'%')
                        ->orWhere('email', 'ilike', '%'.$this->search.'%');
                });
            })
            ->when($this->group_filter, function ($query) {
                $query->where('customer_group_id', $this->group_filter);
            })
            ->latest()
            ->paginate(10);

        // Optimasi: Cache per tenant (1 jam)
        $groups = Cache::remember("customer_groups_tenant_{$tenantId}", 3600, function() use ($tenantId) {
            return CustomerGroup::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        });
        $groups = collect($groups)->map(fn($g) => (object)$g);

        return view('livewire.master-data.customer-list', [
            'customers' => $customers,
            'groups' => $groups,
        ]);
    }
}
