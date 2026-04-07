<?php

namespace App\Livewire\Dashboard;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\Inventory;
use App\Models\Expense;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class OwnerDashboard extends Component
{
    public $totalSales;
    public $totalProfit;
    public $lowStockItems;
    public $branchPerformance = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // 1. Total Dashboard Stats
        $this->totalSales = Sale::sum('grand_total');
        $this->totalProfit = Sale::sum('grand_total') - Sale::join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(DB::raw('sale_items.qty * products.cost_price'));

        // 2. Low Stock Count across all branches
        $this->lowStockItems = Inventory::where('qty_available', '<=', 10)->count();

        // 3. Branch Performance Data
        $this->branchPerformance = Branch::withCount('sales')
            ->withSum('sales', 'grand_total')
            ->get()
            ->map(function($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'sales_count' => $branch->sales_count,
                    'total_revenue' => $branch->sales_sum_grand_total ?: 0,
                    'low_stock' => Inventory::where('branch_id', $branch->id)->where('qty_available', '<=', 5)->count()
                ];
            })
            ->sortByDesc('total_revenue');
    }

    public function render()
    {
        return view('livewire.dashboard.owner-dashboard');
    }
}
