<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceSyncLog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SyncLogs extends Component
{
    use WithPagination;

    public $search = '';

    public $marketplace_filter = '';

    public $type_filter = '';

    public $status_filter = '';

    public $date_from = '';

    public $date_to = '';

    protected $queryString = ['search', 'marketplace_filter', 'type_filter', 'status_filter', 'date_from', 'date_to'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMarketplaceFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->marketplace_filter = '';
        $this->type_filter = '';
        $this->status_filter = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->resetPage();
    }

    public function getPlatformLabel($platform)
    {
        return match ($platform) {
            'shopee' => 'Shopee',
            'tokopedia' => 'Tokopedia',
            'lazada' => 'Lazada',
            'bukalapak' => 'Bukalapak',
            'blibli' => 'Blibli',
            default => ucfirst($platform),
        };
    }

    public function getPlatformColor($platform)
    {
        return match ($platform) {
            'shopee' => 'orange',
            'tokopedia' => 'emerald',
            'lazada' => 'blue',
            'bukalapak' => 'rose',
            'blibli' => 'indigo',
            default => 'gray',
        };
    }

    public function getTypeLabel($type)
    {
        return match ($type) {
            'order_import' => 'Import Order',
            'stock_sync' => 'Sinkron Stok',
            'price_sync' => 'Sinkron Harga',
            'product_sync' => 'Sinkron Produk',
            'order_status_update' => 'Update Status Order',
            default => ucfirst($type),
        };
    }

    public function render()
    {
        $logs = MarketplaceSyncLog::with(['shop'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('error_message', 'ilike', '%'.$this->search.'%')
                        ->orWhere('sync_type', 'ilike', '%'.$this->search.'%');
                });
            })
            ->when($this->marketplace_filter, function ($query) {
                $query->where('marketplace', $this->marketplace_filter);
            })
            ->when($this->type_filter, function ($query) {
                $query->where('sync_type', $this->type_filter);
            })
            ->when($this->status_filter, function ($query) {
                $query->where('status', $this->status_filter);
            })
            ->when($this->date_from, function ($query) {
                $query->whereDate('created_at', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($query) {
                $query->whereDate('created_at', '<=', $this->date_to);
            })
            ->latest()
            ->paginate(20);

        return view('livewire.marketplace.sync-logs', [
            'logs' => $logs,
        ]);
    }
}
