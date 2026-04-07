<?php

namespace App\Livewire\MasterData;

use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CategoryList extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editId = null;

    public $name;

    public $parent_id;

    public $slug;

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:product_categories,id',
            'slug' => 'required|string|max:150|unique:product_categories,slug,'.$this->editId,
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editId = $id;
        $category = ProductCategory::findOrFail($id);

        $this->name = $category->name;
        $this->parent_id = $category->parent_id;
        $this->slug = $category->slug;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->parent_id = null;
        $this->slug = '';
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
            $category = ProductCategory::findOrFail($this->editId);
            $category->update([
                'name' => $this->name,
                'parent_id' => $this->parent_id,
                'slug' => $this->slug,
            ]);
            session()->flash('message', 'Kategori Berhasil Diperbarui.');
        } else {
            ProductCategory::create([
                'name' => $this->name,
                'parent_id' => $this->parent_id,
                'slug' => $this->slug,
            ]);
            session()->flash('message', 'Kategori Berhasil Ditambahkan.');
        }

        \Illuminate\Support\Facades\Cache::forget('categories_tenant_'.auth()->user()->tenant_id);
        $this->closeModal();
    }

    public function delete($id)
    {
        $category = ProductCategory::findOrFail($id);

        // Cek jika ada produk yang menggunakan kategori ini
        if (\App\Models\Product::where('category_id', $id)->exists()) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh beberapa produk.');
            return;
        }

        // Cek jika ada sub-kategori
        if (ProductCategory::where('parent_id', $id)->exists()) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena memiliki sub-kategori.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Kategori Berhasil Dihapus.');
    }

    public function render()
    {
        $categories = ProductCategory::with('parent')
            ->where('name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        $parents = ProductCategory::whereNull('parent_id')
            ->when($this->editId, function ($query) {
                return $query->where('id', '!=', $this->editId);
            })
            ->get();

        return view('livewire.master-data.category-list', [
            'categories' => $categories,
            'parents' => $parents,
        ]);
    }
}
