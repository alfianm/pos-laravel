<?php

namespace App\Livewire\MasterData;

use App\Models\Brand;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BrandList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $name;

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
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
        $brand = Brand::findOrFail($id);

        $this->name = $brand->name;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
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
            $brand = Brand::findOrFail($this->editId);
            $brand->update(['name' => $this->name]);
            session()->flash('message', 'Brand Berhasil Diperbarui.');
        } else {
            Brand::create(['name' => $this->name]);
            session()->flash('message', 'Brand Berhasil Ditambahkan.');
        }

        \Illuminate\Support\Facades\Cache::forget('brands_tenant_'.auth()->user()->tenant_id);
        $this->closeModal();
    }

    public function delete($id)
    {
        $brand = Brand::findOrFail($id);

        // Cek jika ada produk yang menggunakan brand ini
        if (\App\Models\Product::where('brand_id', $id)->exists()) {
            session()->flash('error', 'Brand tidak bisa dihapus karena masih digunakan oleh beberapa produk.');
            return;
        }

        $brand->delete();
        session()->flash('message', 'Brand Berhasil Dihapus.');
    }

    public function render()
    {
        $brands = Brand::where('name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.master-data.brand-list', [
            'brands' => $brands,
        ]);
    }
}
