<?php

namespace App\Models;

use App\Traits\TenantAware;
use Database\Factories\TenantSubscriptionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantSubscription extends Model
{
    /** @use HasFactory<TenantSubscriptionFactory> */
    use HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'status',
        'billing_cycle',
        'amount',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'downgraded_at',
        'downgraded_to_plan_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'trial_ends_at' => 'date',
        'cancelled_at' => 'datetime',
        'downgraded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function downgradedPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'downgraded_to_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_subscription_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trial']);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->ends_at && $this->ends_at->isPast());
    }
}
