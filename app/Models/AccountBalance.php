<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'account_balances';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'account_id',
        'balance_date',
        'period_month',
        'opening_balance',
        'closing_balance',
        'debit_movement',
        'credit_movement',
    ];

    protected $casts = [
        'balance_date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'debit_movement' => 'decimal:2',
        'credit_movement' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeForFiscalYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeForPeriod($query, $period)
    {
        return $query->where('period_month', $period);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeCompanyWide($query)
    {
        return $query->whereNull('branch_id');
    }

    public function calculateClosingBalance(): void
    {
        $normalBalance = $this->account->normal_balance;

        // Normal balance 1 (Debit), -1 (Credit)
        if ($normalBalance == 1) {
            $this->closing_balance = $this->opening_balance + $this->debit_movement - $this->credit_movement;
        } else {
            $this->closing_balance = $this->opening_balance + $this->credit_movement - $this->debit_movement;
        }
    }

    public function getNetChange(): float
    {
        return $this->account->normal_balance == 1
            ? (float)($this->debit_movement - $this->credit_movement)
            : (float)($this->credit_movement - $this->debit_movement);
    }

    public function isDebitBalance(): bool
    {
        return $this->closing_balance > 0 && $this->account->normal_balance === 1;
    }

    public function isCreditBalance(): bool
    {
        return $this->closing_balance > 0 && $this->account->normal_balance === -1;
    }
}
