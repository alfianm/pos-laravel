<?php

namespace App\Services;

use App\Jobs\DispatchWebhookJob;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookService
{
    /**
     * Dispatch a webhook event for a specific tenant.
     * 
     * @param string $tenantId
     * @param string $eventType
     * @param array $payload
     * @return void
     */
    public function dispatch(string $tenantId, string $eventType, array $payload): void
    {
        // 1. Find all active webhooks for this tenant and event type
        $webhooks = Webhook::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($query) use ($eventType) {
                $query->whereJsonContains('monitored_events', $eventType)
                    ->orWhereNull('monitored_events'); // Null means all events
            })
            ->get();

        foreach ($webhooks as $webhook) {
            // 2. Create a delivery record marked as pending
            $delivery = WebhookDelivery::create([
                'id' => Str::uuid(),
                'webhook_id' => $webhook->id,
                'event_type' => $eventType,
                'payload' => $payload,
                'status' => 'pending',
                'attempt' => 1,
            ]);

            // 3. Dispatch Background Job
            DispatchWebhookJob::dispatch($delivery);
        }
    }

    /**
     * Generate signature for webhook security.
     * Calculated as HMAC-SHA256 of the payload string with the secret.
     */
    public static function generateSignature(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }
}
