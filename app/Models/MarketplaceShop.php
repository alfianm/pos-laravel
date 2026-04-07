<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceShop extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(MarketplaceAccount::class, 'marketplace_account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'marketplace_shop_id');
    }

    public function productMaps()
    {
        return $this->hasMany(MarketplaceProductMap::class, 'marketplace_shop_id');
    }

    public function scopeForMarketplace($query, $marketplace)
    {
        return $query->where('marketplace', $marketplace);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
