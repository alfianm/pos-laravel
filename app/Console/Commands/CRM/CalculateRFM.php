<?php

namespace App\Console\Commands\CRM;

use App\Models\Tenant;
use App\Services\CustomerSegmentationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('crm:calculate-rfm')]
#[Description('Hitung skor RFM dan segmentasi customer untuk semua tenant.')]
class CalculateRFM extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CustomerSegmentationService $segmentationService)
    {
        $this->info('Mulai menghitung segmentasi RFM...');

        Tenant::chunk(50, function ($tenants) use ($segmentationService) {
            foreach ($tenants as $tenant) {
                $this->line("Memproses Tenant: {$tenant->name}");
                $segmentationService->updateAllRfmSegments($tenant->id);
            }
        });

        $this->info('Selesai menghitung segmentasi RFM.');
    }
}
