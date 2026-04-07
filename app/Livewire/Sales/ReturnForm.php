<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\ReturnReason;
use App\Services\ReturnService;
use App\Constants\ReturnStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class ReturnForm extends Component
{
    public $sale_id = '';
    public $return_date;
    public $notes = '';
    
    // For sale search
    public $search_sale = '';
    public $selectedSale = null;
    public $saleSearchResults = [];
    
    // For items to return
    public $returnedItems = []; // format: [sale_item_id => ['qty' => 0, 'reason_id' => '', 'notes' => '']]
    
    public $return_reasons = [];

    protected $rules = [
        'sale_id' => 'required|exists:sales,id',
        'return_date' => 'required|date',
        'returnedItems' => 'required|array|min:1',
        'returnedItems.*.qty' => 'required|numeric|min:0.001',
    ];

    public function mount()
    {
        $this->return_date = date('Y-m-d');
        $this->return_reasons = ReturnReason::where('is_active', true)->get();
    }

    public function updatedSearchSale($value)
    {
        if (strlen($value) < 3) {
            $this->saleSearchResults = [];
            return;
        }

        $this->saleSearchResults = Sale::where('branch_id', auth()->user()->active_branch_id)
            ->where('sale_no', 'ilike', '%' . $value . '%')
            ->where('status', 'completed')
            ->limit(5)
            ->get();
    }

    public function selectSale($saleId)
    {
        $this->selectedSale = Sale::with('items.product')->find($saleId);
        $this->sale_id = $saleId;
        $this->search_sale = $this->selectedSale->sale_no;
        $this->saleSearchResults = [];
        
        $this->returnedItems = [];
        foreach ($this->selectedSale->items as $item) {
            $this->returnedItems[$item->id] = [
                'sale_item_id' => $item->id,
                'product_name' => $item->product->name,
                'available_qty' => $item->qty, // ideally subtract already returned qty
                'qty' => 0,
                'price' => $item->unit_price,
                'reason_id' => '',
                'notes' => ''
            ];
        }
    }

    public function save(ReturnService $returnService)
    {
        // Filter items with qty > 0
        $filteredItems = array_filter($this->returnedItems, function($item) {
            return $item['qty'] > 0;
        });

        if (empty($filteredItems)) {
            $this->addError('returnedItems', 'Harus memilih setidaknya satu item untuk direturn.');
            return;
        }

        // Validate qtys
        foreach ($filteredItems as $id => $item) {
            if ($item['qty'] > $item['available_qty']) {
                $this->addError("returnedItems.{$id}.qty", "Jumlah return tidak boleh melebihi jumlah beli ({$item['available_qty']}).");
                return;
            }
        }

        try {
            $return = $returnService->createReturn([
                'sale_id' => $this->sale_id,
                'return_date' => $this->return_date,
                'notes' => $this->notes,
                'items' => array_values($filteredItems)
            ]);

            // For MVP, auto-approve and complete if permission exists?
            // User likely wants to complete it immediately in most POS scenarios
            $returnService->completeReturn($return);

            session()->flash('message', 'Return berhasil dibuat dan diproses.');
            return redirect()->route('sales.returns.index');
        } catch (\Exception $e) {
            Log::error('Return Error: ' . $e->getMessage());
            $this->addError('general', 'Terjadi kesalahan saat memproses return: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.return-form');
    }
}
