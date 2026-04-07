<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\MarketplaceSyncLog;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MarketplaceSyncReport extends Component
{
    use WithPagination;

    public $date_from;

    public $date_to;

    public $selected_branch = '';

    public $selected_marketplace = '';

    public $selected_status = '';

    public $selected_sync_type = '';

    public $branches;

    protected $queryString = ['date_from', 'date_to', 'selected_branch', 'selected_marketplace', 'selected_status', 'selected_sync_type'];

    public function mount()
    {
        $this->date_from = now()->subDays(7)->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
    }

    public function getMarketplaceOptionsProperty()
    {
        return [
            'tokopedia' => 'Tokopedia',
            'shopee' => 'Shopee',
            'lazada' => 'Lazada',
            'bukalapak' => 'Bukalapak',
            'blibli' => 'Blibli',
        ];
    }

    public function getStatusOptionsProperty()
    {
        return [
            'success' => 'Success',
            'failed' => 'Failed',
            'pending' => 'Pending',
        ];
    }

    public function getSyncTypeOptionsProperty()
    {
        return [
            'order_import' => 'Order Import',
            'stock_sync' => 'Stock Sync',
            'product_sync' => 'Product Sync',
            'price_sync' => 'Price Sync',
            'disconnect' => 'Disconnect',
            'reconnect' => 'Reconnect',
            'order' => 'Order Sync',
            'stock' => 'Stock Update',
            'product' => 'Product Sync',
            'price' => 'Price Update',
        ];
    }

    public function getSyncTypeLabel(string $syncType): string
    {
        return $this->syncTypeOptions[$syncType] ?? ucfirst(str_replace('_', ' ', $syncType));
    }

    public function getStatusSummaryProperty()
    {
        return MarketplaceSyncLog::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_marketplace, fn ($q) => $q->where('marketplace', $this->selected_marketplace))
            ->when($this->selected_status, fn ($q) => $q->where('status', $this->selected_status))
            ->when($this->selected_sync_type, fn ($q) => $q->where('sync_type', $this->selected_sync_type))
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    public function getMarketplaceSummaryProperty()
    {
        return MarketplaceSyncLog::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_marketplace, fn ($q) => $q->where('marketplace', $this->selected_marketplace))
            ->when($this->selected_status, fn ($q) => $q->where('status', $this->selected_status))
            ->when($this->selected_sync_type, fn ($q) => $q->where('sync_type', $this->selected_sync_type))
            ->select('marketplace', DB::raw('COUNT(*) as total'), DB::raw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count"), DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count"))
            ->groupBy('marketplace')
            ->get()
            ->map(function ($item) {
                $item->success_rate = $item->total > 0 ? round(($item->success_count / $item->total) * 100, 1) : 0;

                return $item;
            })
            ->sortByDesc('total');
    }

    public function getSyncTypeSummaryProperty()
    {
        return MarketplaceSyncLog::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_marketplace, fn ($q) => $q->where('marketplace', $this->selected_marketplace))
            ->when($this->selected_status, fn ($q) => $q->where('status', $this->selected_status))
            ->when($this->selected_sync_type, fn ($q) => $q->where('sync_type', $this->selected_sync_type))
            ->select('sync_type', DB::raw('COUNT(*) as total'), DB::raw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count"))
            ->groupBy('sync_type')
            ->get()
            ->map(function ($item) {
                $item->success_rate = $item->total > 0 ? round(($item->success_count / $item->total) * 100, 1) : 0;

                return $item;
            });
    }

    public function getGrandTotalsProperty()
    {
        return MarketplaceSyncLog::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_marketplace, fn ($q) => $q->where('marketplace', $this->selected_marketplace))
            ->when($this->selected_status, fn ($q) => $q->where('status', $this->selected_status))
            ->when($this->selected_sync_type, fn ($q) => $q->where('sync_type', $this->selected_sync_type))
            ->select(DB::raw('COUNT(*) as total_syncs'), DB::raw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count"), DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count"), DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count"))
            ->first();
    }

    public function getRecentLogsProperty()
    {
        return MarketplaceSyncLog::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['shop', 'branch'])
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_marketplace, fn ($q) => $q->where('marketplace', $this->selected_marketplace))
            ->when($this->selected_status, fn ($q) => $q->where('status', $this->selected_status))
            ->when($this->selected_sync_type, fn ($q) => $q->where('sync_type', $this->selected_sync_type))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function exportCsv()
    {
        $filename = 'marketplace_sync_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Marketplace', 'Shop', 'Branch', 'Type', 'Status', 'Message']);

            foreach ($this->recentLogs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    ucfirst($log->marketplace),
                    $log->shop->name ?? '-',
                    $log->branch->name ?? '-',
                    $this->getSyncTypeLabel($log->sync_type),
                    ucfirst($log->status),
                    $log->error_message ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetFilters()
    {
        $this->date_from = now()->subDays(7)->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->selected_branch = '';
        $this->selected_marketplace = '';
        $this->selected_status = '';
        $this->selected_sync_type = '';
    }

    public function getMarketplaceColor($marketplace)
    {
        return match ($marketplace) {
            'tokopedia' => 'bg-emerald-100 text-emerald-700',
            'shopee' => 'bg-orange-100 text-orange-700',
            'lazada' => 'bg-blue-100 text-blue-700',
            'bukalapak' => 'bg-red-100 text-red-700',
            'blibli' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'success' => 'bg-emerald-100 text-emerald-700',
            'failed' => 'bg-rose-100 text-rose-700',
            'pending' => 'bg-amber-100 text-amber-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.reports.marketplace-sync-report', [
            'marketplaceOptions' => $this->marketplaceOptions,
            'statusOptions' => $this->statusOptions,
            'syncTypeOptions' => $this->syncTypeOptions,
            'grandTotals' => $this->grandTotals,
            'marketplaceSummary' => $this->marketplaceSummary,
            'syncTypeSummary' => $this->syncTypeSummary,
            'recentLogs' => $this->recentLogs,
        ]);
    }
}
