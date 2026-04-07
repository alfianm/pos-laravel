<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\InvoiceStatus;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, TenantAware;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'reference',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'status' => InvoiceStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', InvoiceStatus::DRAFT);
    }

    public function scopeSent($query)
    {
        return $query->where('status', InvoiceStatus::SENT);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopePartial($query)
    {
        return $query->where('status', InvoiceStatus::PARTIAL);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::OVERDUE)
            ->orWhere(function ($q) {
                $q->whereIn('status', [InvoiceStatus::SENT, InvoiceStatus::PARTIAL])
                    ->where('due_date', '<', now()->toDateString());
            });
    }

    public function isDraft(): bool
    {
        return $this->status === InvoiceStatus::DRAFT;
    }

    public function isSent(): bool
    {
        return $this->status === InvoiceStatus::SENT;
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }

    public function isPartial(): bool
    {
        return $this->status === InvoiceStatus::PARTIAL;
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::OVERDUE ||
            (in_array($this->status, [InvoiceStatus::SENT, InvoiceStatus::PARTIAL]) &&
                $this->due_date < now()->toDateString());
    }

    public function isCancelled(): bool
    {
        return $this->status === InvoiceStatus::CANCELLED;
    }

    public function markAsSent(): void
    {
        if ($this->isDraft()) {
            $this->update(['status' => InvoiceStatus::SENT]);
        }
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => InvoiceStatus::PAID,
            'paid_amount' => $this->total_amount,
            'balance_due' => 0,
        ]);
    }

    public function markAsPartial(float $paidAmount): void
    {
        $this->update([
            'status' => InvoiceStatus::PARTIAL,
            'paid_amount' => $paidAmount,
            'balance_due' => $this->total_amount - $paidAmount,
        ]);
    }

    public function markAsOverdue(): void
    {
        if (in_array($this->status, [InvoiceStatus::SENT, InvoiceStatus::PARTIAL])) {
            $this->update(['status' => InvoiceStatus::OVERDUE]);
        }
    }

    public function cancel(): void
    {
        if (!$this->isPaid()) {
            $this->update(['status' => InvoiceStatus::CANCELLED]);
        }
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('line_total');
        $discountAmount = $this->items->sum('discount_amount');
        $taxAmount = $this->items->sum('tax_amount');
        $totalAmount = $subtotal - $discountAmount + $taxAmount;
        $balanceDue = $totalAmount - $this->paid_amount;

        $status = $this->status;
        if ($this->paid_amount >= $totalAmount) {
            $status = InvoiceStatus::PAID;
        } elseif ($this->paid_amount > 0) {
            $status = InvoiceStatus::PARTIAL;
        } elseif ($this->isDraft()) {
            $status = InvoiceStatus::DRAFT;
        }

        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'balance_due' => $balanceDue,
            'status' => $status,
        ]);
    }
}
