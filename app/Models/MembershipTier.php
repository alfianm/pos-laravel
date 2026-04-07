<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    public function loyaltyAccounts()
    {
        return $this->hasMany(LoyaltyAccount::class);
    }
}
