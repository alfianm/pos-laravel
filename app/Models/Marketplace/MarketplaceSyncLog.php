<?php

namespace App\Models\Marketplace;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketplaceSyncLog extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'marketplace',
        'type',
        'status',
        'message',
    ];
}
