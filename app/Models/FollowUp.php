<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'recurrence_end_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    const TYPE_CALL = 'call';

    const TYPE_EMAIL = 'email';

    const TYPE_CHAT = 'chat';

    const TYPE_MEET = 'meet';

    const STATUS_PENDING = 'pending';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_RESCHEDULED = 'rescheduled';

    const RECURRENCE_DAILY = 'daily';

    const RECURRENCE_WEEKLY = 'weekly';

    const RECURRENCE_MONTHLY = 'monthly';

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function parentFollowUp()
    {
        return $this->belongsTo(FollowUp::class, 'parent_follow_up_id');
    }

    public function childFollowUps()
    {
        return $this->hasMany(FollowUp::class, 'parent_follow_up_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())
            ->where('status', self::STATUS_PENDING);
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
            ->where('status', self::STATUS_PENDING);
    }

    public function markAsCompleted(?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);

        if ($this->is_recurring) {
            $this->createNextRecurrence();
        }
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason ? $this->notes."\n\nCancelled: ".$reason : $this->notes,
        ]);
    }

    public function reschedule(\DateTime $newDateTime, ?string $reason = null): void
    {
        $oldScheduledAt = $this->scheduled_at;

        $this->update([
            'scheduled_at' => $newDateTime,
            'status' => self::STATUS_RESCHEDULED,
            'notes' => $reason
                ? $this->notes."\n\nRescheduled from {$oldScheduledAt->format('Y-m-d H:i')} to {$newDateTime->format('Y-m-d H:i')}. Reason: {$reason}"
                : $this->notes,
        ]);

        $this->update(['status' => self::STATUS_PENDING]);
    }

    protected function createNextRecurrence(): void
    {
        if (! $this->is_recurring || ! $this->recurrence_type) {
            return;
        }

        if ($this->recurrence_end_date && now()->greaterThan($this->recurrence_end_date)) {
            return;
        }

        $nextScheduledAt = match ($this->recurrence_type) {
            self::RECURRENCE_DAILY => $this->scheduled_at->copy()->addDays($this->recurrence_interval ?? 1),
            self::RECURRENCE_WEEKLY => $this->scheduled_at->copy()->addWeeks($this->recurrence_interval ?? 1),
            self::RECURRENCE_MONTHLY => $this->scheduled_at->copy()->addMonths($this->recurrence_interval ?? 1),
            default => null,
        };

        if (! $nextScheduledAt) {
            return;
        }

        if ($this->recurrence_end_date && $nextScheduledAt->greaterThan($this->recurrence_end_date)) {
            return;
        }

        static::create([
            'tenant_id' => $this->tenant_id,
            'lead_id' => $this->lead_id,
            'customer_id' => $this->customer_id,
            'type' => $this->type,
            'scheduled_at' => $nextScheduledAt,
            'notes' => $this->notes,
            'status' => self::STATUS_PENDING,
            'performed_by' => $this->performed_by,
            'is_recurring' => true,
            'recurrence_type' => $this->recurrence_type,
            'recurrence_interval' => $this->recurrence_interval,
            'recurrence_end_date' => $this->recurrence_end_date,
            'reminder_minutes_before' => $this->reminder_minutes_before,
            'parent_follow_up_id' => $this->parent_follow_up_id ?? $this->id,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->scheduled_at->isPast();
    }

    public function needsReminder(): bool
    {
        if (! $this->reminder_minutes_before || $this->reminder_sent_at) {
            return false;
        }

        $reminderTime = $this->scheduled_at->copy()->subMinutes($this->reminder_minutes_before);

        return now()->greaterThanOrEqualTo($reminderTime) && now()->lessThan($this->scheduled_at);
    }
}
