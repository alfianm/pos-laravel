<?php

namespace App\Livewire\CRM;

use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class ProposalForm extends Component
{
    public $lead_id, $customer_id, $proposal_no, $proposal_date, $valid_until, $notes, $terms_conditions;
    public $items = [];
    public $subtotal = 0, $tax_amount = 0, $discount_amount = 0, $total_amount = 0;

    public function mount($leadId = null, $customerId = null)
    {
        $this->lead_id = $leadId;
        $this->customer_id = $customerId;
        $this->proposal_date = now()->format('Y-m-d');
        $this->valid_until = now()->addDays(14)->format('Y-m-d');
        $this->proposal_no = 'PROP-' . strtoupper(bin2hex(random_bytes(3)));
        
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'product_variant_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id') {
            $product = Product::find($value);
            if ($product) {
                $this->items[$index]['unit_price'] = $product->price;
                $this->items[$index]['product_name'] = $product->name;
            }
        }

        $this->items[$index]['total'] = $this->items[$index]['quantity'] * $this->items[$index]['unit_price'];
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->items)->sum('total');
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount;
    }

    public function save()
    {
        $this->validate([
            'proposal_date' => 'required|date',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::transaction(function () {
            $proposal = Proposal::create([
                'tenant_id' => auth()->user()->tenant_id,
                'branch_id' => auth()->user()->active_branch_id,
                'lead_id' => $this->lead_id,
                'customer_id' => $this->customer_id,
                'created_by' => auth()->id(),
                'proposal_no' => $this->proposal_no,
                'proposal_date' => $this->proposal_date,
                'valid_until' => $this->valid_until,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'total_amount' => $this->total_amount,
                'notes' => $this->notes,
                'terms_conditions' => $this->terms_conditions,
                'status' => 'draft',
            ]);

            foreach ($this->items as $item) {
                $proposal->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => Product::find($item['product_id'])->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['total'],
                    'total' => $item['total'],
                ]);
            }
            
            // Log to timeline if lead
            if ($this->lead_id) {
                \App\Models\CustomerTimeline::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'lead_id' => $this->lead_id,
                    'event_type' => 'proposal_sent',
                    'title' => 'Proposal Created: ' . $this->proposal_no,
                    'description' => 'Penawaran harga baru senilai Rp ' . number_format($this->total_amount, 0, ',', '.'),
                ]);
            }
        });

        session()->flash('success', 'Proposal berhasil dibuat!');
        return redirect()->route('crm.proposals.index');
    }

    public function render()
    {
        return view('livewire.c-r-m.proposal-form', [
            'products' => Product::all(),
            'leads' => Lead::all(),
        ]);
    }
}
