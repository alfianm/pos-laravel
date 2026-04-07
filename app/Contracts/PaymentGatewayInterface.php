<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create an external invoice/payment session.
     *
     * @param array $data {
     *  external_id: string,
     *  amount: float,
     *  payer_email: string,
     *  description: string,
     *  callback_url: string,
     *  success_redirect_url: string,
     *  failure_redirect_url: string
     * }
     * @return array Response from the gateway (e.g., invoice URL)
     */
    public function createInvoice(array $data): array;

    /**
     * Verify the webhook signature.
     *
     * @param array $headers
     * @param string $payload
     * @return bool
     */
    public function verifyWebhook(array $headers, string $payload): bool;

    /**
     * Handle the incoming payment status change logic.
     *
     * @param array $payload
     * @return bool
     */
    public function handleWebhook(array $payload): bool;
}
