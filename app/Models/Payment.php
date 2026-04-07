<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\PaymentMethod;
use App\Constants\PaymentStatus;
use App\Traits\BelongsToBranch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use BelongsToBranch;
    use BelongsToTenant;
    use HasFactory;
    
    protected $table = 'invoice_payments';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'invoice_id',
        'payment_number',
        'payment_date',
        'amount',
        'method',
        'reference_number',
        'bank_name',
        'account_number',
        'account_name',
        'notes',
        'processed_by',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function isCompleted(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => PaymentStatus::COMPLETED]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => PaymentStatus::FAILED]);
    }
}
