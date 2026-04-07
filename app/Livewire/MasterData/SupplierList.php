<?php

namespace App\Livewire\MasterData;

use App\Models\Supplier;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SupplierList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $name;

    public $code;

    public $email;

    public $phone;

    public $contact_person;

    public $address;

    public $city;

    public $status = 'active';

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50|unique:suppliers,code,'.$this->editId,
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:150',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|string',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->code = 'VND-'.strtoupper(Str::random(6));
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editId = $id;
        $supplier = Supplier::findOrFail($id);

        $this->name = $supplier->name;
        $this->code = $supplier->code;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->contact_person = $supplier->contact_person;
        $this->address = $supplier->address;
        $this->city = $supplier->city;
        $this->status = $supplier->status;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->code = '';
        $this->email = '';
        $this->phone = '';
        $this->contact_person = '';
        $this->address = '';
        $this->city = '';
        $this->status = 'active';
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
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->phone,
            'contact_person' => $this->contact_person,
            'address' => $this->address,
            'city' => $this->city,
            'status' => $this->status,
        ];

        if ($this->editId) {
            $supplier = Supplier::findOrFail($this->editId);
            $supplier->update($data);
            session()->flash('message', 'Supplier Berhasil Diperbarui.');
        } else {
            Supplier::create($data);
            session()->flash('message', 'Supplier Berhasil Ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        session()->flash('message', 'Supplier Berhasil Dihapus.');
    }

    public function render()
    {
        $suppliers = Supplier::where(function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('code', 'like', '%'.$this->search.'%')
                ->orWhere('contact_person', 'like', '%'.$this->search.'%');
        })
            ->latest()
            ->paginate(10);

        return view('livewire.master-data.supplier-list', [
            'suppliers' => $suppliers,
        ]);
    }
}
