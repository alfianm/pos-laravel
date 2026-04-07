<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;
use App\Models\CashRegisterSession;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ExpenseForm extends Component
{
    public $date;
    public $expense_category_id = '';
    public $amount = 0;
    public $payment_method = 'cash';
    public $notes = '';
    public $branch_id;

    protected $rules = [
        'date' => 'required|date',
        'expense_category_id' => 'required|exists:expense_categories,id',
        'amount' => 'required|numeric|gt:0',
        'payment_method' => 'required|string',
    ];

    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->branch_id = auth()->user()->active_branch_id;
    }

    public function save()
    {
        $this->validate();

        // Find active register session for CURRENT user to link shift
        $activeSession = CashRegisterSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        Expense::create([
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => $this->branch_id,
            'expense_category_id' => $this->expense_category_id,
            'user_id' => auth()->id(),
            'cash_register_session_id' => $activeSession?->id,
            'expense_no' => $this->generateExpenseNo(),
            'date' => $this->date,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Pengeluaran telah dicatat.');
        return redirect()->route('expenses.index');
    }

    private function generateExpenseNo()
    {
        $prefix = 'EXP/' . date('Y/m/d');
        $count = Expense::where('tenant_id', auth()->user()->tenant_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        
        return $prefix . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.expenses.expense-form', [
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'branches' => Branch::where('status', 'active')->get()
        ]);
    }
}
