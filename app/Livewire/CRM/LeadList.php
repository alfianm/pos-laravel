<?php

namespace App\Livewire\CRM;

use App\Models\Lead;
use App\Models\LeadStage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class LeadList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStage = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $leads = Lead::query()
            ->where('tenant_id', $tenantId)
            ->with(['source:id,name', 'stage:id,name', 'assignee:id,name'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('lead_no', 'ilike', '%' . $this->search . '%')
                      ->orWhere('phone', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStage, function($query) {
                $query->where('lead_stage_id', $this->filterStage);
            })
            ->latest()
            ->paginate(10);

        // Optimasi: Cache stages (1 jam)
        $stages = Cache::remember("lead_stages_tenant_{$tenantId}", 3600, function() use ($tenantId) {
            return LeadStage::where('tenant_id', $tenantId)->orderBy('sort_order')->get(['id', 'name'])->toArray();
        });
        $stages = collect($stages)->map(fn($s) => (object)$s);

        return view('livewire.c-r-m.lead-list', [
            'leads' => $leads,
            'stages' => $stages
        ]);
    }
}
