<?php

namespace App\Livewire\Membership;

use App\Models\MembershipTier;
use Livewire\Component;

class TierForm extends Component
{
    public $tierId = null;
    public $name;
    public $min_spending = 0;
    public $point_multiplier = 1.0;

    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'min_spending' => 'required|numeric|min:0',
        'point_multiplier' => 'required|numeric|min:0.1|max:100',
    ];

    public function mount($tierId = null)
    {
        if ($tierId) {
            $this->tierId = $tierId;
            $this->isEdit = true;
            $tier = MembershipTier::findOrFail($tierId);
            $this->name = $tier->name;
            $this->min_spending = (float)$tier->min_spending;
            $this->point_multiplier = (float)$tier->point_multiplier;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'min_spending' => $this->min_spending,
            'point_multiplier' => $this->point_multiplier,
            'tenant_id' => auth()->user()->tenant_id,
        ];

        if ($this->isEdit) {
            MembershipTier::findOrFail($this->tierId)->update($data);
            session()->flash('success', 'Tier berhasil diperbarui!');
        } else {
            MembershipTier::create($data);
            session()->flash('success', 'Tier berhasil dibuat!');
        }

        return redirect()->route('membership.tiers.index');
    }

    public function render()
    {
        return view('livewire.membership.tier-form');
    }
}
