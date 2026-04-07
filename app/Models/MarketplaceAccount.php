<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MarketplaceAccount extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'api_secret',
        'access_token',
        'refresh_token',
    ];

    public function shops()
    {
        return $this->hasMany(MarketplaceShop::class, 'marketplace_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForPlatform($query, $platform)
    {
        return $query->where('marketplace', $platform);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isTokenExpired(): bool
    {
        if (! $this->expires_at) {
            return true;
        }

        return $this->expires_at->isPast();
    }

    public function getApiKeyAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setApiKeyAttribute($value): void
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiSecretAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setApiSecretAttribute($value): void
    {
        $this->attributes['api_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPlatformLabelAttribute(): string
    {
        return match ($this->marketplace) {
            'tokopedia' => 'Tokopedia',
            'shopee' => 'Shopee',
            'lazada' => 'Lazada',
            'bukalapak' => 'Bukalapak',
            'blibli' => 'Blibli',
            default => ucfirst($this->marketplace),
        };
    }

    public function getPlatformLogoAttribute(): string
    {
        return match ($this->marketplace) {
            'shopee' => asset('assets/marketplace/shopee.png'),
            'tokopedia' => asset('assets/marketplace/tokopedia.png'),
            'lazada' => asset('assets/marketplace/lazada.png'),
            'bukalapak' => asset('assets/marketplace/bukalapak.png'),
            'blibli' => asset('assets/marketplace/blibli.png'),
            default => 'https://ui-avatars.com/api/?name='.urlencode($this->marketplace).'&background=6366f1&color=fff',
        };
    }

    public function getPlatformColorAttribute(): string
    {
        return match ($this->marketplace) {
            'shopee' => 'orange',
            'tokopedia' => 'emerald',
            'lazada' => 'blue',
            'bukalapak' => 'rose',
            'blibli' => 'indigo',
            default => 'gray',
        };
    }
}
