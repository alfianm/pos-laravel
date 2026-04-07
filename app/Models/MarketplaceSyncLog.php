<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceSyncLog extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(MarketplaceShop::class, 'marketplace_shop_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeForMarketplace($query, $marketplace)
    {
        return $query->where('marketplace', $marketplace);
    }

    public function scopeForType($query, $type)
    {
        return $query->where('sync_type', $type);
    }

    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $limit = 100)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
