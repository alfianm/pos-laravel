<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InventoryMovementReport extends Component
{
    use WithPagination;

    public $date_from;

    public $date_to;

    public $selected_branch = '';

    public $selected_type = '';

    public $selected_product = '';

    public $branches;

    public $products;

    protected $queryString = ['date_from', 'date_to', 'selected_branch', 'selected_type'];

    public function mount()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $this->products = Product::where('tenant_id', auth()->user()->tenant_id)->limit(100)->get();
    }

    public function getMovementSummaryProperty()
    {
        return StockMovement::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_type, fn ($q) => $q->where('movement_type', $this->selected_type))
            ->when($this->selected_product, fn ($q) => $q->where('product_id', $this->selected_product))
            ->select('movement_type', DB::raw('COUNT(*) as total_movements'), DB::raw('SUM(ABS(qty)) as total_qty'))
            ->groupBy('movement_type')
            ->get()
            ->keyBy('movement_type');
    }

    public function getMovementsProperty()
    {
        return StockMovement::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['product', 'variant', 'branch', 'performedBy'])
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_type, fn ($q) => $q->where('movement_type', $this->selected_type))
            ->when($this->selected_product, fn ($q) => $q->where('product_id', $this->selected_product))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function getMovementTypesProperty()
    {
        return [
            'opening' => 'Stok Awal',
            'purchase' => 'Pembelian',
            'sale' => 'Penjualan',
            'adjustment' => 'Penyesuaian',
            'transfer_in' => 'Transfer Masuk',
            'transfer_out' => 'Transfer Keluar',
        ];
    }

    public function getMovementTypeLabel($type)
    {
        return $this->movementTypes[$type] ?? $type;
    }

    public function exportCsv()
    {
        $filename = 'inventory_movement_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $movements = StockMovement::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['product', 'variant', 'branch', 'performedBy'])
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_type, fn ($q) => $q->where('movement_type', $this->selected_type))
            ->orderBy('created_at', 'desc')
            ->get();

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Cabang', ' Produk', 'Varian', 'Tipe', 'Jumlah', 'Keterangan', 'User']);

            foreach ($movements as $m) {
                fputcsv($file, [
                    $m->created_at->format('Y-m-d H:i'),
                    $m->branch->name ?? '-',
                    $m->product->name ?? '-',
                    $m->variant->name ?? '-',
                    $this->getMovementTypeLabel($m->movement_type),
                    $m->qty > 0 ? '+'.$m->qty : $m->qty,
                    $m->reference_no ?? '-',
                    $m->performedBy->name ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetFilters()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->selected_branch = '';
        $this->selected_type = '';
        $this->selected_product = '';
    }

    public function render()
    {
        return view('livewire.reports.inventory-movement-report');
    }
}
