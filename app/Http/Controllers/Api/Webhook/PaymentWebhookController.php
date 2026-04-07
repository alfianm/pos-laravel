<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateways\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Xendit webhooks.
     *
     * @param Request $request
     * @param XenditService $xenditService
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleXendit(Request $request, XenditService $xenditService)
    {
        Log::info('Xendit Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        if (!$xenditService->verifyWebhook($request->headers->all(), $request->getContent())) {
            Log::warning('Xendit Webhook Signature Failed Verification');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Logic here to process the payload (e.g., mark as paid)
        // This usually triggers a job to ensure idempotency.
        
        $xenditService->handleWebhook($request->all());

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }
}
