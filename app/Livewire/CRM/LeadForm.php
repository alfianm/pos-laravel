<?php

namespace App\Livewire\CRM;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Branch;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class LeadForm extends Component
{
    public $leadId;
    public $name, $email, $phone, $address, $notes;
    public $lead_source_id, $lead_stage_id, $branch_id, $assigned_to;
    public $isEdit = false;

    public function mount($lead = null)
    {
        if ($lead) {
            $this->leadId = $lead;
            $this->isEdit = true;
            $this->loadLead();
        } else {
            $this->lead_stage_id = LeadStage::orderBy('sort_order')->first()?->id;
            $this->branch_id = auth()->user()->active_branch_id;
        }
    }

    public function loadLead()
    {
        $lead = Lead::findOrFail($this->leadId);
        $this->name = $lead->name;
        $this->email = $lead->email;
        $this->phone = $lead->phone;
        $this->address = $lead->address;
        $this->notes = $lead->notes;
        $this->lead_source_id = $lead->lead_source_id;
        $this->lead_stage_id = $lead->lead_stage_id;
        $this->branch_id = $lead->branch_id;
        $this->assigned_to = $lead->assigned_to;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'lead_source_id' => 'required',
            'lead_stage_id' => 'required',
            'branch_id' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'notes' => $this->notes,
            'lead_source_id' => $this->lead_source_id,
            'lead_stage_id' => $this->lead_stage_id,
            'branch_id' => $this->branch_id,
            'assigned_to' => $this->assigned_to ?: auth()->id(),
        ];

        if ($this->isEdit) {
            $lead = Lead::findOrFail($this->leadId);
            $lead->update($data);
            $message = 'Lead berhasil diperbarui!';
        } else {
            $data['lead_no'] = 'LEAD-' . strtoupper(bin2hex(random_bytes(3)));
            $lead = Lead::create($data);
            $message = 'Lead berhasil ditambahkan!';

            // Log Timeline
            \App\Models\CustomerTimeline::create([
                'tenant_id' => auth()->user()->tenant_id,
                'lead_id' => $lead->id,
                'event_type' => 'lead_created',
                'title' => 'Lead Created',
                'description' => 'Prospek baru ditambahkan dari sumber ' . ($lead->source->name ?? 'Internal'),
            ]);
        }

        session()->flash('success', $message);
        return redirect()->route('crm.leads.index');
    }

    public function render()
    {
        return view('livewire.c-r-m.lead-form', [
            'sources' => LeadSource::all(),
            'stages' => LeadStage::orderBy('sort_order')->get(),
            'branches' => Branch::all(),
            'users' => User::all(),
        ]);
    }
}
