<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTimeline extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'customer_id',
        'lead_id',
        'sale_id',
        'type',
        'event_type',
        'title',
        'description',
        'metadata',
        'meta',
        'occurred_at',
        'reference_id',
        'reference_type',
    ];

    protected $casts = [
        'metadata' => 'array',
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the timeline.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the sale associated with the timeline.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the branch that owns the timeline.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the tenant that owns the timeline.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope a query to only include timelines for a specific customer.
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include timelines of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to order by occurred_at descending.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('occurred_at', 'desc');
    }
}
