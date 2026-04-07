<?php

namespace App\Livewire\Membership;

use App\Models\MembershipTier;
use Livewire\Component;
use Livewire\WithPagination;

class TierIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function deletingTier($id)
    {
        $tier = MembershipTier::findOrFail($id);
        
        // Basic check if any accounts are using this (optional, can be expanded)
        if ($tier->loyaltyAccounts()->exists()) {
            session()->flash('error', 'Tidak bisa menghapus tier yang sedang digunakan oleh customer.');
            return;
        }

        $tier->delete();
        session()->flash('success', 'Tier berhasil dihapus.');
    }

    public function render()
    {
        $tiers = MembershipTier::query()
            ->when($this->search, function($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%');
            })
            ->orderBy('min_spending', 'asc')
            ->paginate(10);

        return view('livewire.membership.tier-index', [
            'tiers' => $tiers
        ]);
    }
}
