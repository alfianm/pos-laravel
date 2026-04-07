<?php

namespace App\Livewire\Sales;

use App\Models\SaleReturn;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReturnList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $date_from = '';
    public $date_to = '';

    protected $queryString = ['search', 'status', 'date_from', 'date_to'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $returns = SaleReturn::with(['customer', 'user', 'branch', 'sale'])
            ->where('branch_id', auth()->user()->active_branch_id)
            ->when($this->search, function ($query) {
                $query->where('return_number', 'ilike', '%' . $this->search . '%')
                      ->orWhereHas('sale', function($q) {
                          $q->where('sale_no', 'ilike', '%' . $this->search . '%');
                      });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->date_from, function ($query) {
                $query->whereDate('return_date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($query) {
                $query->whereDate('return_date', '<=', $this->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.sales.return-list', [
            'returns' => $returns
        ]);
    }
}
