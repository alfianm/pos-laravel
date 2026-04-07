<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $table = 'journal_entry_lines';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'journal_entry_id',
        'account_id',
        'description',
        'debit',
        'credit',
        'line_number',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'line_number' => 'integer',
    ];

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeDebit($query)
    {
        return $query->where('debit', '>', 0);
    }

    public function scopeCredit($query)
    {
        return $query->where('credit', '>', 0);
    }

    public function isDebit(): bool
    {
        return $this->debit > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit > 0;
    }

    public function getAmount(): float
    {
        return max($this->debit, $this->credit);
    }

    public function getType(): string
    {
        return $this->debit > 0 ? 'debit' : 'credit';
    }
}
