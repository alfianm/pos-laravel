<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\PurchasePayment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PurchaseSummaryReport extends Component
{
    use WithPagination;

    public $date_from;

    public $date_to;

    public $selected_branch = '';

    public $selected_supplier = '';

    public $selected_status = '';

    public $branches;

    public $suppliers;

    protected $queryString = ['date_from', 'date_to', 'selected_branch', 'selected_supplier', 'selected_status'];

    public function mount()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $this->suppliers = Supplier::where('tenant_id', auth()->user()->tenant_id)->get();
    }

    public function getSupplierSummaryProperty()
    {
        $paymentTotals = PurchasePayment::query()
            ->select('purchase_order_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('purchase_order_id');

        return PurchaseOrder::query()
            ->leftJoinSub($paymentTotals, 'payment_totals', function ($join) {
                $join->on('purchase_orders.id', '=', 'payment_totals.purchase_order_id');
            })
            ->where('purchase_orders.tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('purchase_orders.order_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('purchase_orders.order_date', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('purchase_orders.branch_id', $this->selected_branch))
            ->when($this->selected_supplier, fn ($q) => $q->where('purchase_orders.supplier_id', $this->selected_supplier))
            ->when($this->selected_status, fn ($q) => $q->where('purchase_orders.status', $this->selected_status))
            ->select('purchase_orders.supplier_id', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(purchase_orders.grand_total) as total_amount'), DB::raw('SUM(COALESCE(payment_totals.total_paid, 0)) as total_paid'))
            ->with('supplier')
            ->groupBy('purchase_orders.supplier_id')
            ->get()
            ->map(function ($item) {
                $item->total_amount = (float) ($item->total_amount ?? 0);
                $item->total_paid = (float) ($item->total_paid ?? 0);
                $item->total_due = max($item->total_amount - $item->total_paid, 0);
                $item->payment_status = $item->total_due > 0 ? ($item->total_paid > 0 ? 'partial' : 'unpaid') : 'paid';

                return $item;
            })
            ->sortByDesc('total_amount');
    }

    public function getGrandTotalsProperty()
    {
        $paymentTotals = PurchasePayment::query()
            ->select('purchase_order_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('purchase_order_id');

        $stats = PurchaseOrder::query()
            ->leftJoinSub($paymentTotals, 'payment_totals', function ($join) {
                $join->on('purchase_orders.id', '=', 'payment_totals.purchase_order_id');
            })
            ->where('purchase_orders.tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('purchase_orders.order_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('purchase_orders.order_date', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('purchase_orders.branch_id', $this->selected_branch))
            ->when($this->selected_supplier, fn ($q) => $q->where('purchase_orders.supplier_id', $this->selected_supplier))
            ->when($this->selected_status, fn ($q) => $q->where('purchase_orders.status', $this->selected_status))
            ->select(DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(purchase_orders.grand_total) as total_amount'), DB::raw('SUM(COALESCE(payment_totals.total_paid, 0)) as total_paid'))
            ->first();

        $stats->total_amount = (float) ($stats->total_amount ?? 0);
        $stats->total_paid = (float) ($stats->total_paid ?? 0);
        $stats->total_due = max($stats->total_amount - $stats->total_paid, 0);

        return $stats;
    }

    public function getStatusSummaryProperty()
    {
        return PurchaseOrder::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('order_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('order_date', '<=', $this->date_to))
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    public function getRecentOrdersProperty()
    {
        return PurchaseOrder::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['branch', 'supplier'])
            ->when($this->date_from, fn ($q) => $q->whereDate('order_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('order_date', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_supplier, fn ($q) => $q->where('supplier_id', $this->selected_supplier))
            ->when($this->selected_status, fn ($q) => $q->where('status', $this->selected_status))
            ->orderBy('order_date', 'desc')
            ->paginate(15);
    }

    public function exportCsv()
    {
        $filename = 'purchase_summary_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Supplier', 'Total Orders', 'Total Amount', 'Paid', 'Due', 'Status']);

            foreach ($this->supplierSummary as $item) {
                fputcsv($file, [
                    $item->supplier->name ?? '-',
                    $item->total_orders,
                    number_format($item->total_amount, 2),
                    number_format($item->total_paid, 2),
                    number_format($item->total_due, 2),
                    $item->payment_status,
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
        $this->selected_supplier = '';
        $this->selected_status = '';
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'partial' => 'Partial',
            'received' => 'Received',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => $status,
        };
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'submitted' => 'bg-blue-100 text-blue-700',
            'partial' => 'bg-amber-100 text-amber-700',
            'received' => 'bg-cyan-100 text-cyan-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-rose-100 text-rose-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.reports.purchase-summary-report');
    }
}
