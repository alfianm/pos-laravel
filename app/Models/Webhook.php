<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'url',
        'secret',
        'is_active',
        'monitored_events',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'monitored_events' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    /**
     * Constants for standardized webhook events
     */
    public const EVENT_SALE_CREATED = 'sale.created';
    public const EVENT_SALE_COMPLETED = 'sale.completed';
    public const EVENT_SALE_VOIDED = 'sale.voided';
    public const EVENT_PRODUCT_CREATED = 'product.created';
    public const EVENT_PRODUCT_UPDATED = 'product.updated';
    public const EVENT_PRODUCT_DELETED = 'product.deleted';
    public const EVENT_INVENTORY_RESTOCK = 'inventory.restock';
    public const EVENT_INVENTORY_LOW = 'inventory.low_stock';
    public const EVENT_TENANT_CREATED = 'tenant.created';
    public const EVENT_SUBSCRIPTION_UPDATED = 'subscription.updated';
    public const EVENT_INVOICE_GENERATED = 'invoice.generated';
    public const EVENT_QUOTA_REACHED = 'quota.threshold_reached';

    public static function getAllEvents(): array
    {
        return [
            self::EVENT_SALE_CREATED,
            self::EVENT_SALE_COMPLETED,
            self::EVENT_SALE_VOIDED,
            self::EVENT_PRODUCT_CREATED,
            self::EVENT_PRODUCT_UPDATED,
            self::EVENT_PRODUCT_DELETED,
            self::EVENT_INVENTORY_RESTOCK,
            self::EVENT_INVENTORY_LOW,
            self::EVENT_TENANT_CREATED,
            self::EVENT_SUBSCRIPTION_UPDATED,
            self::EVENT_INVOICE_GENERATED,
            self::EVENT_QUOTA_REACHED,
        ];
    }
}
