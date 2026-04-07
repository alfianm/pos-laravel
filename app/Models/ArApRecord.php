<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArApRecord extends Model
{
    use HasFactory, TenantAware, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'type',
        'entity_id',
        'entity_type',
        'transaction_id',
        'transaction_type',
        'reference_number',
        'transaction_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'days_overdue',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    /**
     * Get the entity (customer or supplier) associated with this record.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the transaction (sale or purchase order) associated with this record.
     */
    public function transaction(): MorphTo
    {
        return $this->morphTo();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for accounts receivable.
     */
    public function scopeReceivable($query)
    {
        return $query->where('type', 'ar');
    }

    /**
     * Scope for accounts payable.
     */
    public function scopePayable($query)
    {
        return $query->where('type', 'ap');
    }

    /**
     * Scope for outstanding records.
     */
    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', ['outstanding', 'partial']);
    }

    /**
     * Scope for overdue records.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope by due date range.
     */
    public function scopeDueBetween($query, $start, $end)
    {
        return $query->whereBetween('due_date', [$start, $end]);
    }

    /**
     * Get aging bucket (current, 1-30, 31-60, 61-90, 90+).
     */
    public function getAgingBucketAttribute(): string
    {
        $days = $this->days_overdue;

        return match (true) {
            $days <= 0 => 'current',
            $days <= 30 => '1-30',
            $days <= 60 => '31-60',
            $days <= 90 => '61-90',
            default => '90+',
        };
    }

    /**
     * Update status based on payment and due date.
     */
    public function updateStatus(): void
    {
        $today = now()->startOfDay();
        $dueDate = $this->due_date->startOfDay();

        // Calculate days overdue
        if ($today > $dueDate && $this->balance_amount > 0) {
            $this->days_overdue = $today->diffInDays($dueDate);
        } else {
            $this->days_overdue = 0;
        }

        // Update status based on payment
        if ($this->balance_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($today > $dueDate) {
            $this->status = 'overdue';
        } else {
            $this->status = 'outstanding';
        }

        $this->save();
    }

    /**
     * Record a payment against this AR/AP record.
     */
    public function recordPayment(float $amount, string $notes = null): void
    {
        $this->paid_amount += $amount;
        $this->balance_amount = $this->total_amount - $this->paid_amount;

        if ($this->balance_amount < 0) {
            $this->balance_amount = 0;
            $this->paid_amount = $this->total_amount;
        }

        if ($notes) {
            $this->notes = $this->notes ? $this->notes . "\n" . $notes : $notes;
        }

        $this->updateStatus();
    }
}