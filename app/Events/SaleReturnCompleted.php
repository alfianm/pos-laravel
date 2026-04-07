<?php

namespace App\Events;

use App\Models\SaleReturn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleReturnCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SaleReturn $return
    ) {
    }
}
