<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionPlanFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'billing_cycle',
        'price_monthly',
        'price_yearly',
        'features',
        'is_active',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class, 'subscription_plan_id');
    }
}
