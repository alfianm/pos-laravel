<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $date_from = '';
    public $date_to = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sales = Sale::with(['customer', 'creator', 'branch'])
            ->where('branch_id', auth()->user()->active_branch_id)
            ->when($this->search, function ($query) {
                $query->where('sale_no', 'ilike', '%' . $this->search . '%');
            })
            ->when($this->date_from, function ($query) {
                $query->whereDate('sale_date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($query) {
                $query->whereDate('sale_date', '<=', $this->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.sales.index', [
            'sales' => $sales
        ]);
    }
}
