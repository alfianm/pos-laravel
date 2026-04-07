<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService implements PaymentGatewayInterface
{
    protected string $secretKey;
    protected string $webhookToken;
    protected bool $isTestMode;

    public function __construct()
    {
        $config = PaymentGatewayConfig::where('provider', 'xendit')->first();

        if ($config) {
            $this->secretKey = $config->config['secret_key'] ?? '';
            $this->webhookToken = $config->config['webhook_secret'] ?? '';
            $this->isTestMode = $config->is_test_mode;
        } else {
            $this->secretKey = config('services.xendit.secret_key');
            $this->webhookToken = config('services.xendit.webhook_token');
            $this->isTestMode = config('services.xendit.is_test_mode', true);
        }
    }

    /**
     * @inheritDoc
     */
    public function createInvoice(array $data): array
    {
        // Example implementation with Xendit API
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id' => $data['external_id'],
                    'amount' => $data['amount'],
                    'payer_email' => $data['payer_email'],
                    'description' => $data['description'],
                    'invoice_duration' => 86400, // 24 hours
                    'success_redirect_url' => $data['success_redirect_url'],
                    'failure_redirect_url' => $data['failure_redirect_url'],
                    'currency' => 'IDR',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'invoice_url' => $response->json('invoice_url'),
                    'external_id' => $response->json('external_id'),
                ];
            }

            Log::error('Xendit Invoice Creation Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Unknown Error',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Internal error connecting to gateway',
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function verifyWebhook(array $headers, string $payload): bool
    {
        $callbackToken = $headers['x-callback-token'] ?? null;

        if (!$callbackToken) {
            return false;
        }

        return $callbackToken === $this->webhookToken;
    }

    /**
     * @inheritDoc
     */
    public function handleWebhook(array $payload): bool
    {
        // Actual logic to update records will be called from here
        // or from the event listener that this service triggers.
        
        Log::info('Xendit Webhook Received', $payload);
        
        // Return true if handled successfully
        return true;
    }
}
