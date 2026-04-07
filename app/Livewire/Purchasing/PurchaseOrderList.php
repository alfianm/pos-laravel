<?php

namespace App\Livewire\Purchasing;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PurchaseOrderList extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'branch'])
            ->when($this->search, function($query) {
                $query->where('po_no', 'ilike', '%' . $this->search . '%');
            })
            ->when($this->status_filter, function($query) {
                $query->where('status', $this->status_filter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.purchasing.purchase-order-list', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }
}
