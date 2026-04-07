<?php

namespace App\Livewire\Membership;

use App\Models\Voucher;
use Livewire\Component;
use Livewire\WithPagination;

class VoucherIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function deletingVoucher($id)
    {
        $voucher = Voucher::findOrFail($id);
        
        if ($voucher->used_count > 0) {
            session()->flash('error', 'Tidak bisa menghapus voucher yang sudah pernah digunakan.');
            return;
        }

        $voucher->delete();
        session()->flash('success', 'Voucher berhasil dihapus.');
    }

    public function render()
    {
        $vouchers = Voucher::query()
            ->when($this->search, function($query) {
                $query->where('code', 'ilike', '%' . $this->search . '%')
                      ->orWhere('type', 'ilike', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.membership.voucher-index', [
            'vouchers' => $vouchers
        ]);
    }
}
