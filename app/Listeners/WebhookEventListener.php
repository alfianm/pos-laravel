<?php

namespace App\Listeners;

use App\Events\InvoiceGenerated;
use App\Events\LowStockAlert;
use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductUpdated;
use App\Events\QuotaThresholdReached;
use App\Events\SaleCreated;
use App\Events\SubscriptionUpdated;
use App\Events\TenantCreated;
use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

class WebhookEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected WebhookService $webhookService
    ) {}

    /**
     * Handle sale creation.
     */
    public function handleSaleCreated(SaleCreated $event): void
    {
        $sale = $event->sale;
        $this->webhookService->dispatch($sale->tenant_id, Webhook::EVENT_SALE_CREATED, [
            'sale_no' => $sale->sale_no,
            'grand_total' => $sale->grand_total,
            'customer_id' => $sale->customer_id,
            'status' => $sale->status,
        ]);
    }

    /**
     * Handle product events.
     */
    public function handleProductCreated(ProductCreated $event): void
    {
        $product = $event->product;
        $this->webhookService->dispatch($product->tenant_id, Webhook::EVENT_PRODUCT_CREATED, [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->selling_price,
        ]);
    }

    public function handleProductUpdated(ProductUpdated $event): void
    {
        $product = $event->product;
        $this->webhookService->dispatch($product->tenant_id, Webhook::EVENT_PRODUCT_UPDATED, [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->selling_price,
        ]);
    }

    public function handleProductDeleted(ProductDeleted $event): void
    {
        $this->webhookService->dispatch($event->tenantId, Webhook::EVENT_PRODUCT_DELETED, [
            'id' => $event->productId,
            'sku' => $event->sku,
        ]);
    }

    /**
     * Handle inventory events.
     */
    public function handleLowStock(LowStockAlert $event): void
    {
        $product = $event->product;
        $this->webhookService->dispatch($product->tenant_id, Webhook::EVENT_INVENTORY_LOW, [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'current_stock' => $event->currentStock,
        ]);
    }

    /**
     * Handle tenant & billing events.
     */
    public function handleTenantCreated(TenantCreated $event): void
    {
        $this->webhookService->dispatch($event->tenant->id, Webhook::EVENT_TENANT_CREATED, [
            'id' => $event->tenant->id,
            'name' => $event->tenant->name,
            'code' => $event->tenant->code,
        ]);
    }

    public function handleSubscriptionUpdated(SubscriptionUpdated $event): void
    {
        $this->webhookService->dispatch($event->tenant->id, Webhook::EVENT_SUBSCRIPTION_UPDATED, [
            'old_plan' => $event->oldPlan,
            'new_plan' => $event->newPlan,
        ]);
    }

    public function handleInvoiceGenerated(InvoiceGenerated $event): void
    {
        $this->webhookService->dispatch($event->tenant->id, Webhook::EVENT_INVOICE_GENERATED, $event->invoiceData);
    }

    public function handleQuotaReached(QuotaThresholdReached $event): void
    {
        $this->webhookService->dispatch($event->tenant->id, Webhook::EVENT_QUOTA_REACHED, [
            'resource' => $event->resourceType,
            'usage' => $event->currentUsage,
            'limit' => $event->limit,
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            SaleCreated::class => 'handleSaleCreated',
            ProductCreated::class => 'handleProductCreated',
            ProductUpdated::class => 'handleProductUpdated',
            ProductDeleted::class => 'handleProductDeleted',
            LowStockAlert::class => 'handleLowStock',
            TenantCreated::class => 'handleTenantCreated',
            SubscriptionUpdated::class => 'handleSubscriptionUpdated',
            InvoiceGenerated::class => 'handleInvoiceGenerated',
            QuotaThresholdReached::class => 'handleQuotaReached',
        ];
    }
}
