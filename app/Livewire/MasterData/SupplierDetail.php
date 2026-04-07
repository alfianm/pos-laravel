<?php

namespace App\Livewire\MasterData;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SupplierDetail extends Component
{
    use WithPagination;

    public $supplier;

    public $activeTab = 'info';

    public function mount($supplierId)
    {
        $this->supplier = Supplier::findOrFail($supplierId);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $purchaseOrders = [];
        if ($this->activeTab === 'purchases') {
            $purchaseOrders = PurchaseOrder::where('supplier_id', $this->supplier->id)
                ->latest()
                ->paginate(10);
        }

        return view('livewire.master-data.supplier-detail', [
            'purchaseOrders' => $purchaseOrders,
        ]);
    }
}
