<?php

namespace App\Livewire\CRM;

use App\Models\Proposal;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProposalList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $proposals = Proposal::with(['lead', 'customer', 'creator'])
            ->when($this->search, function ($query) {
                $query->where('proposal_no', 'ilike', '%' . $this->search . '%')
                      ->orWhereHas('lead', function ($q) {
                          $q->where('name', 'ilike', '%' . $this->search . '%');
                      });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.c-r-m.proposal-list', [
            'proposals' => $proposals
        ]);
    }
}
