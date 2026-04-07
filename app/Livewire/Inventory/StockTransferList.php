<?php

namespace App\Livewire\Inventory;

use App\Models\StockTransfer;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class StockTransferList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $activeBranchId = auth()->user()->active_branch_id;

        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'requestedBy'])
            ->where(function($query) use ($activeBranchId) {
                $query->where('from_branch_id', $activeBranchId)
                      ->orWhere('to_branch_id', $activeBranchId);
            })
            ->when($this->search, function($query) {
                $query->where('transfer_no', 'ilike', '%' . $this->search . '%')
                      ->orWhere('notes', 'ilike', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.stock-transfer-list', [
            'transfers' => $transfers
        ]);
    }
}
