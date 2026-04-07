<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'tenant_id',
        'account_category_id',
        'account_code',
        'account_name',
        'type',
        'normal_balance',
        'level',
        'parent_id',
        'branch_id',
        'opening_balance',
        'current_balance',
        'description',
        'is_active',
        'is_cash_account',
        'is_bank_account',
        'bank_account_number',
        'bank_name',
        'sort_order',
    ];

    protected $casts = [
        'normal_balance' => 'integer',
        'level' => 'integer',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_cash_account' => 'boolean',
        'is_bank_account' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function accountBalances()
    {
        return $this->hasMany(AccountBalance::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCashAccounts($query)
    {
        return $query->where('is_cash_account', true);
    }

    public function scopeBankAccounts($query)
    {
        return $query->where('is_bank_account', true);
    }

    public function scopeMajorAccounts($query)
    {
        return $query->where('level', 1);
    }

    public function scopeSubAccounts($query)
    {
        return $query->where('level', 2);
    }

    public function scopeDetailAccounts($query)
    {
        return $query->where('level', 3);
    }

    public function scopeCompanyWide($query)
    {
        return $query->whereNull('branch_id');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where(function ($q) use ($branchId) {
            $q->whereNull('branch_id')
                ->orWhere('branch_id', $branchId);
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('account_code');
    }

    public function isDebitNormal(): bool
    {
        return $this->normal_balance === 1;
    }

    public function isCreditNormal(): bool
    {
        return $this->normal_balance === -1;
    }

    public function isMajorAccount(): bool
    {
        return $this->level === 1;
    }

    public function isSubAccount(): bool
    {
        return $this->level === 2;
    }

    public function isDetailAccount(): bool
    {
        return $this->level === 3;
    }

    public function getFullCode(): string
    {
        if ($this->parent) {
            return $this->parent->account_code . '.' . $this->account_code;
        }
        return $this->account_code;
    }

    public function updateBalance(float $debit, float $credit): void
    {
        $change = $this->isDebitNormal() ? $debit - $credit : $credit - $debit;
        $this->current_balance += $change;
        $this->save();
    }
}
