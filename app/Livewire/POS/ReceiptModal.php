<?php

namespace App\Livewire\POS;

use App\Models\Sale;
use Livewire\Component;

class ReceiptModal extends Component
{
    public $isOpen = false;
    public $saleId = null;
    public $sale = null;

    protected $listeners = ['showReceipt'];

    public function showReceipt($saleId)
    {
        $this->saleId = $saleId;
        $this->sale = Sale::with(['items.product', 'customer', 'branch', 'cashier'])->find($saleId);
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->saleId = null;
        $this->sale = null;
    }

    public function render()
    {
        return view('livewire.p-o-s.receipt-modal');
    }
}
