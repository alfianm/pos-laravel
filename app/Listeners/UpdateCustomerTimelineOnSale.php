<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Services\CustomerTimelineService;

class UpdateCustomerTimelineOnSale
{
    public function __construct(
        private CustomerTimelineService $timelineService
    ) {
    }

    public function handle(SaleCreated $event): void
    {
        $sale = $event->sale;

        if (!$sale->customer_id) {
            return;
        }

        $this->timelineService->recordSale($sale);
    }
}
