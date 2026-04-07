<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceAccount;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AccountList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $platform = '';

    public $name = '';

    public $api_key = '';

    public $api_secret = '';

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'platform' => 'required|string|in:tokopedia,shopee,lazada,bukalapak,blibli',
            'name' => 'required|string|max:150',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
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
        $account = MarketplaceAccount::findOrFail($id);

        $this->platform = $account->marketplace;
        $this->name = $account->name;
        $this->api_key = $account->api_key ?? '';
        $this->api_secret = $account->api_secret ?? '';

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->platform = '';
        $this->name = '';
        $this->api_key = '';
        $this->api_secret = '';
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
            'marketplace' => $this->platform,
            'name' => $this->name,
            'api_key' => $this->api_key ?: null,
            'api_secret' => $this->api_secret ?: null,
        ];

        if ($this->editId) {
            $account = MarketplaceAccount::findOrFail($this->editId);
            $account->update($data);
            session()->flash('message', 'Akun marketplace berhasil diperbarui.');
        } else {
            MarketplaceAccount::create($data);
            session()->flash('message', 'Akun marketplace berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $account = MarketplaceAccount::findOrFail($id);
        $account->delete();
        session()->flash('message', 'Akun marketplace berhasil dihapus.');
    }

    public function getPlatformIcon($platform)
    {
        return match ($platform) {
            'tokopedia' => '🛒',
            'shopee' => '🛍️',
            'lazada' => '🏪',
            'bukalapak' => '📦',
            'blibli' => '🎯',
            default => '🌐',
        };
    }

    public function getPlatformColor($platform)
    {
        return match ($platform) {
            'tokopedia' => 'emerald',
            'shopee' => 'orange',
            'lazada' => 'blue',
            'bukalapak' => 'red',
            'blibli' => 'indigo',
            default => 'gray',
        };
    }

    public function render()
    {
        $accounts = MarketplaceAccount::where(function ($query) {
            $query->where('name', 'ilike', '%'.$this->search.'%')
                ->orWhere('marketplace', 'ilike', '%'.$this->search.'%');
        })
            ->latest()
            ->paginate(10);

        return view('livewire.marketplace.account-list', [
            'accounts' => $accounts,
        ]);
    }
}
