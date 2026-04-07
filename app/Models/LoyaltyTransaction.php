<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'points' => 'decimal:4',
    ];

    public function loyaltyAccount()
    {
        return $this->belongsTo(LoyaltyAccount::class);
    }
}
