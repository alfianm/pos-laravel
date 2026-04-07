<?php

namespace App\Livewire\Crm;

use App\Models\Campaign;
use App\Services\CampaignService;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function deleteCampaign(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();
        
        $this->dispatch('swal', [
            'title' => 'Deleted!',
            'text' => 'Campaign has been archived.',
            'type' => 'success'
        ]);
    }

    public function runCampaign(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update(['status' => 'running']);

        $service = new CampaignService();
        $result = $service->execute($campaign);

        if ($result['success']) {
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => "Campaign executed. Targeted {$result['reach']} customers.",
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $result['message'],
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        $query = Campaign::query()
            ->with('voucher')
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return view('livewire.crm.campaign-list', [
            'campaigns' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
