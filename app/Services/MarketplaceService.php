<?php

namespace App\Services;

use App\Models\MarketplaceAccount;
use App\Models\MarketplaceSyncLog;

class MarketplaceService
{
    protected array $platforms = [
        'tokopedia' => [
            'name' => 'Tokopedia',
            'baseUrl' => 'https://api.tokopedia.com',
        ],
        'shopee' => [
            'name' => 'Shopee',
            'baseUrl' => 'https://partner.shopeemobile.com',
        ],
        'lazada' => [
            'name' => 'Lazada',
            'baseUrl' => 'https://api.lazada.com',
        ],
        'bukalapak' => [
            'name' => 'Bukalapak',
            'baseUrl' => 'https://api.bukalapak.com',
        ],
        'blibli' => [
            'name' => 'Blibli',
            'baseUrl' => 'https://api.blibli.com',
        ],
    ];

    public function saveCredentials(MarketplaceAccount $account, array $credentials): MarketplaceAccount
    {
        $account->api_key = $credentials['api_key'] ?? null;
        $account->api_secret = $credentials['api_secret'] ?? null;
        $account->access_token = $credentials['access_token'] ?? null;
        $account->refresh_token = $credentials['refresh_token'] ?? null;

        if (isset($credentials['expires_in'])) {
            $account->expires_at = now()->addSeconds($credentials['expires_in']);
        }

        if (isset($credentials['meta'])) {
            $account->meta = $credentials['meta'];
        }

        $account->status = 'active';
        $account->save();

        return $account->fresh();
    }

    public function updateTokens(MarketplaceAccount $account, array $tokens): MarketplaceAccount
    {
        $account->access_token = $tokens['access_token'] ?? null;
        $account->refresh_token = $tokens['refresh_token'] ?? null;

        if (isset($tokens['expires_in'])) {
            $account->expires_at = now()->addSeconds($tokens['expires_in']);
        }

        $account->save();

        return $account->fresh();
    }

    public function getDecryptedCredentials(MarketplaceAccount $account): array
    {
        return [
            'api_key' => $account->api_key,
            'api_secret' => $account->api_secret,
            'access_token' => $account->access_token,
            'refresh_token' => $account->refresh_token,
            'expires_at' => $account->expires_at,
        ];
    }

    public function isTokenValid(MarketplaceAccount $account): bool
    {
        if (! $account->access_token) {
            return false;
        }

        if ($account->isTokenExpired()) {
            return false;
        }

        return true;
    }

    public function needsTokenRefresh(MarketplaceAccount $account): bool
    {
        if (! $account->refresh_token) {
            return false;
        }

        if (! $account->expires_at) {
            return false;
        }

        return $account->expires_at->subMinutes(30)->isPast();
    }

    public function logSync(MarketplaceAccount $account, string $type, string $status, ?string $message = null, ?array $payload = null): MarketplaceSyncLog
    {
        return MarketplaceSyncLog::create([
            'tenant_id' => $account->tenant_id,
            'marketplace' => $account->marketplace,
            'sync_type' => $type,
            'status' => $status,
            'error_message' => $message,
            'payload' => $payload,
            'synced_at' => now(),
        ]);
    }

    public function getPlatformInfo(string $platform): ?array
    {
        return $this->platforms[$platform] ?? null;
    }

    public function getAvailablePlatforms(): array
    {
        return $this->platforms;
    }

    public function disconnect(MarketplaceAccount $account): void
    {
        $account->update([
            'access_token' => null,
            'refresh_token' => null,
            'expires_at' => null,
            'status' => 'disconnected',
        ]);

        $this->logSync($account, 'disconnect', 'success', 'Account disconnected successfully');
    }

    public function reconnect(MarketplaceAccount $account, array $newTokens): MarketplaceAccount
    {
        $account = $this->updateTokens($account, $newTokens);
        $account->update(['status' => 'active']);

        $this->logSync($account, 'reconnect', 'success', 'Account reconnected successfully');

        return $account;
    }
}
