<?php

use App\Http\Controllers\Api\Webhook\PaymentWebhookController;
use App\Http\Controllers\Api\Webhook\StockSyncController;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', function () {
    return response()->json(['status' => 'alive']);
})->middleware(['throttle:tenant-api']);

Route::prefix('webhook')->group(function () {
    Route::post('xendit', [PaymentWebhookController::class, 'handleXendit'])->name('webhook.xendit');
    Route::post('stock-sync/{tenant}', [StockSyncController::class, 'handle'])->name('webhook.stock-sync');
});
