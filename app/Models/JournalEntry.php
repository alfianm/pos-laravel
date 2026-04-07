<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $table = 'journal_entries';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'journal_number',
        'reference_number',
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
        'notes',
        'total_debit',
        'total_credit',
        'is_balanced',
        'is_posted',
        'status',
        'posted_at',
        'posted_by',
        'reversed_entry_id',
        'attachment',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'is_balanced' => 'boolean',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedEntry()
    {
        return $this->belongsTo(self::class, 'reversed_entry_id');
    }

    public function reversingEntry()
    {
        return $this->hasOne(self::class, 'reversed_entry_id');
    }

    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeUnposted($query)
    {
        return $query->where('is_posted', false);
    }

    public function scopeBalanced($query)
    {
        return $query->where('is_balanced', true);
    }

    public function scopeUnbalanced($query)
    {
        return $query->where('is_balanced', false);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByReference($query, string $type, string $id)
    {
        return $query->where('reference_type', $type)->where('reference_id', $id);
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('entry_date', $date);
    }

    public function isPosted(): bool
    {
        return $this->is_posted;
    }

    public function isBalanced(): bool
    {
        return $this->is_balanced;
    }

    public function canBeEdited(): bool
    {
        return !$this->is_posted;
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_posted;
    }

    public function canBePosted(): bool
    {
        return $this->is_balanced && !$this->is_posted;
    }

    public function canBeReversed(): bool
    {
        return $this->is_posted && is_null($this->reversingEntry) && $this->status !== 'reversed';
    }

    public function getBalance(): float
    {
        return abs((float) $this->total_debit - (float) $this->total_credit);
    }

    public function calculateTotals(): void
    {
        $this->total_debit = $this->lines->sum('debit');
        $this->total_credit = $this->lines->sum('credit');
        $this->is_balanced = abs($this->total_debit - $this->total_credit) < 0.01;
        $this->save();
    }
}
