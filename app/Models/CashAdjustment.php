<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdjustment extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeCashIn($query)
    {
        return $query->where('type', 'cash_in');
    }

    public function scopeCashOut($query)
    {
        return $query->where('type', 'cash_out');
    }
}
