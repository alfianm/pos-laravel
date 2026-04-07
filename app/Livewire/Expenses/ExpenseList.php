<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ExpenseList extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $date_from;
    public $date_to;

    public function mount()
    {
        $this->date_from = date('Y-m-01');
        $this->date_to = date('Y-m-t');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $expense = Expense::find($id);
        if ($expense) {
            $expense->delete();
            session()->flash('message', 'Pengeluaran telah dihapus.');
        }
    }

    public function render()
    {
        $expenses = Expense::with(['category', 'branch', 'user'])
            ->when($this->search, function($query) {
                $query->where('expense_no', 'ilike', '%' . $this->search . '%')
                      ->orWhere('notes', 'ilike', '%' . $this->search . '%');
            })
            ->when($this->category_id, function($query) {
                $query->where('expense_category_id', $this->category_id);
            })
            ->whereBetween('date', [$this->date_from, $this->date_to])
            ->latest()
            ->paginate(10);

        return view('livewire.expenses.expense-list', [
            'expenses' => $expenses,
            'categories' => \App\Models\ExpenseCategory::orderBy('name')->get()
        ]);
    }
}
