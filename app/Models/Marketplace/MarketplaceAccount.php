<?php

namespace App\Models\Marketplace;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketplaceAccount extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'marketplace',
        'name',
        'access_token',
        'refresh_token',
        'expires_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'json',
    ];

    public function shops()
    {
        return $this->hasMany(MarketplaceShop::class);
    }
}
