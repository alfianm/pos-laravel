<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceProductMap extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'sync_price' => 'boolean',
        'sync_stock' => 'boolean',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'meta' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(MarketplaceShop::class, 'marketplace_shop_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function scopeForMarketplace($query, $marketplace)
    {
        return $query->where('marketplace', $marketplace);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSyncStockEnabled($query)
    {
        return $query->where('sync_stock', true);
    }

    public function scopeSyncPriceEnabled($query)
    {
        return $query->where('sync_price', true);
    }
}
