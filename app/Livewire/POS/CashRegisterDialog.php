<?php

namespace App\Livewire\POS;

use App\Models\CashAdjustment;
use App\Models\CashRegisterSession;
use Livewire\Component;

class CashRegisterDialog extends Component
{
    public $isOpen = false;

    public $mode = 'open';

    public $opening_balance = 0;

    public $closing_cash_submitted = 0;

    public $notes = '';

    public $activeSession = null;

    public $showAdjustmentModal = false;

    public $adjustmentType = 'cash_in';

    public $adjustmentAmount = 0;

    public $adjustmentReason = '';

    public $adjustmentNotes = '';

    protected $listeners = ['openCashRegister' => 'show'];

    protected function rules()
    {
        return [
            'opening_balance' => 'required_if:mode,open|numeric|min:0',
            'closing_cash_submitted' => 'required_if:mode,close|numeric|min:0',
            'adjustmentAmount' => 'required_if:showAdjustmentModal,true|numeric|min:1',
            'adjustmentReason' => 'required_if:showAdjustmentModal,true|string|max:255',
        ];
    }

    public function mount()
    {
        $this->activeSession = CashRegisterSession::where('user_id', auth()->id())
            ->where('branch_id', auth()->user()->active_branch_id)
            ->where('status', 'open')
            ->first();
    }

public function show($mode = 'open')
    {
        $this->mode = $mode;
        $this->isOpen = true;
        
        $this->mount();
        
        if ($mode === 'close' && $this->activeSession) {
            $this->closing_cash_submitted = $this->activeSession->expected_cash;
        }
        
        if ($mode === 'cash_in' || $mode === 'cash_out') {
            $this->showAdjustmentModal = true;
            $this->adjustmentType = $mode;
            $this->adjustmentAmount = 0;
            $this->adjustmentReason = '';
            $this->adjustmentNotes = '';
        }
    }

    public function openRegister()
    {
        $this->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        CashRegisterSession::create([
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => auth()->user()->active_branch_id,
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_balance' => $this->opening_balance,
            'status' => 'open',
        ]);

        $this->isOpen = false;
        $this->dispatch('refreshPOS');
        session()->flash('success', 'Shift kasir berhasil dibuka.');

        return redirect()->route('pos.index');
    }

    public function closeRegister()
    {
        if (! $this->activeSession) {
            return;
        }

        $this->activeSession->update([
            'closed_at' => now(),
            'total_cash_submitted' => $this->closing_cash_submitted,
            'notes' => $this->notes,
            'status' => 'closed',
        ]);

        $this->isOpen = false;
        $this->dispatch('refreshPOS');
        session()->flash('success', 'Shift kasir berhasil ditutup.');

        return redirect()->route('pos.index');
    }

    public function openAdjustmentModal($type)
    {
        $this->adjustmentType = $type;
        $this->adjustmentAmount = 0;
        $this->adjustmentReason = '';
        $this->adjustmentNotes = '';
        $this->showAdjustmentModal = true;
    }

    public function processAdjustment()
    {
        $this->validate([
            'adjustmentAmount' => 'required|numeric|min:1',
            'adjustmentReason' => 'required|string|max:255',
        ]);

        if (! $this->activeSession) {
            session()->flash('error', 'Tidak ada shift aktif.');

            return;
        }

        CashAdjustment::create([
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => auth()->user()->active_branch_id,
            'cash_register_session_id' => $this->activeSession->id,
            'user_id' => auth()->id(),
            'type' => $this->adjustmentType,
            'amount' => $this->adjustmentAmount,
            'reason' => $this->adjustmentReason,
            'notes' => $this->adjustmentNotes,
        ]);

        $this->activeSession->refresh();
        $this->showAdjustmentModal = false;

        $typeLabel = $this->adjustmentType === 'cash_in' ? 'Uang masuk' : 'Uang keluar';
        session()->flash('success', "{$typeLabel} berhasil dicatat.");

        $this->dispatch('refreshPOS');
    }

    public function render()
    {
        return view('livewire.p-o-s.cash-register-dialog');
    }
}
