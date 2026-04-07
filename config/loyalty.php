<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Loyalty System Configuration
    |--------------------------------------------------------------------------
    */

    'enabled' => env('LOYALTY_ENABLED', true),

    // How many Rupiah spent to earn 1 point (before multipliers)
    'points_base_rate' => env('LOYALTY_POINTS_BASE_RATE', 10000),

    // How many Rupiah value per 1 point for redemption
    'value_per_point' => env('LOYALTY_VALUE_PER_POINT', 10),

    // Default point expiration in months
    'points_expiry_months' => env('LOYALTY_POINTS_EXPIRY_MONTHS', 12),

    // Auto-downgrade check period in months (look back spending)
    'downgrade_check_months' => env('LOYALTY_DOWNGRADE_CHECK_MONTHS', 12),
];
