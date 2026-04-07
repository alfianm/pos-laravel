<?php

namespace App\Livewire\CRM;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\CustomerTimeline;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class LeadShow extends Component
{
    public $leadId;
    
    protected $listeners = ['refreshTimeline' => '$refresh'];

    public function mount($leadId)
    {
        $this->leadId = $leadId;
    }

    public function convertToCustomer()
    {
        $lead = Lead::findOrFail($this->leadId);

        if ($lead->status === 'converted') {
            return;
        }

        DB::transaction(function () use ($lead) {
            // 1. Create Customer
            $customer = Customer::create([
                'tenant_id' => $lead->tenant_id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'address' => $lead->address,
                'code' => 'CUST-' . strtoupper(bin2hex(random_bytes(3))),
                'status' => 'active',
            ]);

            // 2. Update Lead
            $lead->update([
                'status' => 'converted',
                'converted_customer_id' => $customer->id,
                'converted_at' => now(),
            ]);

            // 3. Log Timeline
            CustomerTimeline::create([
                'tenant_id' => $lead->tenant_id,
                'lead_id' => $lead->id,
                'customer_id' => $customer->id,
                'event_type' => 'converted',
                'title' => 'Lead Converted to Customer',
                'description' => 'Prospek berhasil diubah menjadi pelanggan tetap.',
            ]);
        });

        session()->flash('success', 'Lead berhasil dikonversi!');
    }

    public function render()
    {
        $lead = Lead::withCount('followUps')->with(['source', 'stage', 'assignee', 'branch', 'timelines' => function($q) {
            $q->latest();
        }])->findOrFail($this->leadId);

        return view('livewire.c-r-m.lead-show', [
            'lead' => $lead
        ]);
    }
}
