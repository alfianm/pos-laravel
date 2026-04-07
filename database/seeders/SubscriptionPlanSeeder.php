<?php

namespace Database\Seeders;

use App\Constants\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => Subscription::PLAN_FREE,
                'name' => 'Free',
                'description' => 'Cocok untuk UMKM yang baru memulai. Fitur dasar POS dan manajemen produk.',
                'billing_cycle' => Subscription::BILLING_MONTHLY,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => [
                    'max_branches' => 1,
                    'max_products' => 100,
                    'max_users' => 2,
                    'max_monthly_transactions' => 500,
                    'storage_mb' => 100,
                    'modules' => ['pos', 'inventory', 'master_data'],
                    'support' => 'email',
                    'api_access' => false,
                    'custom_domain' => false,
                    'white_label' => false,
                ],
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 1,
            ],
            [
                'code' => Subscription::PLAN_STARTER,
                'name' => 'Starter',
                'description' => 'Cocok untuk bisnis retail dengan 1-3 cabang. Semua fitur Free + fitur lanjutan.',
                'billing_cycle' => Subscription::BILLING_MONTHLY,
                'price_monthly' => 299000,
                'price_yearly' => 2990000,
                'features' => [
                    'max_branches' => 3,
                    'max_products' => 1000,
                    'max_users' => 10,
                    'max_monthly_transactions' => 5000,
                    'storage_mb' => 1000,
                    'modules' => ['pos', 'inventory', 'master_data', 'purchasing', 'expenses', 'reports'],
                    'support' => 'priority_email',
                    'api_access' => true,
                    'custom_domain' => false,
                    'white_label' => false,
                ],
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 2,
            ],
            [
                'code' => Subscription::PLAN_PRO,
                'name' => 'Pro',
                'description' => 'Cocok untuk bisnis dengan 5-10 cabang. Fitur lengkap termasuk CRM dan Loyalty.',
                'billing_cycle' => Subscription::BILLING_MONTHLY,
                'price_monthly' => 799000,
                'price_yearly' => 7990000,
                'features' => [
                    'max_branches' => 10,
                    'max_products' => 5000,
                    'max_users' => 30,
                    'max_monthly_transactions' => 20000,
                    'storage_mb' => 5000,
                    'modules' => ['pos', 'inventory', 'master_data', 'purchasing', 'expenses', 'crm', 'loyalty', 'reports', 'marketplace', 'accounting'],
                    'support' => 'priority_chat',
                    'api_access' => true,
                    'custom_domain' => true,
                    'white_label' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 3,
            ],
            [
                'code' => Subscription::PLAN_ENTERPRISE,
                'name' => 'Enterprise',
                'description' => 'Solusi kustom untuk bisnis dengan kebutuhan khusus. Hubungi kami untuk penawaran.',
                'billing_cycle' => Subscription::BILLING_MONTHLY,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => [
                    'max_branches' => -1,
                    'max_products' => -1,
                    'max_users' => -1,
                    'max_monthly_transactions' => -1,
                    'storage_mb' => -1,
                    'modules' => ['pos', 'inventory', 'master_data', 'purchasing', 'expenses', 'crm', 'loyalty', 'reports', 'marketplace', 'api', 'webhooks', 'accounting'],
                    'support' => 'dedicated_account_manager',
                    'api_access' => true,
                    'custom_domain' => true,
                    'white_label' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(
                ['code' => $plan['code']],
                $plan
            );
        }
    }
}
