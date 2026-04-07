<?php

namespace App\Models\Marketplace;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketplaceOrder extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'marketplace_shop_id',
        'external_order_id',
        'marketplace',
        'order_date',
        'status',
        'total_amount',
        'raw_data',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'raw_data' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(MarketplaceShop::class, 'marketplace_shop_id');
    }

    public function items()
    {
        return $this->hasMany(MarketplaceOrderItem::class);
    }
}
