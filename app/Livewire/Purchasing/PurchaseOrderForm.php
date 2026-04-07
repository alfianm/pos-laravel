<?php

namespace App\Livewire\Purchasing;

use App\Models\Branch;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PurchaseOrderForm extends Component
{
    public $isEdit = false;
    public $purchaseOrderId = null;

    // Form Fields
    public $supplier_id = '';
    public $branch_id = '';
    public $order_date;
    public $expected_date;
    public $notes = '';
    
    // Dynamic Items
    public $items = []; // Each: product_id, product_name, qty, unit_cost, subtotal
    public $search_product = '';

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'branch_id' => 'required|exists:branches,id',
        'order_date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.qty' => 'required|numeric|gt:0',
        'items.*.unit_cost' => 'required|numeric|min:0',
    ];

    public function mount($purchaseOrder = null)
    {
        $this->order_date = date('Y-m-d');
        $this->branch_id = auth()->user()->active_branch_id;

        if ($purchaseOrder) {
            $this->isEdit = true;
            $this->purchaseOrderId = $purchaseOrder->id;
            $this->supplier_id = $purchaseOrder->supplier_id;
            $this->branch_id = $purchaseOrder->branch_id;
            $this->order_date = $purchaseOrder->order_date->format('Y-m-d');
            $this->expected_date = $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('Y-m-d') : '';
            $this->notes = $purchaseOrder->notes;

            foreach ($purchaseOrder->items as $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'qty' => (float)$item->qty,
                    'unit_cost' => (float)$item->unit_cost,
                    'subtotal' => (float)$item->subtotal,
                ];
            }
        }
    }

    public function addProduct($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        // Check if already in items
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] === $productId) {
                $this->items[$index]['qty']++;
                $this->calculateTotals();
                $this->search_product = '';
                return;
            }
        }

        $this->items[] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 1,
            'unit_cost' => (float)($product->purchase_price ?? 0),
            'subtotal' => (float)($product->purchase_price ?? 0),
        ];

        $this->calculateTotals();
        $this->search_product = '';
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['subtotal'] = $item['qty'] * $item['unit_cost'];
        }
    }

    public function getGrandTotalProperty()
    {
        return collect($this->items)->sum('subtotal');
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $data = [
                    'tenant_id' => auth()->user()->tenant_id,
                    'branch_id' => $this->branch_id,
                    'supplier_id' => $this->supplier_id,
                    'user_id' => auth()->id(),
                    'order_date' => $this->order_date,
                    'expected_date' => $this->expected_date ?: null,
                    'subtotal' => $this->grand_total,
                    'grand_total' => $this->grand_total,
                    'due_amount' => $this->grand_total,
                    'notes' => $this->notes,
                ];

                if (!$this->isEdit) {
                    $data['po_no'] = $this->generatePONo();
                    $po = PurchaseOrder::create($data);
                } else {
                    $po = PurchaseOrder::find($this->purchaseOrderId);
                    $po->update($data);
                    $po->items()->delete();
                }

                foreach ($this->items as $item) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'unit_cost' => $item['unit_cost'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            });

            session()->flash('message', $this->isEdit ? 'PO berhasil diperbarui' : 'PO berhasil dibuat');
            return redirect()->route('purchasing.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan PO: ' . $e->getMessage());
        }
    }

    private function generatePONo()
    {
        $prefix = 'PO/' . date('Y/m/d');
        $count = PurchaseOrder::where('tenant_id', auth()->user()->tenant_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        
        return $prefix . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        
        $products = [];
        if (strlen($this->search_product) >= 2) {
            $products = Product::where('name', 'ilike', '%' . $this->search_product . '%')
                ->orWhere('sku', 'ilike', '%' . $this->search_product . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.purchasing.purchase-order-form', [
            'suppliers' => $suppliers,
            'branches' => $branches,
            'search_results' => $products,
        ]);
    }
}
