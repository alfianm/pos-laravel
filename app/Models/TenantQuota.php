<?php

namespace App\Models;

use App\Traits\TenantAware;
use Database\Factories\TenantQuotaFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantQuota extends Model
{
    /** @use HasFactory<TenantQuotaFactory> */
    use HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $fillable = [
        'tenant_id',
        'quota_type',
        'limit_value',
        'used_value',
        'alert_threshold',
        'last_calculated_at',
        'metadata',
    ];

    protected $casts = [
        'limit_value' => 'integer',
        'used_value' => 'integer',
        'alert_threshold' => 'integer',
        'last_calculated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('quota_type', $type);
    }

    public function isUnlimited(): bool
    {
        return $this->limit_value === -1;
    }

    public function isExceeded(): bool
    {
        if ($this->isUnlimited()) {
            return false;
        }

        return $this->used_value >= $this->limit_value;
    }

    public function isApproachingLimit(): bool
    {
        if ($this->isUnlimited()) {
            return false;
        }

        if ($this->limit_value === 0) {
            return false;
        }

        $percentage = ($this->used_value / $this->limit_value) * 100;

        return $percentage >= $this->alert_threshold;
    }

    public function remaining(): int
    {
        if ($this->isUnlimited()) {
            return PHP_INT_MAX;
        }

        return max(0, $this->limit_value - $this->used_value);
    }

    public function usagePercentage(): float
    {
        if ($this->isUnlimited()) {
            return 0;
        }

        if ($this->limit_value === 0) {
            return 0;
        }

        return round(($this->used_value / $this->limit_value) * 100, 2);
    }

    public function incrementUsage(int $amount = 1): void
    {
        $this->increment('used_value', $amount);
        $this->update(['last_calculated_at' => now()]);

        if ($this->isApproachingLimit()) {
            event(new \App\Events\QuotaThresholdReached(
                $this->tenant,
                $this->quota_type,
                $this->used_value,
                $this->limit_value
            ));
        }
    }

    public function decrementUsage(int $amount = 1): void
    {
        $this->decrement('used_value', $amount);
        $this->used_value = max(0, $this->used_value - $amount);
        $this->update(['last_calculated_at' => now()]);
    }

    public function recalculate(): void
    {
        $this->update(['last_calculated_at' => now()]);
    }
}
