<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyAccount extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'points_balance' => 'decimal:4',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }
}
