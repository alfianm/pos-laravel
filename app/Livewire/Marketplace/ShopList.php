<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceAccount;
use App\Models\MarketplaceShop;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ShopList extends Component
{
    use WithPagination;

    public $search = '';

    public $account_filter = '';

    public $showModal = false;

    public $editId = null;

    public $marketplace_account_id;

    public $shop_id;

    public $name;

    protected $queryString = ['search', 'account_filter'];

    protected function rules()
    {
        return [
            'marketplace_account_id' => 'required|exists:marketplace_accounts,id',
            'shop_id' => 'required|string|max:100',
            'name' => 'required|string|max:150',
        ];
    }

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
        $shop = MarketplaceShop::findOrFail($id);

        $this->marketplace_account_id = $shop->marketplace_account_id;
        $this->shop_id = $shop->shop_id;
        $this->name = $shop->name;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->marketplace_account_id = null;
        $this->shop_id = '';
        $this->name = '';
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
            'marketplace_account_id' => $this->marketplace_account_id,
            'shop_id' => $this->shop_id,
            'name' => $this->name,
        ];

        if ($this->editId) {
            $shop = MarketplaceShop::findOrFail($this->editId);
            $shop->update($data);
            session()->flash('message', 'Toko berhasil diperbarui.');
        } else {
            MarketplaceShop::create($data);
            session()->flash('message', 'Toko berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $shop = MarketplaceShop::findOrFail($id);
        $shop->delete();
        session()->flash('message', 'Toko berhasil dihapus.');
    }

    public function render()
    {
        $shops = MarketplaceShop::with('account')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%'.$this->search.'%')
                        ->orWhere('shop_id', 'ilike', '%'.$this->search.'%');
                });
            })
            ->when($this->account_filter, function ($query) {
                $query->where('marketplace_account_id', $this->account_filter);
            })
            ->latest()
            ->paginate(15);

        $accounts = MarketplaceAccount::orderBy('name')->get();

        return view('livewire.marketplace.shop-list', [
            'shops' => $shops,
            'accounts' => $accounts,
        ]);
    }
}
