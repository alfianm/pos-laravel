<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SalesByCashierReport extends Component
{
    use WithPagination;

    public $date_from;

    public $date_to;

    public $selected_cashier = '';

    public $cashiers;

    protected $queryString = ['date_from', 'date_to', 'selected_cashier'];

    public function mount()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->cashiers = User::where('tenant_id', auth()->user()->tenant_id)->get();
    }

    public function getCashierSummaryProperty()
    {
        return Sale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'completed')
            ->when($this->date_from, fn ($q) => $q->whereDate('sale_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('sale_date', '<=', $this->date_to))
            ->when($this->selected_cashier, fn ($q) => $q->where('user_id', $this->selected_cashier))
            ->select('user_id', DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(grand_total) as total_sales'), DB::raw('SUM(discount_amount) as total_discount'))
            ->with('cashier')
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                $item->avg_transaction = $item->total_transactions > 0
                    ? $item->total_sales / $item->total_transactions
                    : 0;

                return $item;
            })
            ->sortByDesc('total_sales');
    }

    public function getGrandTotalsProperty()
    {
        return Sale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'completed')
            ->when($this->date_from, fn ($q) => $q->whereDate('sale_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('sale_date', '<=', $this->date_to))
            ->when($this->selected_cashier, fn ($q) => $q->where('user_id', $this->selected_cashier))
            ->select(DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(grand_total) as total_sales'), DB::raw('SUM(discount_amount) as total_discount'))
            ->first();
    }

    public function exportCsv()
    {
        $filename = 'sales_by_cashier_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kasir', 'Transaksi', 'Total Penjualan', 'Rata-rata Transaksi', 'Total Diskon']);

            foreach ($this->cashierSummary as $item) {
                fputcsv($file, [
                    $item->cashier->name ?? 'Unknown',
                    $item->total_transactions,
                    number_format($item->total_sales, 2),
                    number_format($item->avg_transaction, 2),
                    number_format($item->total_discount, 2),
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
        $this->selected_cashier = '';
    }

    public function render()
    {
        return view('livewire.reports.sales-by-cashier-report');
    }
}
