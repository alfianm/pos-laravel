<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Auditable, HasFactory, HasUuids, Notifiable, SoftDeletes, TenantAware;

    protected $fillable = [
        'tenant_id',
        'customer_group_id',
        'code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'total_spent',
        'last_purchase_date',
        'notes',
        'status',
        'birthday',
        'anniversary',
        'recency_score',
        'frequency_score',
        'monetary_score',
        'rfm_segment',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'last_purchase_date' => 'date',
        'birthday' => 'date',
        'anniversary' => 'date',
        'recency_score' => 'integer',
        'frequency_score' => 'integer',
        'monetary_score' => 'integer',
    ];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function loyaltyAccount()
    {
        return $this->hasOne(LoyaltyAccount::class);
    }

    public function getPointsBalanceAttribute()
    {
        return (float) ($this->loyaltyAccount->points_balance ?? 0);
    }
}
