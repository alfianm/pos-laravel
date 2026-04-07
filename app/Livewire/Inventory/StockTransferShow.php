<?php

namespace App\Livewire\Inventory;

use App\Models\StockTransfer;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StockTransferShow extends Component
{
    public $transfer;

    public function mount($stockTransfer)
    {
        $this->transfer = StockTransfer::with(['fromBranch', 'toBranch', 'requestedBy', 'items.product'])
            ->findOrFail($stockTransfer);
    }

    public function render()
    {
        return view('livewire.inventory.stock-transfer-show');
    }
}
