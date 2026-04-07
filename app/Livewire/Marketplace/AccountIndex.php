<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceAccount;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AccountIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['refreshAccounts' => '$refresh'];

    public function delete($id)
    {
        $account = MarketplaceAccount::findOrFail($id);
        $account->delete();
        session()->flash('message', 'Akun Marketplace berhasil dihapus.');
    }

    public function render()
    {
        $accounts = MarketplaceAccount::where('name', 'ilike', '%'.$this->search.'%')
            ->orWhere('marketplace', 'ilike', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.marketplace.account-index', [
            'accounts' => $accounts,
        ]);
    }
}
