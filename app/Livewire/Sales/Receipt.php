<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Receipt extends Component
{
    public $sale;

    public function mount($saleId)
    {
        $this->sale = Sale::with(['items.product', 'customer', 'branch', 'payments', 'creator'])
            ->where('branch_id', auth()->user()->active_branch_id)
            ->findOrFail($saleId);
    }

    public function render()
    {
        return view('livewire.sales.receipt');
    }
}
