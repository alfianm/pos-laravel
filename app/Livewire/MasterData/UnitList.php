<?php

namespace App\Livewire\MasterData;

use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UnitList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $name;

    public $short_name;

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'short_name' => 'required|string|max:20',
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
        $unit = Unit::findOrFail($id);

        $this->name = $unit->name;
        $this->short_name = $unit->short_name;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->short_name = '';
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

        if ($this->editId) {
            $unit = Unit::findOrFail($this->editId);
            $unit->update([
                'name' => $this->name,
                'short_name' => $this->short_name,
            ]);
            session()->flash('message', 'Satuan Berhasil Diperbarui.');
        } else {
            Unit::create([
                'name' => $this->name,
                'short_name' => $this->short_name,
            ]);
            session()->flash('message', 'Satuan Berhasil Ditambahkan.');
        }

        \Illuminate\Support\Facades\Cache::forget('units_tenant_'.auth()->user()->tenant_id);
        $this->closeModal();
    }

    public function delete($id)
    {
        $unit = Unit::findOrFail($id);
        
        // Cek jika ada produk yang menggunakan unit ini
        if (\App\Models\Product::where('unit_id', $id)->exists()) {
            session()->flash('error', 'Unit tidak bisa dihapus karena masih digunakan oleh beberapa produk.');
            return;
        }

        $unit->delete();
        session()->flash('message', 'Satuan Berhasil Dihapus.');
    }

    public function render()
    {
        $units = Unit::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('short_name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.master-data.unit-list', [
            'units' => $units,
        ]);
    }
}
