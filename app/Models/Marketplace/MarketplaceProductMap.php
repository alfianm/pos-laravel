<?php

namespace App\Models\Marketplace;

use App\Traits\TenantAware;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketplaceProductMap extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'product_variant_id',
        'marketplace',
        'external_product_id',
        'external_sku',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
