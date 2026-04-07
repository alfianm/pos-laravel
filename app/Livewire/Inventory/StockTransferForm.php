<?php

namespace App\Livewire\Inventory;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Services\StockService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class StockTransferForm extends Component
{
    public $transfer_no;
    public $from_branch_id;
    public $to_branch_id;
    public $transfer_date;
    public $notes;
    public $items = [];
    public $search = '';
    public $searchResults = [];

    public function mount()
    {
        $this->transfer_date = date('Y-m-d');
        $this->transfer_no = 'TRF/' . date('Y/m/d') . '/' . strtoupper(Str::random(4));
        $this->from_branch_id = auth()->user()->active_branch_id;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::with(['variants', 'inventories' => function($q) {
                $q->where('branch_id', $this->from_branch_id);
            }])
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('sku', 'ilike', '%' . $this->search . '%');
            })
            ->limit(10)
            ->get();
    }

    public function addItem($productId, $variantId = null)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $variant = $variantId ? ProductVariant::find($variantId) : null;
        
        $itemKey = $productId . ($variantId ? '-' . $variantId : '');
        
        // Prevent duplicate items
        foreach ($this->items as $index => $item) {
            if (($item['product_id'] == $productId) && ($item['product_variant_id'] == $variantId)) {
                $this->items[$index]['qty']++;
                $this->search = '';
                $this->searchResults = [];
                return;
            }
        }

        // Get current stock at source
        $inventory = \App\Models\Inventory::where('branch_id', $this->from_branch_id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        $this->items[] = [
            'product_id' => $product->id,
            'product_name' => $product->name . ($variant ? " ({$variant->name})" : ""),
            'product_variant_id' => $variantId,
            'qty' => 1,
            'available_qty' => (float)($inventory->qty_on_hand ?? 0),
        ];

        $this->search = '';
        $this->searchResults = [];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockService $stockService)
    {
        $this->validate([
            'to_branch_id' => 'required|different:from_branch_id',
            'transfer_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($stockService) {
                $transfer = StockTransfer::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'from_branch_id' => $this->from_branch_id,
                    'to_branch_id' => $this->to_branch_id,
                    'transfer_no' => $this->transfer_no,
                    'transfer_date' => $this->transfer_date,
                    'status' => 'sent', // For simple MVP we assume it's sent immediately
                    'created_by' => auth()->id(),
                    'sent_by' => auth()->id(),
                    'notes' => $this->notes,
                ]);

                foreach ($this->items as $item) {
                    StockTransferItem::create([
                        'stock_transfer_id' => $transfer->id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'qty' => $item['qty'],
                    ]);

                    // 1. Stock Out from FromBranch
                    $stockService->recordMovement([
                        'tenant_id' => auth()->user()->tenant_id,
                        'branch_id' => $this->from_branch_id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'qty' => -$item['qty'],
                        'movement_type' => 'transfer_out',
                        'reference_type' => 'StockTransfer',
                        'reference_id' => $transfer->id,
                        'reference_no' => $transfer->transfer_no,
                        'performed_by' => auth()->id(),
                    ]);

                    // 2. Stock In to ToBranch (Auto-receive for MVP)
                    $stockService->recordMovement([
                        'tenant_id' => auth()->user()->tenant_id,
                        'branch_id' => $this->to_branch_id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                        'qty' => $item['qty'],
                        'movement_type' => 'transfer_in',
                        'reference_type' => 'StockTransfer',
                        'reference_id' => $transfer->id,
                        'reference_no' => $transfer->transfer_no,
                        'performed_by' => auth()->id(),
                    ]);
                }

                $transfer->update([
                    'status' => 'received',
                    'received_date' => now(),
                    'received_by' => auth()->id()
                ]);
            });

            session()->flash('success', 'Mutasi stok antar cabang berhasil diproses.');
            return redirect()->route('inventory.transfers.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses transfer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory.stock-transfer-form', [
            'branches' => Branch::where('id', '!=', $this->from_branch_id)->where('status', 'active')->get()
        ]);
    }
}
