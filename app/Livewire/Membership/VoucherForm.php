<?php

namespace App\Livewire\Membership;

use App\Models\Voucher;
use Livewire\Component;
use Illuminate\Support\Str;

class VoucherForm extends Component
{
    public $voucherId = null;
    public $code;
    public $type = 'fixed'; // fixed, percentage
    public $value = 0;
    public $starts_at;
    public $ends_at;
    public $usage_limit;
    public $min_order_amount = 0;

    public $isEdit = false;

    protected $rules = [
        'code' => 'required|string|max:50',
        'type' => 'required|in:fixed,percentage',
        'value' => 'required|numeric|min:0',
        'starts_at' => 'nullable|date',
        'ends_at' => 'nullable|date|after_or_equal:starts_at',
        'usage_limit' => 'nullable|integer|min:1',
        'min_order_amount' => 'required|numeric|min:0',
    ];

    public function mount($voucherId = null)
    {
        if ($voucherId) {
            $this->voucherId = $voucherId;
            $this->isEdit = true;
            $voucher = Voucher::findOrFail($voucherId);
            $this->code = $voucher->code;
            $this->type = $voucher->type;
            $this->value = (float)$voucher->value;
            $this->starts_at = $voucher->starts_at ? $voucher->starts_at->format('Y-m-d\TH:i') : null;
            $this->ends_at = $voucher->ends_at ? $voucher->ends_at->format('Y-m-d\TH:i') : null;
            $this->usage_limit = $voucher->usage_limit;
            $this->min_order_amount = (float)$voucher->min_order_amount;
        } else {
            $this->starts_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function generateCode()
    {
        $this->code = strtoupper(Str::random(8));
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tenant_id' => auth()->user()->tenant_id,
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'value' => $this->value,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'usage_limit' => $this->usage_limit,
            'min_order_amount' => $this->min_order_amount,
        ];

        if ($this->isEdit) {
            Voucher::findOrFail($this->voucherId)->update($data);
            session()->flash('success', 'Voucher berhasil diperbarui!');
        } else {
            // Check for duplicate code in same tenant
            $exists = Voucher::where('tenant_id', $data['tenant_id'])
                ->where('code', $data['code'])
                ->exists();
            
            if ($exists) {
                $this->addError('code', 'Kode voucher sudah digunakan.');
                return;
            }

            Voucher::create($data);
            session()->flash('success', 'Voucher berhasil dibuat!');
        }

        return redirect()->route('membership.vouchers.index');
    }

    public function render()
    {
        return view('livewire.membership.voucher-form');
    }
}
