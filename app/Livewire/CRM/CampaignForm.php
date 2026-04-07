<?php

namespace App\Livewire\Crm;

use App\Models\Campaign;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Livewire\Component;

class CampaignForm extends Component
{
    public ?string $campaignId = null;
    public string $name = '';
    public string $type = 'voucher';
    public string $target_segment = 'all';
    public ?string $voucher_id = null;
    public string $message = '';
    public string $status = 'draft';
    public string $starts_at = '';
    public string $ends_at = '';
    public float $bonus_points = 0;

    public function mount(?string $campaignId = null)
    {
        if ($campaignId) {
            $campaign = Campaign::findOrFail($campaignId);
            $this->campaignId = $campaign->id;
            $this->name = $campaign->name;
            $this->type = $campaign->type;
            $this->target_segment = $campaign->target_segment ?? 'all';
            $this->voucher_id = $campaign->voucher_id;
            $this->message = $campaign->message ?? '';
            $this->status = $campaign->status;
            $this->starts_at = $campaign->starts_at ? $campaign->starts_at->format('Y-m-d\TH:i') : '';
            $this->ends_at = $campaign->ends_at ? $campaign->ends_at->format('Y-m-d\TH:i') : '';
            $this->bonus_points = (float) $campaign->bonus_points;
        }
    }

    protected $rules = [
        'name' => 'required|min:3',
        'type' => 'required|in:voucher,broadcast,loyalty_bonus',
        'target_segment' => 'required',
        'starts_at' => 'nullable|date',
        'ends_at' => 'nullable|date|after:starts_at',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $this->name,
            'type' => $this->type,
            'target_segment' => $this->target_segment === 'all' ? null : $this->target_segment,
            'voucher_id' => $this->voucher_id,
            'message' => $this->message,
            'status' => $this->status,
            'starts_at' => $this->starts_at ?: null,
            'ends_at' => $this->ends_at ?: null,
            'bonus_points' => $this->bonus_points,
        ];

        if ($this->campaignId) {
            Campaign::findOrFail($this->campaignId)->update($data);
        } else {
            Campaign::create(array_merge($data, ['id' => Str::uuid()->toString()]));
        }

        $this->dispatch('swal', [
            'title' => 'Saved!',
            'text' => 'Campaign configuration stored securely.',
            'type' => 'success'
        ]);

        return redirect()->route('crm.campaigns.index');
    }

    public function render()
    {
        return view('livewire.crm.campaign-form', [
            'vouchers' => Voucher::where('is_active', true)->get(),
            'segments' => [
                'all' => 'All Customers',
                'Champions' => 'Champions',
                'Loyal Customers' => 'Loyal Customers',
                'Potential Loyalists' => 'Potential Loyalists',
                'At Risk' => 'At Risk',
                'Can\'t Lose Them' => 'Can\'t Lose Them',
                'Hibernating / Lost' => 'Hibernating / Lost',
            ]
        ])->layout('layouts.app');
    }
}
