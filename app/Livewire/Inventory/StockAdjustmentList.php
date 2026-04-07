<?php

namespace App\Livewire\Inventory;

use App\Models\StockAdjustment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class StockAdjustmentList extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $adjustments = StockAdjustment::with(['branch', 'performedBy'])
            ->where('branch_id', auth()->user()->active_branch_id)
            ->when($this->search, function($query) {
                $query->where('adjustment_no', 'ilike', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.inventory.stock-adjustment-list', [
            'adjustments' => $adjustments
        ]);
    }
}
