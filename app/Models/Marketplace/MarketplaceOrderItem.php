<?php

namespace App\Models\Marketplace;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketplaceOrderItem extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'marketplace_order_id',
        'external_item_id',
        'name',
        'sku',
        'qty',
        'price',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
