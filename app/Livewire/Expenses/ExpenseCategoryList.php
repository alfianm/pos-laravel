<?php

namespace App\Livewire\Expenses;

use App\Models\ExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ExpenseCategoryList extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $isEdit = false;
    public $categoryId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        if ($this->isEdit) {
            ExpenseCategory::find($this->categoryId)->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
        } else {
            ExpenseCategory::create([
                'tenant_id' => auth()->user()->tenant_id,
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }

        $this->reset(['name', 'description', 'isEdit', 'categoryId']);
        session()->flash('message', 'Kategori berhasil disimpan.');
    }

    public function edit($id)
    {
        $cat = ExpenseCategory::find($id);
        $this->isEdit = true;
        $this->categoryId = $id;
        $this->name = $cat->name;
        $this->description = $cat->description;
    }

    public function delete($id)
    {
        ExpenseCategory::find($id)->delete();
        session()->flash('message', 'Kategori dihapus.');
    }

    public function render()
    {
        return view('livewire.expenses.expense-category-list', [
            'categories' => ExpenseCategory::latest()->paginate(10)
        ]);
    }
}
