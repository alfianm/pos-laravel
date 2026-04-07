<?php

namespace App\Services;

use App\Models\Voucher;
use Illuminate\Support\Str;

class VoucherService
{
    /**
     * Generate a unique voucher code.
     */
    public function generateCode(string $prefix = '', int $length = 8): string
    {
        $code = $prefix . strtoupper(Str::random($length));
        
        // Ensure uniqueness for the tenant context is handled during creation
        return $code;
    }

    /**
     * Create a new voucher programmatically.
     */
    public function createVoucher(array $data)
    {
        $tenantId = $data['tenant_id'] ?? auth()->user()->tenant_id;

        return Voucher::create([
            'tenant_id' => $tenantId,
            'code' => $data['code'] ?? $this->generateCode($data['prefix'] ?? ''),
            'type' => $data['type'] ?? 'fixed',
            'value' => $data['value'],
            'starts_at' => $data['starts_at'] ?? now(),
            'ends_at' => $data['ends_at'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'min_order_amount' => $data['min_order_amount'] ?? 0,
            'max_discount_amount' => $data['max_discount_amount'] ?? null,
            'membership_tier_id' => $data['membership_tier_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}
