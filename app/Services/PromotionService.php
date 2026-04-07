<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\PromoRule;

class PromotionService
{
    /**
     * Validate inclusive rules for a voucher against current cart.
     * $cartItems shape: [['id' => '...', 'qty' => 2, 'price' => 10000, 'sku' => '...'], ...]
     */
    public function validateRules(Voucher $voucher, array $cartItems): array
    {
        $rules = $voucher->rules()->where('is_active', true)->get();
        
        if ($rules->isEmpty()) {
            return ['valid' => true];
        }

        foreach ($rules as $rule) {
            $isValid = match ($rule->type) {
                'min_qty' => $this->validateMinQty($rule, $cartItems),
                'buy_x_get_y' => $this->validateBuyXGetY($rule, $cartItems),
                'bundle' => $this->validateBundle($rule, $cartItems),
                default => true,
            };

            if (!$isValid) {
                return [
                    'valid' => false, 
                    'message' => $this->getRuleErrorMessage($rule)
                ];
            }
        }

        return ['valid' => true];
    }

    protected function validateMinQty(PromoRule $rule, array $cartItems): bool
    {
        $data = $rule->rule_data;
        $targetSku = $data['target_sku'] ?? null;
        $minQty = $data['min_qty'] ?? 1;

        if (!$targetSku) return true;

        $totalQty = 0;
        foreach ($cartItems as $item) {
            if ($item['sku'] === $targetSku) {
                $totalQty += $item['qty'];
            }
        }

        return $totalQty >= $minQty;
    }

    protected function validateBuyXGetY(PromoRule $rule, array $cartItems): bool
    {
        $data = $rule->rule_data;
        $buySku = $data['buy_sku'] ?? null;
        $buyQty = $data['buy_qty'] ?? 1;

        if (!$buySku) return true;

        $totalBuyQty = 0;
        foreach ($cartItems as $item) {
            if ($item['sku'] === $buySku) {
                $totalBuyQty += $item['qty'];
            }
        }

        return $totalBuyQty >= $buyQty;
    }

    protected function validateBundle(PromoRule $rule, array $cartItems): bool
    {
        $data = $rule->rule_data;
        $requiredSkus = $data['bundle_skus'] ?? []; // ['SKU-A', 'SKU-B']

        if (empty($requiredSkus)) return true;

        $cartSkus = array_column($cartItems, 'sku');
        
        foreach ($requiredSkus as $sku) {
            if (!in_array($sku, $cartSkus)) {
                return false;
            }
        }

        return true;
    }

    protected function getRuleErrorMessage(PromoRule $rule): string
    {
        $data = $rule->rule_data;
        return match ($rule->type) {
            'min_qty' => "Minimal pembelian produk {$data['target_sku']} adalah {$data['min_qty']} unit.",
            'buy_x_get_y' => "Promo ini memerlukan minimal {$data['buy_qty']} unit produk {$data['buy_sku']}.",
            'bundle' => "Promo ini hanya berlaku untuk pembelian paket produk tertentu.",
            default => "Syarat promo tidak terpenuhi.",
        };
    }
}
