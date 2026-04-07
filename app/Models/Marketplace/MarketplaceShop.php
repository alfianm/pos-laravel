<?php

namespace App\Models\Marketplace;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketplaceShop extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'marketplace_account_id',
        'shop_id',
        'name',
    ];

    public function account()
    {
        return $this->belongsTo(MarketplaceAccount::class, 'marketplace_account_id');
    }
}
