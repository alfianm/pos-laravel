<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\Branch;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class OpeningStock extends Component
{
    public $items = [];
    public $search = '';
    public $notes = 'Initial stock entry';

    public function mount()
    {
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'qty' => 0,
            'unit_cost' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockService $stockService)
    {
        $this->validate([
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.0001',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $branchId = $user->active_branch_id;

        if (!$branchId) {
            $branchId = $user->branches()->first()?->id;
        }

        if (!$branchId) {
            session()->flash('error', 'Silakan pilih cabang aktif terlebih dahulu.');
            return;
        }

        foreach ($this->items as $item) {
            $stockService->recordMovement([
                'tenant_id' => $user->tenant_id,
                'branch_id' => $branchId,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'movement_type' => 'opening_stock',
                'reference_type' => 'OpeningStock',
                'unit_cost' => $item['unit_cost'],
                'notes' => $this->notes,
                'performed_by' => $user->id,
            ]);
        }

        session()->flash('success', 'Stok awal berhasil disimpan.');
        return redirect()->route('inventory.index');
    }

    public function render()
    {
        return view('livewire.inventory.opening-stock', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
