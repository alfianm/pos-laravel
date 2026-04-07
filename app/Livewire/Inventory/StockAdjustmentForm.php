<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Services\StockService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class StockAdjustmentForm extends Component
{
    public $adjustment_no;
    public $reason = 'Audit';
    public $notes;
    public $items = [];
    public $search = '';
    public $searchResults = [];

    public function mount()
    {
        $this->adjustment_no = 'ADJ-' . strtoupper(Str::random(10));
        
        if ($productId = request('product_id')) {
            $this->addItem($productId);
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::where('name', 'ilike', '%' . $this->search . '%')
            ->orWhere('sku', 'ilike', '%' . $this->search . '%')
            ->limit(5)
            ->get();
    }

    public function addItem($productId)
    {
        $product = Product::with(['inventories' => function($q) {
            $q->where('branch_id', auth()->user()->active_branch_id);
        }])->find($productId);

        if (!$product) return;

        $current_qty = $product->inventories->first()->qty_available ?? 0;

        foreach ($this->items as $item) {
            if ($item['product_id'] == $productId) return;
        }

        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'before_qty' => $current_qty,
            'adjusted_qty' => 0,
            'after_qty' => $current_qty,
            'notes' => '',
        ];

        $this->search = '';
        $this->searchResults = [];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        // key format like "0.adjusted_qty"
        if (strpos($key, '.adjusted_qty') !== false) {
            $index = explode('.', $key)[0];
            $this->items[$index]['after_qty'] = (float)$this->items[$index]['before_qty'] + (float)$value;
        }
    }

    public function save(StockService $stockService)
    {
        $this->validate([
            'reason' => 'required',
            'items' => 'required|array|min:1',
            'items.*.adjusted_qty' => 'required|numeric',
        ]);

        try {
            DB::transaction(function () use ($stockService) {
                $adjustment = StockAdjustment::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'branch_id' => auth()->user()->active_branch_id,
                    'adjustment_no' => $this->adjustment_no,
                    'reason' => $this->reason,
                    'status' => 'completed',
                    'notes' => $this->notes,
                    'performed_by' => auth()->id(),
                ]);

                foreach ($this->items as $item) {
                    StockAdjustmentItem::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'stock_adjustment_id' => $adjustment->id,
                        'product_id' => $item['product_id'],
                        'before_qty' => $item['before_qty'],
                        'adjusted_qty' => $item['adjusted_qty'],
                        'after_qty' => $item['after_qty'],
                        'notes' => $item['notes'],
                    ]);

                    // Update Inventory
                    $stockService->recordMovement([
                        'tenant_id' => auth()->user()->tenant_id,
                        'branch_id' => auth()->user()->active_branch_id,
                        'product_id' => $item['product_id'],
                        'qty' => $item['adjusted_qty'],
                        'movement_type' => 'adjustment',
                        'reference_type' => 'StockAdjustment',
                        'reference_id' => $adjustment->id,
                        'notes' => 'Adjustment ' . $adjustment->adjustment_no,
                        'performed_by' => auth()->id(),
                    ]);
                }
            });

            session()->flash('success', 'Stok berhasil disesuaikan.');
            return redirect()->route('inventory.adjustments.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory.stock-adjustment-form');
    }
}
