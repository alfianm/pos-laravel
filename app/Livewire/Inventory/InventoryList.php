<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InventoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $stock_status = ''; // all, low, out

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'stock_status' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $branchId = $user->active_branch_id;

        // Optimasi: Gunakan query builder yang konsisten
        $query = Inventory::query()
            ->where('branch_id', $branchId)
            ->with(['product:id,name,sku,category_id', 'product.category:id,name', 'variant:id,name']);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('sku', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->category_id) {
            $query->whereHas('product', function ($q) {
                $q->where('category_id', $this->category_id);
            });
        }

        if ($this->stock_status === 'low') {
            $query->whereColumn('qty_available', '<=', 'reorder_level')
                  ->where('qty_available', '>', 0);
        } elseif ($this->stock_status === 'out') {
            $query->where('qty_available', '<=', 0);
        }

        // Optimasi: Cache total stock value per branch (5 menit)
        $totalStockValue = Cache::remember("total_stock_value_branch_{$branchId}", 300, function() use ($branchId) {
            return Inventory::where('branch_id', $branchId)
                ->sum(DB::raw('qty_on_hand * avg_cost'));
        });

        // Optimasi: Cache categories per tenant (1 jam)
        $categories = Cache::remember("categories_tenant_{$tenantId}", 3600, function() use ($tenantId) {
            return ProductCategory::where('tenant_id', $tenantId)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        });
        $categories = collect($categories)->map(fn($c) => (object)$c);

        return view('livewire.inventory.inventory-list', [
            'inventories' => $query->latest()->paginate(10),
            'categories' => $categories,
            'totalStockValue' => $totalStockValue,
        ]);
    }
}
