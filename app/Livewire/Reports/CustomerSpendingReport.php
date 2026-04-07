<?php

namespace App\Livewire\Reports;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CustomerSpendingReport extends Component
{
    use WithPagination;

    public $date_from;

    public $date_to;

    public $search = '';

    public $selected_customer = '';

    protected $queryString = ['date_from', 'date_to', 'search', 'selected_customer'];

    public function mount()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
    }

    public function getTopCustomersProperty()
    {
        return Sale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->when($this->date_from, fn ($q) => $q->whereDate('sale_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('sale_date', '<=', $this->date_to))
            ->when($this->selected_customer, fn ($q) => $q->where('customer_id', $this->selected_customer))
            ->select('customer_id', DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(grand_total) as total_spent'), DB::raw('AVG(grand_total) as avg_transaction'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();
    }

    public function getCustomerListProperty()
    {
        return Customer::where('tenant_id', auth()->user()->tenant_id)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    public function getAllTransactionsProperty()
    {
        return Sale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'completed')
            ->when($this->date_from, fn ($q) => $q->whereDate('sale_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('sale_date', '<=', $this->date_to))
            ->when($this->selected_customer, fn ($q) => $q->where('customer_id', $this->selected_customer))
            ->select(DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(grand_total) as total_spent'))
            ->first();
    }

    public function getRecentPurchasesProperty()
    {
        return Sale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->when($this->date_from, fn ($q) => $q->whereDate('sale_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('sale_date', '<=', $this->date_to))
            ->when($this->selected_customer, fn ($q) => $q->where('customer_id', $this->selected_customer))
            ->with(['customer', 'branch'])
            ->orderBy('sale_date', 'desc')
            ->paginate(15);
    }

    public function exportCsv()
    {
        $filename = 'customer_spending_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $data = $this->topCustomers;

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Customer', 'Email', 'Telepon', 'Total Transaksi', 'Total Belanja', 'Rata-rata Transaksi']);

            foreach ($data as $item) {
                fputcsv($file, [
                    $item->customer->name ?? 'Walk-in',
                    $item->customer->email ?? '-',
                    $item->customer->phone ?? '-',
                    $item->total_transactions,
                    number_format($item->total_spent, 2),
                    number_format($item->avg_transaction, 2),
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
        $this->selected_customer = '';
        $this->search = '';
    }

    public function render()
    {
        return view('livewire.reports.customer-spending-report');
    }
}
