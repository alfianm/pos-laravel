<?php

namespace App\Livewire\MasterData;

use App\Models\CustomerGroup;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CustomerGroupList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $name;

    public $description;

    public $discount_percentage = 0;

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editId = $id;
        $group = CustomerGroup::findOrFail($id);

        $this->name = $group->name;
        $this->description = $group->description;
        $this->discount_percentage = $group->discount_percentage;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->description = '';
        $this->discount_percentage = 0;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'discount_percentage' => $this->discount_percentage,
        ];

        if ($this->editId) {
            $group = CustomerGroup::findOrFail($this->editId);
            $group->update($data);
            session()->flash('message', 'Grup Pelanggan Berhasil Diperbarui.');
        } else {
            CustomerGroup::create($data);
            session()->flash('message', 'Grup Pelanggan Berhasil Ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $group = CustomerGroup::findOrFail($id);
        
        if ($group->customers()->count() > 0) {
            session()->flash('error', 'Grup tidak bisa dihapus karena masih memiliki pelanggan.');
            return;
        }

        $group->delete();
        session()->flash('message', 'Grup Pelanggan Berhasil Dihapus.');
    }

    public function render()
    {
        $groups = CustomerGroup::where('name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.master-data.customer-group-list', [
            'groups' => $groups,
        ]);
    }
}
