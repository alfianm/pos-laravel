<?php

namespace App\Livewire\MasterData;

use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\Product;
use App\Events\ProductDeleted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $category_filter = '';
    public $brand_filter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    protected $queryString = ['search', 'category_filter', 'brand_filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        
        $tenantId = $product->tenant_id;
        $productId = $product->id;
        $sku = $product->sku;

        $product->delete();
        
        event(new ProductDeleted($tenantId, $productId, $sku));
        session()->flash('message', 'Produk Berhasil Dihapus.');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->with(['category:id,name', 'brand:id,name', 'unit:id,name'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('sku', 'ilike', '%' . $this->search . '%')
                      ->orWhere('barcode', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->category_filter, function ($query) {
                $query->where('category_id', $this->category_filter);
            })
            ->when($this->brand_filter, function ($query) {
                $query->where('brand_id', $this->brand_filter);
            })
            ->latest()
            ->paginate(10);

        // Optimasi: Cache per tenant (1 jam)
        $categories = Cache::remember("categories_tenant_{$tenantId}", 3600, function() use ($tenantId) {
            return ProductCategory::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        });
        $categories = collect($categories)->map(fn($c) => (object)$c);

        $brands = Cache::remember("brands_tenant_{$tenantId}", 3600, function() use ($tenantId) {
            return Brand::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        });
        $brands = collect($brands)->map(fn($b) => (object)$b);

        return view('livewire.master-data.product-list', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
