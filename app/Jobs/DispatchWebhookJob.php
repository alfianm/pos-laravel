<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Services\WebhookService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [60, 300, 1800, 3600]; // Exp backoff: 1m, 5m, 30m, 1h

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WebhookDelivery $delivery
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhook = $this->delivery->webhook;

        if (!$webhook || !$webhook->is_active) {
            $this->delivery->update(['status' => 'failed', 'response_body' => 'Webhook deleted or inactive.']);
            return;
        }

        $payload = json_encode([
            'id' => $this->delivery->id,
            'event' => $this->delivery->event_type,
            'data' => $this->delivery->payload,
            'timestamp' => now()->toIso8601String(),
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'RasaNusa-Webhook-Dispatcher/1.0',
        ];

        // Add Security Signature if secret is set
        if ($webhook->secret) {
            $headers['X-RasaNusa-Signature'] = WebhookService::generateSignature($payload, $webhook->secret);
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($webhook->url, json_decode($payload, true));

            $this->delivery->update([
                'response_code' => $response->status(),
                'response_body' => Str::limit($response->body(), 1000),
                'status' => $response->successful() ? 'success' : 'failed',
                'delivered_at' => $response->successful() ? now() : null,
                'attempt' => $this->attempts(),
            ]);

            if (!$response->successful()) {
                throw new Exception("Webhook delivery failed with status: {$response->status()}");
            }

        } catch (Exception $e) {
            $this->delivery->update([
                'status' => 'failed',
                'response_body' => Str::limit($e->getMessage(), 1000),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw to trigger retry mechanism if tries < limit
            throw $e;
        }
    }
}
