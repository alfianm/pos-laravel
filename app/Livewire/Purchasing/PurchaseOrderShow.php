<?php

namespace App\Livewire\Purchasing;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Inventory;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PurchaseOrderShow extends Component
{
    public $purchaseOrderId;
    public $purchaseOrder;

    public function mount($purchaseOrder)
    {
        $this->purchaseOrderId = $purchaseOrder;
        $this->loadPO();
    }

    public function loadPO()
    {
        $this->purchaseOrder = PurchaseOrder::with(['supplier', 'branch', 'items.product', 'user', 'payments'])
            ->find($this->purchaseOrderId);
    }

    public function submitOrder()
    {
        if ($this->purchaseOrder->status !== 'pending') return;

        $this->purchaseOrder->update(['status' => 'ordered']);
        $this->loadPO();
        session()->flash('success', 'PO telah ditandai sebagai TERKIRIM (Ordered).');
    }

    public function receiveGoods(StockService $stockService)
    {
        if (!in_array($this->purchaseOrder->status, ['ordered', 'partial'])) return;

        try {
            DB::transaction(function () use ($stockService) {
                foreach ($this->purchaseOrder->items as $item) {
                    // In simple MVP, we receive all items
                    $remainingQty = $item->qty - $item->received_qty;

                    if ($remainingQty > 0) {
                        $item->update([
                            'received_qty' => $item->qty
                        ]);

                        // Record Stock Movement (Addition)
                        $stockService->recordMovement([
                            'tenant_id' => $this->purchaseOrder->tenant_id,
                            'branch_id' => $this->purchaseOrder->branch_id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'qty' => $remainingQty,
                            'unit_cost' => $item->unit_cost,
                            'movement_type' => 'purchase',
                            'reference_type' => 'PurchaseOrder',
                            'reference_id' => $this->purchaseOrder->id,
                            'reference_no' => $this->purchaseOrder->po_no,
                            'performed_by' => auth()->id(),
                        ]);
                    }
                }

                $this->purchaseOrder->update(['status' => 'received']);
                
                event(new \App\Events\PurchaseOrderReceived($this->purchaseOrder));
            });

            $this->loadPO();
            session()->flash('success', 'Barang telah diterima dan stok telah diperbarui.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.purchasing.purchase-order-show');
    }
}
