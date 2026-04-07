<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_non_cash_sales' => 'decimal:2',
        'total_cash_submitted' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function cashAdjustments()
    {
        return $this->hasMany(CashAdjustment::class);
    }

    public function getTotalCashInAttribute()
    {
        return $this->cashAdjustments()->cashIn()->sum('amount');
    }

    public function getTotalCashOutAttribute()
    {
        return $this->cashAdjustments()->cashOut()->sum('amount');
    }

    public function getExpectedCashAttribute()
    {
        return $this->opening_balance
            + $this->total_cash_sales
            + $this->total_cash_in
            - $this->total_cash_out;
    }

    public function getDifferenceAttribute()
    {
        return $this->total_cash_submitted - $this->expected_cash;
    }
}
