<?php

namespace App\Jobs;

use App\Models\MarketplaceSyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetryFailedMarketplaceSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $lookbackHours = 2,
        public int $maxRetriesPerLog = 1
    ) {}

    public function handle(): void
    {
        // Ambil log gagal yang belum pernah di-retry secara otomatis
        // Kita menggunakan metadata 'auto_retried' di payload untuk menghindari loop
        $failedLogs = MarketplaceSyncLog::query()
            ->failed()
            ->where('created_at', '>=', now()->subHours($this->lookbackHours))
            ->get()
            ->filter(fn (MarketplaceSyncLog $log) => !($log->payload['auto_retried'] ?? false));

        if ($failedLogs->isEmpty()) {
            return;
        }

        Log::info("Retrying {$failedLogs->count()} failed marketplace sync logs.");

        foreach ($failedLogs as $log) {
            $this->dispatchRetry($log);
            
            // Tandai agar tidak di-retry lagi oleh scheduler yang sama
            $payload = $log->payload ?? [];
            $payload['auto_retried'] = true;
            $payload['retried_at'] = now()->toDateTimeString();
            
            $log->update(['payload' => $payload]);
        }
    }

    protected function dispatchRetry(MarketplaceSyncLog $log): void
    {
        try {
            if (!$log->marketplace_shop_id) {
                return;
            }

            match ($log->sync_type) {
                'order_import' => ImportMarketplaceOrdersJob::dispatch($log->marketplace_shop_id),
                'stock_sync' => SyncMarketplaceStockJob::dispatch($log->marketplace_shop_id),
                default => Log::warning("Unknown sync_type '{$log->sync_type}' for retry in log: {$log->id}"),
            };
        } catch (\Exception $e) {
            Log::error("Failed to dispatch retry for log {$log->id}: " . $e->getMessage());
        }
    }
}
