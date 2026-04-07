<?php

namespace App\Jobs;

use App\Models\ImportBatch;
use App\Services\ProductImportService;
use App\Services\CustomerImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(public ImportBatch $batch)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = match ($this->batch->import_type) {
            'products' => app(ProductImportService::class),
            'customers' => app(CustomerImportService::class),
            // 'suppliers' => app(SupplierImportService::class),
            default => throw new \Exception("Unsupported import type: " . $this->batch->import_type),
        };

        $service->import($this->batch);
    }
}
