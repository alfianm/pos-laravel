<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Services\LoyaltyService;
use App\Services\StockService;
use Carbon\CarbonInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RealisticDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    private StockService $stockService;

    private LoyaltyService $loyaltyService;

    private Carbon $now;

    /** @var array<string, list<string>> */
    private array $tableColumns = [];

    /** @var array<string, array<string, string>> */
    private array $tableColumnTypes = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->stockService = app(StockService::class);
        $this->loyaltyService = app(LoyaltyService::class);
        $this->now = now();

        Model::unguard();

        try {
            DB::transaction(function (): void {
                $tenant = $this->seedTenant();
                $branches = $this->seedBranches($tenant->id);
                $users = $this->seedUsers($tenant->id, $branches);

                $this->cleanupTenantData($tenant->id);
                $this->seedSettings($tenant->id, $branches);

                $reference = $this->seedReferenceData($tenant->id);
                $catalog = $this->seedCatalog(
                    $tenant->id,
                    $branches,
                    $reference['brands'],
                    $reference['units'],
                    $reference['categories'],
                );

                $customers = $this->seedCustomers($tenant->id, $branches, $reference['customer_groups']);
                $purchases = $this->seedPurchases(
                    $tenant->id,
                    $branches,
                    $users,
                    $reference['suppliers'],
                    $catalog['products'],
                    $catalog['variants'],
                );

                $this->seedTransfers(
                    $tenant->id,
                    $branches,
                    $users,
                    $catalog['products'],
                    $catalog['variants'],
                );

                $cashSession = $this->seedCashSession($tenant->id, $branches['main'], $users['cashier']);

                $this->seedSales(
                    $tenant->id,
                    $branches,
                    $users,
                    $customers,
                    $catalog['products'],
                    $catalog['variants'],
                    $reference['vouchers'],
                    $cashSession,
                );

                $this->closeCashSession($cashSession->id);

                $this->seedStockAdjustments(
                    $tenant->id,
                    $branches,
                    $users,
                    $catalog['products'],
                    $catalog['variants'],
                );

                $this->seedExpenses(
                    $tenant->id,
                    $branches,
                    $users,
                    $reference['expense_categories'],
                );

                $this->seedCrm(
                    $tenant->id,
                    $branches,
                    $users,
                    $customers,
                    $reference['lead_sources'],
                    $reference['lead_stages'],
                    $catalog['products'],
                    $catalog['variants'],
                );

                $this->seedMarketplace(
                    $tenant->id,
                    $branches,
                    $users,
                    $catalog['products'],
                    $catalog['variants'],
                );

                $this->seedAuditTrail($tenant->id, $branches, $users, $purchases);
                $this->seedNotifications($tenant->id, $branches, $users);
            });

            $this->command?->info('Realistic demo data seeded for tenant PT Makmur Sentosa.');
        } finally {
            Model::reguard();
        }
    }

    private function seedTenant(): object
    {
        return $this->upsertRecord('tenants', ['code' => 'MAK'], [
            'name' => 'PT Makmur Sentosa',
            'slug' => 'pt-makmur-sentosa',
            'email' => 'admin@makmur.com',
            'phone' => '021-8066-4100',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'tax_number' => '01.234.567.8-091.000',
            'address' => 'Jl. Kemang Raya No. 18, Bangka, Mampang Prapatan',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12730',
            'status' => 'active',
            'settings' => [
                'business_type' => 'retail-grocery',
                'demo_seeded_at' => $this->now->toIso8601String(),
            ],
        ]);
    }

    /**
     * @return array<string, object>
     */
    private function seedBranches(string $tenantId): array
    {
        return [
            'main' => $this->upsertRecord('branches', ['tenant_id' => $tenantId, 'code' => 'MAK'], [
                'name' => 'Makmur Pusat Jakarta',
                'slug' => 'makmur-pusat-jakarta',
                'type' => 'store',
                'email' => 'jakarta@makmur.co.id',
                'phone' => '021-798-1212',
                'address' => 'Jl. Kemang Raya No. 18, Bangka, Mampang Prapatan',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12730',
                'is_main' => true,
                'status' => 'active',
                'settings' => [
                    'warehouse_zone' => 'A1',
                    'supports_marketplace_pickup' => true,
                ],
            ]),
            'bandung' => $this->upsertRecord('branches', ['tenant_id' => $tenantId, 'code' => 'BDG'], [
                'name' => 'Makmur Bandung Dago',
                'slug' => 'makmur-bandung-dago',
                'type' => 'store',
                'email' => 'bandung@makmur.co.id',
                'phone' => '022-204-8811',
                'address' => 'Jl. Ir. H. Juanda No. 112, Dago',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40135',
                'is_main' => false,
                'status' => 'active',
                'settings' => [
                    'warehouse_zone' => 'B2',
                    'supports_marketplace_pickup' => false,
                ],
            ]),
            'surabaya' => $this->upsertRecord('branches', ['tenant_id' => $tenantId, 'code' => 'SBY'], [
                'name' => 'Makmur Surabaya Manyar',
                'slug' => 'makmur-surabaya-manyar',
                'type' => 'store',
                'email' => 'surabaya@makmur.co.id',
                'phone' => '031-594-1100',
                'address' => 'Jl. Manyar Kertoarjo No. 59',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60284',
                'is_main' => false,
                'status' => 'active',
                'settings' => [
                    'warehouse_zone' => 'C1',
                    'supports_marketplace_pickup' => true,
                ],
            ]),
        ];
    }

    /**
     * @param  array<string, object>  $branches
     * @return array<string, object>
     */
    private function seedUsers(string $tenantId, array $branches): array
    {
        $users = [
            'admin' => $this->upsertRecord('users', ['email' => 'admin@makmur.com'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Arif Setiawan',
                'phone' => '0812-1111-2200',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'is_super_admin' => true,
                'status' => 'active',
                'preferences' => ['dashboard' => 'owner', 'locale' => 'id'],
            ]),
            'owner' => $this->upsertRecord('users', ['email' => 'owner@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Andre Wijaya',
                'phone' => '0812-2222-3300',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
                'preferences' => ['dashboard' => 'owner', 'locale' => 'id'],
            ]),
            'manager_jakarta' => $this->upsertRecord('users', ['email' => 'manager.jakarta@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Maya Lestari',
                'phone' => '0812-3333-4401',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
                'preferences' => ['dashboard' => 'branch', 'locale' => 'id'],
            ]),
            'manager_bandung' => $this->upsertRecord('users', ['email' => 'manager.bandung@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['bandung']->id,
                'name' => 'Doni Permana',
                'phone' => '0812-3333-4402',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
                'preferences' => ['dashboard' => 'branch', 'locale' => 'id'],
            ]),
            'cashier' => $this->upsertRecord('users', ['email' => 'cashier.kemang@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Fitri Amelia',
                'phone' => '0812-3333-4403',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
            ]),
            'inventory' => $this->upsertRecord('users', ['email' => 'inventory@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Bagus Prasetyo',
                'phone' => '0812-3333-4404',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
            ]),
            'purchasing' => $this->upsertRecord('users', ['email' => 'purchasing@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Santi Rahayu',
                'phone' => '0812-3333-4405',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
            ]),
            'crm' => $this->upsertRecord('users', ['email' => 'crm@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Nadia Putri',
                'phone' => '0812-3333-4406',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
            ]),
            'omnichannel' => $this->upsertRecord('users', ['email' => 'omni@makmur.co.id'], [
                'tenant_id' => $tenantId,
                'active_branch_id' => $branches['main']->id,
                'name' => 'Fajar Pratama',
                'phone' => '0812-3333-4407',
                'password' => Hash::make('password'),
                'email_verified_at' => $this->now,
                'status' => 'active',
            ]),
        ];

        $this->attachRole($users['admin']->id, 'super_admin');
        $this->attachRole($users['owner']->id, 'owner');
        $this->attachRole($users['manager_jakarta']->id, 'branch_manager');
        $this->attachRole($users['manager_bandung']->id, 'branch_manager');
        $this->attachRole($users['cashier']->id, 'cashier');
        $this->attachRole($users['inventory']->id, 'inventory_staff');
        $this->attachRole($users['purchasing']->id, 'purchasing_staff');
        $this->attachRole($users['crm']->id, 'crm_staff');
        $this->attachRole($users['omnichannel']->id, 'omnichannel_staff');

        $this->syncBranchMemberships($tenantId, $users['admin']->id, $branches, ['main', 'bandung', 'surabaya'], 'main');
        $this->syncBranchMemberships($tenantId, $users['owner']->id, $branches, ['main', 'bandung', 'surabaya'], 'main');
        $this->syncBranchMemberships($tenantId, $users['manager_jakarta']->id, $branches, ['main'], 'main');
        $this->syncBranchMemberships($tenantId, $users['manager_bandung']->id, $branches, ['bandung'], 'bandung');
        $this->syncBranchMemberships($tenantId, $users['cashier']->id, $branches, ['main'], 'main');
        $this->syncBranchMemberships($tenantId, $users['inventory']->id, $branches, ['main', 'bandung', 'surabaya'], 'main');
        $this->syncBranchMemberships($tenantId, $users['purchasing']->id, $branches, ['main'], 'main');
        $this->syncBranchMemberships($tenantId, $users['crm']->id, $branches, ['main', 'bandung'], 'main');
        $this->syncBranchMemberships($tenantId, $users['omnichannel']->id, $branches, ['main', 'surabaya'], 'main');

        return $users;
    }

    private function cleanupTenantData(string $tenantId): void
    {
        foreach ([
            'notifications_log',
            'audit_logs',
            'customer_timelines',
            'follow_ups',
            'marketplace_sync_logs',
            'marketplace_orders',
            'marketplace_product_maps',
            'marketplace_shops',
            'marketplace_accounts',
            'proposals',
            'leads',
            'loyalty_transactions',
            'loyalty_accounts',
            'sales',
            'purchase_orders',
            'stock_transfers',
            'stock_adjustments',
            'expenses',
            'cash_register_sessions',
            'stock_movements',
            'settings',
            'branch_prices',
            'inventories',
            'vouchers',
            'membership_tiers',
            'products',
            'product_categories',
            'brands',
            'units',
            'suppliers',
            'customer_groups',
            'expense_categories',
            'customers',
            'lead_stages',
            'lead_sources',
        ] as $table) {
            DB::table($table)->where('tenant_id', $tenantId)->delete();
        }
    }

    /**
     * @param  array<string, object>  $branches
     */
    private function seedSettings(string $tenantId, array $branches): void
    {
        $settings = [
            [
                'tenant_id' => $tenantId,
                'branch_id' => null,
                'group' => 'company',
                'key' => 'profile',
                'value' => [
                    'display_name' => 'PT Makmur Sentosa',
                    'receipt_footer' => 'Terima kasih sudah belanja di Makmur Sentosa.',
                    'support_phone' => '021-8066-4100',
                ],
            ],
            [
                'tenant_id' => $tenantId,
                'branch_id' => null,
                'group' => 'loyalty',
                'key' => 'rules',
                'value' => [
                    'earn_rate_per' => 10000,
                    'max_redeem_percent' => 20,
                    'tier_review' => 'monthly',
                ],
            ],
            [
                'tenant_id' => $tenantId,
                'branch_id' => null,
                'group' => 'marketplace',
                'key' => 'sync',
                'value' => [
                    'inventory_buffer' => 3,
                    'auto_import_orders' => true,
                    'sync_window_minutes' => 15,
                ],
            ],
            [
                'tenant_id' => $tenantId,
                'branch_id' => $branches['main']->id,
                'group' => 'pos',
                'key' => 'terminal',
                'value' => [
                    'terminal_name' => 'Kasir Kemang 01',
                    'supports_qris' => true,
                ],
            ],
            [
                'tenant_id' => $tenantId,
                'branch_id' => $branches['bandung']->id,
                'group' => 'pos',
                'key' => 'terminal',
                'value' => [
                    'terminal_name' => 'Kasir Dago 01',
                    'supports_qris' => true,
                ],
            ],
            [
                'tenant_id' => $tenantId,
                'branch_id' => $branches['surabaya']->id,
                'group' => 'pos',
                'key' => 'terminal',
                'value' => [
                    'terminal_name' => 'Kasir Manyar 01',
                    'supports_qris' => true,
                ],
            ],
        ];

        foreach ($settings as $setting) {
            $this->upsertRecord('settings', [
                'tenant_id' => $setting['tenant_id'],
                'branch_id' => $setting['branch_id'],
                'group' => $setting['group'],
                'key' => $setting['key'],
            ], [
                'value' => $setting['value'],
            ]);
        }
    }

    /**
     * @return array<string, array<string, object>>
     */
    private function seedReferenceData(string $tenantId): array
    {
        $customerGroups = [
            'retail' => $this->upsertRecord('customer_groups', ['tenant_id' => $tenantId, 'name' => 'Retail'], [
                'discount_percentage' => 0,
            ]),
            'silver' => $this->upsertRecord('customer_groups', ['tenant_id' => $tenantId, 'name' => 'Member Silver'], [
                'discount_percentage' => 2.5,
            ]),
            'gold' => $this->upsertRecord('customer_groups', ['tenant_id' => $tenantId, 'name' => 'Member Gold'], [
                'discount_percentage' => 5,
            ]),
            'corporate' => $this->upsertRecord('customer_groups', ['tenant_id' => $tenantId, 'name' => 'Corporate'], [
                'discount_percentage' => 8,
            ]),
        ];

        $suppliers = [
            'indofood' => $this->upsertRecord('suppliers', ['tenant_id' => $tenantId, 'code' => 'SUP-MAK-001'], [
                'name' => 'PT Indofood CBP Sukses Makmur Tbk',
                'email' => 'distribusi@indofood.co.id',
                'phone' => '021-5795-8822',
                'contact_person' => 'Rizal Fadli',
                'address' => 'Jl. Jenderal Sudirman Kav. 76-78',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12910',
                'payment_terms_days' => 14,
                'status' => 'active',
                'meta' => ['category' => 'FMCG', 'priority' => 'A'],
            ]),
            'aqua' => $this->upsertRecord('suppliers', ['tenant_id' => $tenantId, 'code' => 'SUP-MAK-002'], [
                'name' => 'PT Tirta Investama',
                'email' => 'moderntrade@aqua.com',
                'phone' => '021-8062-8000',
                'contact_person' => 'Helmi Ramadhan',
                'address' => 'Jl. Pulo Lentut No. 3',
                'city' => 'Jakarta Timur',
                'province' => 'DKI Jakarta',
                'postal_code' => '13920',
                'payment_terms_days' => 10,
                'status' => 'active',
            ]),
            'sosro' => $this->upsertRecord('suppliers', ['tenant_id' => $tenantId, 'code' => 'SUP-MAK-003'], [
                'name' => 'PT Sinar Sosro',
                'email' => 'sales.moderntrade@sosro.com',
                'phone' => '021-8660-6600',
                'contact_person' => 'Yessy Puspita',
                'address' => 'Jl. Raya Sultan Agung Km. 28,5',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'postal_code' => '17132',
                'payment_terms_days' => 14,
                'status' => 'active',
            ]),
            'unilever' => $this->upsertRecord('suppliers', ['tenant_id' => $tenantId, 'code' => 'SUP-MAK-004'], [
                'name' => 'PT Unilever Indonesia Tbk',
                'email' => 'sales@unilever.com',
                'phone' => '021-8082-7000',
                'contact_person' => 'Tania Kusuma',
                'address' => 'Grha Unilever, BSD Green Office Park',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'postal_code' => '15345',
                'payment_terms_days' => 21,
                'status' => 'active',
            ]),
            'mayora' => $this->upsertRecord('suppliers', ['tenant_id' => $tenantId, 'code' => 'SUP-MAK-005'], [
                'name' => 'PT Mayora Indah Tbk',
                'email' => 'b2b@mayora.co.id',
                'phone' => '021-565-5322',
                'contact_person' => 'Widya Sari',
                'address' => 'Jl. Tomang Raya No. 21-23',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'postal_code' => '11440',
                'payment_terms_days' => 14,
                'status' => 'active',
            ]),
            'wings' => $this->upsertRecord('suppliers', ['tenant_id' => $tenantId, 'code' => 'SUP-MAK-006'], [
                'name' => 'PT Wings Surya',
                'email' => 'distribusi@wingscorp.com',
                'phone' => '031-9900-0000',
                'contact_person' => 'Agus Salim',
                'address' => 'Jl. Tipar Cakung Kav. F 5-7',
                'city' => 'Jakarta Timur',
                'province' => 'DKI Jakarta',
                'postal_code' => '13910',
                'payment_terms_days' => 7,
                'status' => 'active',
            ]),
        ];

        $brands = [
            'indomie' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Indomie'], []),
            'aqua' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Aqua'], []),
            'teh-botol-sosro' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Teh Botol Sosro'], []),
            'kapal-api' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Kapal Api'], []),
            'pepsodent' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Pepsodent'], []),
            'sunlight' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Sunlight'], []),
            'roma' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Roma'], []),
            'sari-roti' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Sari Roti'], []),
            'bear-brand' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Bear Brand'], []),
            'ramos' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Ramos'], []),
            'marjan' => $this->upsertRecord('brands', ['tenant_id' => $tenantId, 'name' => 'Marjan'], []),
        ];

        $units = [
            'pcs' => $this->upsertRecord('units', ['tenant_id' => $tenantId, 'short_name' => 'pcs'], ['name' => 'Pieces']),
            'pack' => $this->upsertRecord('units', ['tenant_id' => $tenantId, 'short_name' => 'pack'], ['name' => 'Pack']),
            'botol' => $this->upsertRecord('units', ['tenant_id' => $tenantId, 'short_name' => 'btl'], ['name' => 'Botol']),
            'kg' => $this->upsertRecord('units', ['tenant_id' => $tenantId, 'short_name' => 'kg'], ['name' => 'Kilogram']),
            'dus' => $this->upsertRecord('units', ['tenant_id' => $tenantId, 'short_name' => 'dus'], ['name' => 'Dus']),
        ];

        $categories = [
            'makanan-instan' => $this->upsertRecord('product_categories', ['tenant_id' => $tenantId, 'slug' => 'makanan-instan'], [
                'name' => 'Makanan Instan',
            ]),
            'minuman' => $this->upsertRecord('product_categories', ['tenant_id' => $tenantId, 'slug' => 'minuman'], [
                'name' => 'Minuman',
            ]),
            'sembako' => $this->upsertRecord('product_categories', ['tenant_id' => $tenantId, 'slug' => 'sembako'], [
                'name' => 'Sembako',
            ]),
            'perawatan-rumah' => $this->upsertRecord('product_categories', ['tenant_id' => $tenantId, 'slug' => 'perawatan-rumah'], [
                'name' => 'Perawatan Rumah',
            ]),
            'snack-roti' => $this->upsertRecord('product_categories', ['tenant_id' => $tenantId, 'slug' => 'snack-roti'], [
                'name' => 'Snack & Roti',
            ]),
            'personal-care' => $this->upsertRecord('product_categories', ['tenant_id' => $tenantId, 'slug' => 'personal-care'], [
                'name' => 'Personal Care',
            ]),
        ];

        $membershipTiers = [
            'bronze' => $this->upsertRecord('membership_tiers', ['tenant_id' => $tenantId, 'name' => 'Bronze'], [
                'min_spending' => 0,
                'point_multiplier' => 1.0,
            ]),
            'silver' => $this->upsertRecord('membership_tiers', ['tenant_id' => $tenantId, 'name' => 'Silver'], [
                'min_spending' => 500000,
                'point_multiplier' => 1.25,
            ]),
            'gold' => $this->upsertRecord('membership_tiers', ['tenant_id' => $tenantId, 'name' => 'Gold'], [
                'min_spending' => 1500000,
                'point_multiplier' => 1.5,
            ]),
        ];

        $vouchers = [
            'ramadan10' => $this->upsertRecord('vouchers', ['tenant_id' => $tenantId, 'code' => 'RAMADAN10'], [
                'type' => 'fixed',
                'value' => 10000,
                'starts_at' => Carbon::parse('2026-03-01 00:00:00'),
                'ends_at' => Carbon::parse('2026-04-15 23:59:59'),
                'usage_limit' => 500,
                'used_count' => 0,
                'min_order_amount' => 100000,
            ]),
            'hemat5' => $this->upsertRecord('vouchers', ['tenant_id' => $tenantId, 'code' => 'HEMAT5'], [
                'type' => 'percentage',
                'value' => 5,
                'starts_at' => Carbon::parse('2026-01-01 00:00:00'),
                'ends_at' => Carbon::parse('2026-12-31 23:59:59'),
                'usage_limit' => 300,
                'used_count' => 0,
                'min_order_amount' => 75000,
            ]),
        ];

        $expenseCategories = [
            'utilities' => $this->upsertRecord('expense_categories', ['tenant_id' => $tenantId, 'name' => 'Listrik & Internet'], []),
            'operations' => $this->upsertRecord('expense_categories', ['tenant_id' => $tenantId, 'name' => 'Operasional Toko'], []),
            'marketing' => $this->upsertRecord('expense_categories', ['tenant_id' => $tenantId, 'name' => 'Promosi Digital'], []),
            'logistics' => $this->upsertRecord('expense_categories', ['tenant_id' => $tenantId, 'name' => 'Pengiriman'], []),
        ];

        $leadSources = [
            'website' => $this->upsertRecord('lead_sources', ['tenant_id' => $tenantId, 'name' => 'Website Form'], []),
            'instagram' => $this->upsertRecord('lead_sources', ['tenant_id' => $tenantId, 'name' => 'Instagram Ads'], []),
            'referral' => $this->upsertRecord('lead_sources', ['tenant_id' => $tenantId, 'name' => 'Referral'], []),
            'tokopedia' => $this->upsertRecord('lead_sources', ['tenant_id' => $tenantId, 'name' => 'Tokopedia Chat'], []),
        ];

        $leadStages = [
            'new' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'New'], ['sort_order' => 1]),
            'contacted' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'Contacted'], ['sort_order' => 2]),
            'qualified' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'Qualified'], ['sort_order' => 3]),
            'proposal' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'Proposal'], ['sort_order' => 4]),
            'negotiation' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'Negotiation'], ['sort_order' => 5]),
            'won' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'Won'], ['sort_order' => 6]),
            'lost' => $this->upsertRecord('lead_stages', ['tenant_id' => $tenantId, 'name' => 'Lost'], ['sort_order' => 7]),
        ];

        return [
            'customer_groups' => $customerGroups,
            'suppliers' => $suppliers,
            'brands' => $brands,
            'units' => $units,
            'categories' => $categories,
            'membership_tiers' => $membershipTiers,
            'vouchers' => $vouchers,
            'expense_categories' => $expenseCategories,
            'lead_sources' => $leadSources,
            'lead_stages' => $leadStages,
        ];
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $brands
     * @param  array<string, object>  $units
     * @param  array<string, object>  $categories
     * @return array<string, array<string, object>>
     */
    private function seedCatalog(
        string $tenantId,
        array $branches,
        array $brands,
        array $units,
        array $categories,
    ): array {
        $products = [
            'indomie-goreng' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-INDM-GRG'], [
                'code' => 'PRD-MAK-001',
                'category_id' => $categories['makanan-instan']->id,
                'brand_id' => $brands['indomie']->id,
                'unit_id' => $units['pcs']->id,
                'barcode' => '089686041001',
                'name' => 'Indomie Mi Goreng 85 g',
                'type' => 'single',
                'purchase_price' => 2900,
                'selling_price' => 3500,
                'cost_price' => 2900,
                'track_stock' => true,
                'allow_decimal' => false,
                'has_expiry' => false,
                'is_active' => true,
                'description' => 'Mie instan goreng favorit untuk kebutuhan harian.',
            ]),
            'indomie-soto' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-INDM-STO'], [
                'code' => 'PRD-MAK-002',
                'category_id' => $categories['makanan-instan']->id,
                'brand_id' => $brands['indomie']->id,
                'unit_id' => $units['pcs']->id,
                'barcode' => '089686041002',
                'name' => 'Indomie Soto Mie 75 g',
                'type' => 'single',
                'purchase_price' => 2850,
                'selling_price' => 3500,
                'cost_price' => 2850,
                'track_stock' => true,
                'is_active' => true,
                'description' => 'Mie instan rasa soto dengan kuah gurih.',
            ]),
            'aqua-600' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-AQUA-600'], [
                'code' => 'PRD-MAK-003',
                'category_id' => $categories['minuman']->id,
                'brand_id' => $brands['aqua']->id,
                'unit_id' => $units['botol']->id,
                'barcode' => '089866300600',
                'name' => 'Aqua Air Mineral 600 ml',
                'type' => 'single',
                'purchase_price' => 3400,
                'selling_price' => 4500,
                'cost_price' => 3400,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'teh-botol' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-TBS-450'], [
                'code' => 'PRD-MAK-004',
                'category_id' => $categories['minuman']->id,
                'brand_id' => $brands['teh-botol-sosro']->id,
                'unit_id' => $units['botol']->id,
                'barcode' => '089951112450',
                'name' => 'Teh Botol Sosro 450 ml',
                'type' => 'single',
                'purchase_price' => 4300,
                'selling_price' => 5500,
                'cost_price' => 4300,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'kapal-api' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-KAPI-SPM'], [
                'code' => 'PRD-MAK-005',
                'category_id' => $categories['minuman']->id,
                'brand_id' => $brands['kapal-api']->id,
                'unit_id' => $units['pack']->id,
                'barcode' => '089345001001',
                'name' => 'Kapal Api Special Mix 10 Sachet',
                'type' => 'single',
                'purchase_price' => 12800,
                'selling_price' => 16000,
                'cost_price' => 12800,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'pepsodent' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-PEP-190'], [
                'code' => 'PRD-MAK-006',
                'category_id' => $categories['personal-care']->id,
                'brand_id' => $brands['pepsodent']->id,
                'unit_id' => $units['pcs']->id,
                'barcode' => '089911101900',
                'name' => 'Pepsodent Complete 190 g',
                'type' => 'single',
                'purchase_price' => 11500,
                'selling_price' => 14500,
                'cost_price' => 11500,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'sunlight' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-SUN-755'], [
                'code' => 'PRD-MAK-007',
                'category_id' => $categories['perawatan-rumah']->id,
                'brand_id' => $brands['sunlight']->id,
                'unit_id' => $units['botol']->id,
                'barcode' => '089911107550',
                'name' => 'Sunlight Jeruk Nipis 755 ml',
                'type' => 'single',
                'purchase_price' => 18500,
                'selling_price' => 23000,
                'cost_price' => 18500,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'roma' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-ROMA-KLP'], [
                'code' => 'PRD-MAK-008',
                'category_id' => $categories['snack-roti']->id,
                'brand_id' => $brands['roma']->id,
                'unit_id' => $units['pack']->id,
                'barcode' => '089300055501',
                'name' => 'Roma Kelapa 300 g',
                'type' => 'single',
                'purchase_price' => 8200,
                'selling_price' => 10500,
                'cost_price' => 8200,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'sari-roti' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-SR-TWR'], [
                'code' => 'PRD-MAK-009',
                'category_id' => $categories['snack-roti']->id,
                'brand_id' => $brands['sari-roti']->id,
                'unit_id' => $units['pcs']->id,
                'barcode' => '089730010001',
                'name' => 'Sari Roti Tawar Special',
                'type' => 'single',
                'purchase_price' => 14200,
                'selling_price' => 18000,
                'cost_price' => 14200,
                'track_stock' => true,
                'has_expiry' => true,
                'is_active' => true,
            ]),
            'bear-brand' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-BEAR-189'], [
                'code' => 'PRD-MAK-010',
                'category_id' => $categories['minuman']->id,
                'brand_id' => $brands['bear-brand']->id,
                'unit_id' => $units['botol']->id,
                'barcode' => '089700010189',
                'name' => 'Bear Brand Susu Steril 189 ml',
                'type' => 'single',
                'purchase_price' => 9200,
                'selling_price' => 11500,
                'cost_price' => 9200,
                'track_stock' => true,
                'is_active' => true,
            ]),
            'beras-ramos' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-RMS-BASE'], [
                'code' => 'PRD-MAK-011',
                'category_id' => $categories['sembako']->id,
                'brand_id' => $brands['ramos']->id,
                'unit_id' => $units['kg']->id,
                'barcode' => null,
                'name' => 'Beras Ramos Premium',
                'type' => 'variant',
                'purchase_price' => 68000,
                'selling_price' => 74500,
                'cost_price' => 68000,
                'track_stock' => true,
                'allow_decimal' => false,
                'is_active' => true,
            ]),
            'sirup-marjan' => $this->upsertRecord('products', ['tenant_id' => $tenantId, 'sku' => 'MAK-MRJ-BASE'], [
                'code' => 'PRD-MAK-012',
                'category_id' => $categories['minuman']->id,
                'brand_id' => $brands['marjan']->id,
                'unit_id' => $units['botol']->id,
                'barcode' => null,
                'name' => 'Sirup Marjan Cocopandan',
                'type' => 'variant',
                'purchase_price' => 18000,
                'selling_price' => 21000,
                'cost_price' => 18000,
                'track_stock' => true,
                'is_active' => true,
            ]),
        ];

        $variants = [
            'beras-5kg' => $this->upsertRecord('product_variants', ['tenant_id' => $tenantId, 'sku' => 'MAK-RMS-5KG'], [
                'product_id' => $products['beras-ramos']->id,
                'name' => '5 kg',
                'barcode' => '089900015005',
                'purchase_price' => 68000,
                'selling_price' => 74500,
                'cost_price' => 68000,
                'is_default' => true,
                'is_active' => true,
                'attributes' => ['size' => '5kg'],
            ]),
            'beras-10kg' => $this->upsertRecord('product_variants', ['tenant_id' => $tenantId, 'sku' => 'MAK-RMS-10KG'], [
                'product_id' => $products['beras-ramos']->id,
                'name' => '10 kg',
                'barcode' => '089900015010',
                'purchase_price' => 132000,
                'selling_price' => 144000,
                'cost_price' => 132000,
                'is_default' => false,
                'is_active' => true,
                'attributes' => ['size' => '10kg'],
            ]),
            'marjan-460' => $this->upsertRecord('product_variants', ['tenant_id' => $tenantId, 'sku' => 'MAK-MRJ-460'], [
                'product_id' => $products['sirup-marjan']->id,
                'name' => '460 ml',
                'barcode' => '089700460460',
                'purchase_price' => 18000,
                'selling_price' => 21000,
                'cost_price' => 18000,
                'is_default' => true,
                'is_active' => true,
                'attributes' => ['size' => '460ml'],
            ]),
            'marjan-1l' => $this->upsertRecord('product_variants', ['tenant_id' => $tenantId, 'sku' => 'MAK-MRJ-1L'], [
                'product_id' => $products['sirup-marjan']->id,
                'name' => '1 Liter',
                'barcode' => '089700460100',
                'purchase_price' => 32000,
                'selling_price' => 36000,
                'cost_price' => 32000,
                'is_default' => false,
                'is_active' => true,
                'attributes' => ['size' => '1L'],
            ]),
        ];

        $branchAdjustments = [
            'main' => 0,
            'bandung' => 500,
            'surabaya' => 1000,
        ];

        foreach ([
            'indomie-goreng',
            'indomie-soto',
            'aqua-600',
            'teh-botol',
            'kapal-api',
            'pepsodent',
            'sunlight',
            'roma',
            'sari-roti',
            'bear-brand',
        ] as $productKey) {
            foreach ($branchAdjustments as $branchKey => $adjustment) {
                $this->upsertRecord('branch_prices', [
                    'tenant_id' => $tenantId,
                    'branch_id' => $branches[$branchKey]->id,
                    'product_id' => $products[$productKey]->id,
                    'product_variant_id' => null,
                ], [
                    'price' => (float) $products[$productKey]->selling_price + $adjustment,
                ]);
            }
        }

        foreach ($variants as $variantKey => $variant) {
            $productKey = str_starts_with($variantKey, 'beras') ? 'beras-ramos' : 'sirup-marjan';

            foreach ($branchAdjustments as $branchKey => $adjustment) {
                $this->upsertRecord('branch_prices', [
                    'tenant_id' => $tenantId,
                    'branch_id' => $branches[$branchKey]->id,
                    'product_id' => $products[$productKey]->id,
                    'product_variant_id' => $variant->id,
                ], [
                    'price' => (float) $variant->selling_price + $adjustment,
                ]);
            }
        }

        return [
            'products' => $products,
            'variants' => $variants,
        ];
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $customerGroups
     * @return array<string, object>
     */
    private function seedCustomers(string $tenantId, array $branches, array $customerGroups): array
    {
        return [
            'rina' => $this->upsertRecord('customers', ['tenant_id' => $tenantId, 'code' => 'CUS-MAK-001'], [
                'branch_id' => $branches['main']->id,
                'customer_group_id' => $customerGroups['silver']->id,
                'name' => 'Rina Maharani',
                'email' => 'rina.maharani@gmail.com',
                'phone' => '0813-8012-4432',
                'gender' => 'female',
                'birth_date' => '1993-04-17',
                'address' => 'Jl. Bangka XI No. 9',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12720',
                'status' => 'active',
                'source' => 'walk_in',
                'meta' => ['preferred_channel' => 'whatsapp'],
            ]),
            'budi' => $this->upsertRecord('customers', ['tenant_id' => $tenantId, 'code' => 'CUS-MAK-002'], [
                'branch_id' => $branches['bandung']->id,
                'customer_group_id' => $customerGroups['retail']->id,
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@yahoo.com',
                'phone' => '0812-9244-7712',
                'gender' => 'male',
                'birth_date' => '1988-09-08',
                'address' => 'Jl. Tubagus Ismail VIII No. 14',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40134',
                'status' => 'active',
                'source' => 'instagram',
            ]),
            'dewi' => $this->upsertRecord('customers', ['tenant_id' => $tenantId, 'code' => 'CUS-MAK-003'], [
                'branch_id' => $branches['surabaya']->id,
                'customer_group_id' => $customerGroups['gold']->id,
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.anggraini@outlook.com',
                'phone' => '0812-7788-2201',
                'gender' => 'female',
                'birth_date' => '1990-01-21',
                'address' => 'Jl. Kertajaya Indah Timur No. 22',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60116',
                'status' => 'active',
                'source' => 'member_referral',
            ]),
            'sinar-boga' => $this->upsertRecord('customers', ['tenant_id' => $tenantId, 'code' => 'CUS-MAK-004'], [
                'branch_id' => $branches['main']->id,
                'customer_group_id' => $customerGroups['corporate']->id,
                'name' => 'PT Sinar Boga Nusantara',
                'email' => 'procurement@sinarboga.co.id',
                'phone' => '021-5088-1177',
                'address' => 'Jl. Gatot Subroto Kav. 52',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12950',
                'tax_number' => '03.111.222.3-091.000',
                'notes' => 'Pelanggan B2B untuk pantry kantor dan acara internal.',
                'status' => 'active',
                'source' => 'sales_visit',
            ]),
            'hotel-melati' => $this->upsertRecord('customers', ['tenant_id' => $tenantId, 'code' => 'CUS-MAK-005'], [
                'branch_id' => $branches['main']->id,
                'customer_group_id' => $customerGroups['corporate']->id,
                'name' => 'Hotel Melati Semarang',
                'email' => 'purchasing@hotelmelati.id',
                'phone' => '024-8654-9910',
                'address' => 'Jl. Pandanaran No. 88',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'postal_code' => '50134',
                'tax_number' => '02.998.776.5-503.000',
                'status' => 'active',
                'source' => 'referral',
            ]),
        ];
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $suppliers
     * @param  array<string, object>  $products
     * @param  array<string, object>  $variants
     * @return array<string, object>
     */
    private function seedPurchases(
        string $tenantId,
        array $branches,
        array $users,
        array $suppliers,
        array $products,
        array $variants,
    ): array {
        return [
            'po-001' => $this->createPurchaseOrder(
                tenantId: $tenantId,
                branchId: $branches['main']->id,
                supplierId: $suppliers['indofood']->id,
                createdBy: $users['purchasing']->id,
                approvedBy: $users['owner']->id,
                purchaseNo: 'PO-MAK-202603-001',
                orderDate: Carbon::parse('2026-03-22'),
                expectedDate: Carbon::parse('2026-03-24'),
                status: 'received',
                paymentStatus: 'paid',
                shippingAmount: 150000,
                paymentMethod: 'bank_transfer',
                paymentAmount: null,
                items: [
                    $this->purchaseLine($products['indomie-goreng'], null, 120, 2900),
                    $this->purchaseLine($products['indomie-soto'], null, 80, 2850),
                    $this->purchaseLine($products['aqua-600'], null, 72, 3400),
                    $this->purchaseLine($products['teh-botol'], null, 48, 4300),
                    $this->purchaseLine($products['kapal-api'], null, 36, 12800),
                ],
                notes: 'Restock mingguan untuk kebutuhan outlet Jakarta dan transfer cabang.',
            ),
            'po-002' => $this->createPurchaseOrder(
                tenantId: $tenantId,
                branchId: $branches['main']->id,
                supplierId: $suppliers['unilever']->id,
                createdBy: $users['purchasing']->id,
                approvedBy: $users['owner']->id,
                purchaseNo: 'PO-MAK-202603-002',
                orderDate: Carbon::parse('2026-03-24'),
                expectedDate: Carbon::parse('2026-03-26'),
                status: 'received',
                paymentStatus: 'partial',
                shippingAmount: 185000,
                paymentMethod: 'giro',
                paymentAmount: 1800000,
                items: [
                    $this->purchaseLine($products['pepsodent'], null, 24, 11500),
                    $this->purchaseLine($products['sunlight'], null, 24, 18500),
                    $this->purchaseLine($products['roma'], null, 48, 8200),
                    $this->purchaseLine($products['sari-roti'], null, 36, 14200),
                    $this->purchaseLine($products['bear-brand'], null, 48, 9200),
                    $this->purchaseLine($products['beras-ramos'], $variants['beras-5kg'], 30, 68000),
                    $this->purchaseLine($products['beras-ramos'], $variants['beras-10kg'], 20, 132000),
                    $this->purchaseLine($products['sirup-marjan'], $variants['marjan-460'], 24, 18000),
                    $this->purchaseLine($products['sirup-marjan'], $variants['marjan-1l'], 12, 32000),
                ],
                notes: 'Pengadaan FMCG dan sembako untuk akhir pekan.',
            ),
        ];
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $products
     * @param  array<string, object>  $variants
     */
    private function seedTransfers(
        string $tenantId,
        array $branches,
        array $users,
        array $products,
        array $variants,
    ): void {
        $this->createTransfer(
            tenantId: $tenantId,
            fromBranchId: $branches['main']->id,
            toBranchId: $branches['bandung']->id,
            requestedBy: $users['inventory']->id,
            approvedBy: $users['manager_jakarta']->id,
            transferNo: 'TRF-MAK-202603-001',
            sentAt: Carbon::parse('2026-03-25 09:30:00'),
            receivedAt: Carbon::parse('2026-03-26 14:15:00'),
            status: 'received',
            items: [
                $this->transferLine($products['aqua-600'], null, 12),
                $this->transferLine($products['teh-botol'], null, 8),
                $this->transferLine($products['pepsodent'], null, 6),
                $this->transferLine($products['sari-roti'], null, 10),
                $this->transferLine($products['beras-ramos'], $variants['beras-5kg'], 6),
            ],
            notes: 'Replenishment untuk outlet Bandung sebelum akhir pekan.',
        );

        $this->createTransfer(
            tenantId: $tenantId,
            fromBranchId: $branches['main']->id,
            toBranchId: $branches['surabaya']->id,
            requestedBy: $users['inventory']->id,
            approvedBy: $users['manager_jakarta']->id,
            transferNo: 'TRF-MAK-202603-002',
            sentAt: Carbon::parse('2026-03-26 08:45:00'),
            receivedAt: Carbon::parse('2026-03-27 11:00:00'),
            status: 'received',
            items: [
                $this->transferLine($products['indomie-goreng'], null, 20),
                $this->transferLine($products['aqua-600'], null, 12),
                $this->transferLine($products['sunlight'], null, 8),
                $this->transferLine($products['sirup-marjan'], $variants['marjan-460'], 6),
                $this->transferLine($products['bear-brand'], null, 12),
            ],
            notes: 'Pengiriman stok reguler untuk outlet Surabaya.',
        );
    }

    private function seedCashSession(string $tenantId, object $branch, object $cashier): object
    {
        $session = $this->upsertRecord('cash_register_sessions', [
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'user_id' => $cashier->id,
            'opened_at' => Carbon::parse('2026-03-28 07:55:00'),
        ], [
            'opening_balance' => 1500000,
            'status' => 'open',
            'notes' => 'Shift pagi kasir utama.',
            'total_cash_sales' => 0,
            'total_non_cash_sales' => 0,
        ]);

        $this->upsertRecord('cash_adjustments', [
            'tenant_id' => $tenantId,
            'cash_register_session_id' => $session->id,
            'type' => 'cash_in',
            'reason' => 'Tambahan uang kecil untuk kembalian',
            'amount' => 200000,
        ], [
            'branch_id' => $branch->id,
            'user_id' => $cashier->id,
            'notes' => 'Pecahan Rp2.000 dan Rp5.000 untuk shift pagi.',
        ]);

        return $session;
    }

    private function closeCashSession(string $sessionId): void
    {
        $session = DB::table('cash_register_sessions')->where('id', $sessionId)->first();

        if (! $session) {
            return;
        }

        $openingBalance = (float) $session->opening_balance;
        $cashSales = (float) $session->total_cash_sales;
        $expectedCash = $openingBalance + $cashSales + 200000;

        DB::table('cash_register_sessions')
            ->where('id', $sessionId)
            ->update([
                'closed_at' => Carbon::parse('2026-03-28 21:05:00'),
                'expected_cash' => $expectedCash,
                'total_cash_submitted' => $expectedCash - 15000,
                'closing_balance' => $expectedCash - 15000,
                'status' => 'closed',
                'updated_at' => $this->now,
            ]);
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $customers
     * @param  array<string, object>  $products
     * @param  array<string, object>  $variants
     * @param  array<string, object>  $vouchers
     */
    private function seedSales(
        string $tenantId,
        array $branches,
        array $users,
        array $customers,
        array $products,
        array $variants,
        array $vouchers,
        object $cashSession,
    ): void {
        $this->createSale(
            tenantId: $tenantId,
            branchId: $branches['main']->id,
            customerId: $customers['rina']->id,
            saleNo: 'SAL-MAK-202603-001',
            saleDate: Carbon::parse('2026-03-28 10:12:00'),
            paymentMethod: 'cash',
            createdBy: $users['cashier']->id,
            cashRegisterSessionId: $cashSession->id,
            voucherId: $vouchers['ramadan10']->id,
            discountAmount: 10000,
            notes: 'Belanja mingguan pelanggan loyal.',
            lines: [
                $this->saleLine($products['aqua-600'], null, 2, 4500),
                $this->saleLine($products['indomie-goreng'], null, 5, 3500),
                $this->saleLine($products['sunlight'], null, 1, 23000),
                $this->saleLine($products['kapal-api'], null, 1, 16000),
            ],
        );

        $this->createSale(
            tenantId: $tenantId,
            branchId: $branches['bandung']->id,
            customerId: $customers['budi']->id,
            saleNo: 'SAL-MAK-202603-002',
            saleDate: Carbon::parse('2026-03-28 12:45:00'),
            paymentMethod: 'qris',
            createdBy: $users['manager_bandung']->id,
            cashRegisterSessionId: null,
            voucherId: null,
            discountAmount: 0,
            notes: 'Pembelian kebutuhan rumah tangga outlet Bandung.',
            lines: [
                $this->saleLine($products['beras-ramos'], $variants['beras-5kg'], 1, 75000),
                $this->saleLine($products['teh-botol'], null, 2, 6000),
                $this->saleLine($products['sari-roti'], null, 2, 18500),
            ],
        );

        $this->createSale(
            tenantId: $tenantId,
            branchId: $branches['surabaya']->id,
            customerId: $customers['dewi']->id,
            saleNo: 'SAL-MAK-202603-003',
            saleDate: Carbon::parse('2026-03-28 17:20:00'),
            paymentMethod: 'debit',
            createdBy: $users['omnichannel']->id,
            cashRegisterSessionId: null,
            voucherId: null,
            discountAmount: 0,
            notes: 'Belanja sore pelanggan member gold.',
            lines: [
                $this->saleLine($products['bear-brand'], null, 4, 12500),
                $this->saleLine($products['sirup-marjan'], $variants['marjan-460'], 1, 22000),
                $this->saleLine($products['indomie-goreng'], null, 4, 4500),
            ],
        );

        $this->createSale(
            tenantId: $tenantId,
            branchId: $branches['main']->id,
            customerId: $customers['sinar-boga']->id,
            saleNo: 'SAL-MAK-202603-004',
            saleDate: Carbon::parse('2026-03-29 09:10:00'),
            paymentMethod: 'bank_transfer',
            createdBy: $users['manager_jakarta']->id,
            cashRegisterSessionId: null,
            voucherId: null,
            discountAmount: 15000,
            notes: 'Pesanan pantry bulanan klien korporat.',
            lines: [
                $this->saleLine($products['aqua-600'], null, 12, 4500),
                $this->saleLine($products['bear-brand'], null, 12, 11500),
                $this->saleLine($products['sirup-marjan'], $variants['marjan-1l'], 4, 36000),
            ],
        );
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $products
     * @param  array<string, object>  $variants
     */
    private function seedStockAdjustments(
        string $tenantId,
        array $branches,
        array $users,
        array $products,
        array $variants,
    ): void {
        $adjustment = $this->upsertRecord('stock_adjustments', [
            'tenant_id' => $tenantId,
            'adjustment_no' => 'ADJ-MAK-202603-001',
        ], [
            'branch_id' => $branches['main']->id,
            'reason' => 'Stok opname akhir bulan',
            'status' => 'completed',
            'notes' => 'Penyesuaian kerusakan minor dan selisih hitung fisik.',
            'performed_by' => $users['inventory']->id,
            'approved_by' => $users['manager_jakarta']->id,
        ]);

        $this->recordAdjustmentLine($tenantId, $branches['main']->id, $adjustment->id, $users['inventory']->id, $products['roma'], null, -2, 'Kemasan rusak di rak display');
        $this->recordAdjustmentLine($tenantId, $branches['main']->id, $adjustment->id, $users['inventory']->id, $products['aqua-600'], null, -1, 'Botol penyok saat bongkar muat');
        $this->recordAdjustmentLine($tenantId, $branches['main']->id, $adjustment->id, $users['inventory']->id, $products['beras-ramos'], $variants['beras-5kg'], 1, 'Tambahan hasil recount stok gudang');
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $expenseCategories
     */
    private function seedExpenses(string $tenantId, array $branches, array $users, array $expenseCategories): void
    {
        $this->upsertRecord('expenses', ['tenant_id' => $tenantId, 'expense_no' => 'EXP-MAK-202603-001'], [
            'branch_id' => $branches['main']->id,
            'expense_category_id' => $expenseCategories['utilities']->id,
            'date' => '2026-03-27',
            'amount' => 1850000,
            'notes' => 'Tagihan listrik dan internet outlet Jakarta.',
            'created_by' => $users['manager_jakarta']->id,
        ]);

        $this->upsertRecord('expenses', ['tenant_id' => $tenantId, 'expense_no' => 'EXP-MAK-202603-002'], [
            'branch_id' => $branches['bandung']->id,
            'expense_category_id' => $expenseCategories['operations']->id,
            'date' => '2026-03-28',
            'amount' => 285000,
            'notes' => 'Pembelian kantong plastik, label harga, dan alat kebersihan.',
            'created_by' => $users['manager_bandung']->id,
        ]);

        $this->upsertRecord('expenses', ['tenant_id' => $tenantId, 'expense_no' => 'EXP-MAK-202603-003'], [
            'branch_id' => $branches['main']->id,
            'expense_category_id' => $expenseCategories['marketing']->id,
            'date' => '2026-03-29',
            'amount' => 750000,
            'notes' => 'Iklan Instagram untuk promo paket sembako akhir pekan.',
            'created_by' => $users['owner']->id,
        ]);
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $customers
     * @param  array<string, object>  $leadSources
     * @param  array<string, object>  $leadStages
     * @param  array<string, object>  $products
     * @param  array<string, object>  $variants
     */
    private function seedCrm(
        string $tenantId,
        array $branches,
        array $users,
        array $customers,
        array $leadSources,
        array $leadStages,
        array $products,
        array $variants,
    ): void {
        $leadHotel = $this->upsertRecord('leads', ['tenant_id' => $tenantId, 'lead_no' => 'LD-MAK-202603-001'], [
            'lead_source_id' => $leadSources['referral']->id,
            'lead_stage_id' => $leadStages['won']->id,
            'branch_id' => $branches['main']->id,
            'name' => 'Hotel Melati Semarang',
            'company' => 'Hotel Melati Semarang',
            'email' => 'purchasing@hotelmelati.id',
            'phone' => '024-8654-9910',
            'address' => 'Jl. Pandanaran No. 88, Semarang',
            'status' => 'converted',
            'assigned_to' => $users['crm']->id,
            'converted_at' => Carbon::parse('2026-03-27 15:30:00'),
            'converted_customer_id' => $customers['hotel-melati']->id,
        ]);

        $leadKantin = $this->upsertRecord('leads', ['tenant_id' => $tenantId, 'lead_no' => 'LD-MAK-202603-002'], [
            'lead_source_id' => $leadSources['instagram']->id,
            'lead_stage_id' => $leadStages['negotiation']->id,
            'branch_id' => $branches['bandung']->id,
            'name' => 'Kantin Karya Mandiri',
            'company' => 'Kantin Karya Mandiri',
            'email' => 'owner@kantinkarya.id',
            'phone' => '0812-7788-9900',
            'address' => 'Jl. Ciumbuleuit No. 54, Bandung',
            'status' => 'qualified',
            'assigned_to' => $users['crm']->id,
        ]);

        $leadCorporate = $this->upsertRecord('leads', ['tenant_id' => $tenantId, 'lead_no' => 'LD-MAK-202603-003'], [
            'lead_source_id' => $leadSources['website']->id,
            'lead_stage_id' => $leadStages['contacted']->id,
            'branch_id' => $branches['main']->id,
            'name' => 'CV Surya Kencana',
            'company' => 'CV Surya Kencana',
            'email' => 'procurement@svkencana.co.id',
            'phone' => '021-9988-1110',
            'address' => 'Jl. Warung Buncit Raya No. 77, Jakarta Selatan',
            'status' => 'contacted',
            'assigned_to' => $users['crm']->id,
        ]);

        $acceptedProposal = $this->createProposal(
            tenantId: $tenantId,
            branchId: $branches['main']->id,
            customerId: $customers['hotel-melati']->id,
            leadId: $leadHotel->id,
            createdBy: $users['crm']->id,
            proposalNo: 'PRP-MAK-202603-001',
            proposalDate: Carbon::parse('2026-03-26'),
            validUntil: Carbon::parse('2026-04-05'),
            status: 'accepted',
            notes: 'Proposal paket kebutuhan sarapan dan mini bar hotel.',
            items: [
                $this->proposalLine($products['aqua-600'], null, 'Aqua Air Mineral 600 ml', 24, 4500),
                $this->proposalLine($products['sirup-marjan'], $variants['marjan-1l'], 'Sirup Marjan Cocopandan 1 Liter', 6, 36000),
                $this->proposalLine($products['bear-brand'], null, 'Bear Brand Susu Steril 189 ml', 24, 11500),
            ],
        );

        $draftProposal = $this->createProposal(
            tenantId: $tenantId,
            branchId: $branches['bandung']->id,
            customerId: null,
            leadId: $leadKantin->id,
            createdBy: $users['crm']->id,
            proposalNo: 'PRP-MAK-202603-002',
            proposalDate: Carbon::parse('2026-03-29'),
            validUntil: Carbon::parse('2026-04-12'),
            status: 'draft',
            notes: 'Draft penawaran snack dan minuman untuk kantin pabrik.',
            items: [
                $this->proposalLine($products['indomie-goreng'], null, 'Indomie Mi Goreng 85 g', 120, 3400),
                $this->proposalLine($products['teh-botol'], null, 'Teh Botol Sosro 450 ml', 60, 5400),
                $this->proposalLine($products['roma'], null, 'Roma Kelapa 300 g', 40, 10250),
            ],
        );

        $this->createFollowUp(
            tenantId: $tenantId,
            followableType: 'App\\Models\\Lead',
            followableId: $leadHotel->id,
            type: 'call',
            scheduledAt: Carbon::parse('2026-03-27 10:00:00'),
            completedAt: Carbon::parse('2026-03-27 10:25:00'),
            status: 'completed',
            performedBy: $users['crm']->id,
            notes: 'Konfirmasi proposal diterima dan jadwal pengiriman awal April.',
        );

        $this->createFollowUp(
            tenantId: $tenantId,
            followableType: 'App\\Models\\Lead',
            followableId: $leadKantin->id,
            type: 'visit',
            scheduledAt: Carbon::parse('2026-04-01 14:00:00'),
            completedAt: null,
            status: 'pending',
            performedBy: $users['crm']->id,
            notes: 'Site visit untuk cek volume kebutuhan mingguan kantin.',
        );

        $this->createFollowUp(
            tenantId: $tenantId,
            followableType: 'App\\Models\\Customer',
            followableId: $customers['sinar-boga']->id,
            type: 'whatsapp',
            scheduledAt: Carbon::parse('2026-04-03 09:00:00'),
            completedAt: null,
            status: 'pending',
            performedBy: $users['crm']->id,
            notes: 'Reminder re-order pantry mingguan dan cek kebutuhan event internal.',
            recurring: [
                'is_recurring' => true,
                'recurrence_type' => 'weekly',
                'recurrence_interval' => 1,
                'recurrence_end_date' => '2026-05-29',
                'reminder_minutes_before' => 60,
            ],
        );

        $this->upsertRecord('customer_timelines', [
            'tenant_id' => $tenantId,
            'customer_id' => $customers['hotel-melati']->id,
            'reference_id' => $acceptedProposal->id,
            'event_type' => 'proposal_accepted',
        ], [
            'reference_type' => 'App\\Models\\Proposal',
            'description' => 'Proposal paket mini bar dan sarapan disetujui oleh Hotel Melati Semarang.',
            'meta' => [
                'proposal_no' => $acceptedProposal->proposal_no,
                'total_amount' => (float) $acceptedProposal->total_amount,
            ],
        ]);

        $this->upsertRecord('customer_timelines', [
            'tenant_id' => $tenantId,
            'customer_id' => $customers['sinar-boga']->id,
            'reference_id' => $draftProposal->id,
            'event_type' => 'proposal_draft_created',
        ], [
            'reference_type' => 'App\\Models\\Proposal',
            'description' => 'Draft proposal untuk pelanggan korporat disiapkan oleh tim CRM.',
            'meta' => [
                'proposal_no' => $draftProposal->proposal_no,
                'status' => $draftProposal->status,
            ],
        ]);

        $this->upsertRecord('customer_timelines', [
            'tenant_id' => $tenantId,
            'customer_id' => $customers['hotel-melati']->id,
            'reference_id' => $leadHotel->id,
            'event_type' => 'lead_converted',
        ], [
            'reference_type' => 'App\\Models\\Lead',
            'description' => 'Lead referral berhasil dikonversi menjadi pelanggan korporat.',
            'meta' => [
                'lead_no' => $leadHotel->lead_no,
                'assigned_to' => $users['crm']->id,
            ],
        ]);

        $this->upsertRecord('customer_timelines', [
            'tenant_id' => $tenantId,
            'customer_id' => $customers['sinar-boga']->id,
            'reference_id' => $leadCorporate->id,
            'event_type' => 'follow_up_scheduled',
        ], [
            'reference_type' => 'App\\Models\\Lead',
            'description' => 'Tindak lanjut penawaran pantry bulanan dijadwalkan oleh tim CRM.',
            'meta' => [
                'lead_no' => $leadCorporate->lead_no,
                'status' => $leadCorporate->status,
            ],
        ]);
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $products
     * @param  array<string, object>  $variants
     */
    private function seedMarketplace(
        string $tenantId,
        array $branches,
        array $users,
        array $products,
        array $variants,
    ): void {
        $tokopediaAccount = $this->upsertRecord('marketplace_accounts', [
            'tenant_id' => $tenantId,
            'marketplace' => 'tokopedia',
            'name' => 'Tokopedia Makmur Official',
        ], [
            'status' => 'active',
            'external_account_id' => 'tokopedia-makmur-official',
            'api_key' => 'demo_tokopedia_key',
            'api_secret' => 'demo_tokopedia_secret',
            'created_by' => $users['omnichannel']->id,
            'meta' => [
                'store_tier' => 'Power Merchant',
                'warehouse_city' => 'Jakarta Selatan',
            ],
        ]);

        $shopeeAccount = $this->upsertRecord('marketplace_accounts', [
            'tenant_id' => $tenantId,
            'marketplace' => 'shopee',
            'name' => 'Shopee Makmur Surabaya',
        ], [
            'status' => 'active',
            'external_account_id' => 'shopee-makmur-surabaya',
            'api_key' => 'demo_shopee_key',
            'api_secret' => 'demo_shopee_secret',
            'created_by' => $users['omnichannel']->id,
            'meta' => [
                'store_tier' => 'Star Seller',
                'warehouse_city' => 'Surabaya',
            ],
        ]);

        $tokopediaShop = $this->upsertRecord('marketplace_shops', [
            'tenant_id' => $tenantId,
            'external_shop_id' => 'TKP-1001',
        ], [
            'marketplace_account_id' => $tokopediaAccount->id,
            'branch_id' => $branches['main']->id,
            'marketplace' => 'tokopedia',
            'name' => 'Makmur Official Store',
            'region_code' => 'ID-JK',
            'status' => 'active',
            'settings' => ['pickup_time' => '2h', 'couriers' => ['instant', 'same_day', 'reguler']],
        ]);

        $shopeeShop = $this->upsertRecord('marketplace_shops', [
            'tenant_id' => $tenantId,
            'external_shop_id' => 'SHP-2001',
        ], [
            'marketplace_account_id' => $shopeeAccount->id,
            'branch_id' => $branches['surabaya']->id,
            'marketplace' => 'shopee',
            'name' => 'Makmur Surabaya Official',
            'region_code' => 'ID-JI',
            'status' => 'active',
            'settings' => ['pickup_time' => '3h', 'couriers' => ['hemat', 'reguler']],
        ]);

        $maps = [
            'tokopedia-aqua' => $this->upsertRecord('marketplace_product_maps', [
                'tenant_id' => $tenantId,
                'marketplace' => 'tokopedia',
                'external_product_id' => 'TKP-PROD-6001',
            ], [
                'marketplace_shop_id' => $tokopediaShop->id,
                'product_id' => $products['aqua-600']->id,
                'external_sku' => 'TKP-AQUA-600',
                'external_name' => 'Aqua Air Mineral 600 ml',
                'sync_price' => true,
                'sync_stock' => true,
                'is_active' => true,
                'last_sync_at' => Carbon::parse('2026-03-29 07:10:00'),
                'last_sync_status' => 'success',
                'meta' => ['listing_status' => 'active'],
            ]),
            'tokopedia-indomie' => $this->upsertRecord('marketplace_product_maps', [
                'tenant_id' => $tenantId,
                'marketplace' => 'tokopedia',
                'external_product_id' => 'TKP-PROD-6002',
            ], [
                'marketplace_shop_id' => $tokopediaShop->id,
                'product_id' => $products['indomie-goreng']->id,
                'external_sku' => 'TKP-INDM-GRG',
                'external_name' => 'Indomie Mi Goreng 85 g',
                'sync_price' => true,
                'sync_stock' => true,
                'is_active' => true,
                'last_sync_at' => Carbon::parse('2026-03-29 07:12:00'),
                'last_sync_status' => 'success',
            ]),
            'shopee-bear' => $this->upsertRecord('marketplace_product_maps', [
                'tenant_id' => $tenantId,
                'marketplace' => 'shopee',
                'external_product_id' => 'SHP-PROD-7001',
            ], [
                'marketplace_shop_id' => $shopeeShop->id,
                'product_id' => $products['bear-brand']->id,
                'external_sku' => 'SHP-BEAR-189',
                'external_name' => 'Bear Brand Susu Steril 189 ml',
                'sync_price' => true,
                'sync_stock' => true,
                'is_active' => true,
                'last_sync_at' => Carbon::parse('2026-03-29 07:15:00'),
                'last_sync_status' => 'success',
            ]),
            'shopee-marjan' => $this->upsertRecord('marketplace_product_maps', [
                'tenant_id' => $tenantId,
                'marketplace' => 'shopee',
                'external_product_id' => 'SHP-PROD-7002',
            ], [
                'marketplace_shop_id' => $shopeeShop->id,
                'product_id' => $products['sirup-marjan']->id,
                'product_variant_id' => $variants['marjan-460']->id,
                'external_sku' => 'SHP-MRJ-460',
                'external_name' => 'Sirup Marjan Cocopandan 460 ml',
                'sync_price' => true,
                'sync_stock' => true,
                'is_active' => true,
                'last_sync_at' => Carbon::parse('2026-03-29 07:16:00'),
                'last_sync_status' => 'success',
            ]),
        ];

        $tokopediaOrder = $this->upsertRecord('marketplace_orders', [
            'tenant_id' => $tenantId,
            'marketplace' => 'tokopedia',
            'external_order_id' => 'TKP-INV-33001',
        ], [
            'marketplace_shop_id' => $tokopediaShop->id,
            'branch_id' => $branches['main']->id,
            'external_order_no' => 'TKP/33001/03/2026',
            'buyer_name' => 'Fajar Pratama',
            'buyer_phone' => '0812-7000-5500',
            'order_date' => Carbon::parse('2026-03-29 08:20:00'),
            'status' => 'completed',
            'subtotal' => 56000,
            'shipping_amount' => 12000,
            'discount_amount' => 5000,
            'grand_total' => 63000,
            'imported_at' => Carbon::parse('2026-03-29 08:35:00'),
            'raw_data' => ['courier' => 'same_day', 'channel' => 'tokopedia'],
        ]);

        $this->upsertRecord('marketplace_order_items', [
            'tenant_id' => $tenantId,
            'marketplace_order_id' => $tokopediaOrder->id,
            'external_item_id' => 'TKP-ITEM-33001-A',
        ], [
            'marketplace_product_map_id' => $maps['tokopedia-aqua']->id,
            'product_id' => $products['aqua-600']->id,
            'name_snapshot' => 'Aqua Air Mineral 600 ml',
            'external_sku' => 'TKP-AQUA-600',
            'qty' => 6,
            'unit_price' => 4500,
            'line_total' => 27000,
            'raw_data' => ['warehouse' => 'main'],
        ]);

        $this->upsertRecord('marketplace_order_items', [
            'tenant_id' => $tenantId,
            'marketplace_order_id' => $tokopediaOrder->id,
            'external_item_id' => 'TKP-ITEM-33001-B',
        ], [
            'marketplace_product_map_id' => $maps['tokopedia-indomie']->id,
            'product_id' => $products['indomie-goreng']->id,
            'name_snapshot' => 'Indomie Mi Goreng 85 g',
            'external_sku' => 'TKP-INDM-GRG',
            'qty' => 8,
            'unit_price' => 3625,
            'line_total' => 29000,
            'raw_data' => ['warehouse' => 'main'],
        ]);

        $shopeeOrder = $this->upsertRecord('marketplace_orders', [
            'tenant_id' => $tenantId,
            'marketplace' => 'shopee',
            'external_order_id' => 'SHP-INV-55001',
        ], [
            'marketplace_shop_id' => $shopeeShop->id,
            'branch_id' => $branches['surabaya']->id,
            'external_order_no' => 'SHP/55001/03/2026',
            'buyer_name' => 'Nadia Putri',
            'buyer_phone' => '0812-6600-7788',
            'order_date' => Carbon::parse('2026-03-29 11:45:00'),
            'status' => 'processing',
            'subtotal' => 128000,
            'shipping_amount' => 15000,
            'discount_amount' => 8000,
            'grand_total' => 135000,
            'imported_at' => Carbon::parse('2026-03-29 11:55:00'),
            'raw_data' => ['courier' => 'hemat', 'channel' => 'shopee'],
        ]);

        $this->upsertRecord('marketplace_order_items', [
            'tenant_id' => $tenantId,
            'marketplace_order_id' => $shopeeOrder->id,
            'external_item_id' => 'SHP-ITEM-55001-A',
        ], [
            'marketplace_product_map_id' => $maps['shopee-bear']->id,
            'product_id' => $products['bear-brand']->id,
            'name_snapshot' => 'Bear Brand Susu Steril 189 ml',
            'external_sku' => 'SHP-BEAR-189',
            'qty' => 6,
            'unit_price' => 12000,
            'line_total' => 72000,
            'raw_data' => ['promo_label' => 'star_seller'],
        ]);

        $this->upsertRecord('marketplace_order_items', [
            'tenant_id' => $tenantId,
            'marketplace_order_id' => $shopeeOrder->id,
            'external_item_id' => 'SHP-ITEM-55001-B',
        ], [
            'marketplace_product_map_id' => $maps['shopee-marjan']->id,
            'product_id' => $products['sirup-marjan']->id,
            'product_variant_id' => $variants['marjan-460']->id,
            'name_snapshot' => 'Sirup Marjan Cocopandan 460 ml',
            'external_sku' => 'SHP-MRJ-460',
            'external_variant_id' => 'SHP-VAR-MRJ-460',
            'qty' => 3,
            'unit_price' => 21000,
            'line_total' => 63000,
            'raw_data' => ['promo_label' => 'free_shipping'],
        ]);

        $this->upsertRecord('marketplace_sync_logs', [
            'tenant_id' => $tenantId,
            'marketplace' => 'tokopedia',
            'sync_type' => 'orders',
            'status' => 'success',
            'created_at' => Carbon::parse('2026-03-29 08:35:00'),
        ], [
            'branch_id' => $branches['main']->id,
            'marketplace_shop_id' => $tokopediaShop->id,
            'direction' => 'inbound',
            'entity_type' => 'order',
            'entity_id' => $tokopediaOrder->id,
            'external_entity_id' => $tokopediaOrder->external_order_id,
            'payload' => ['imported_orders' => 1],
            'synced_at' => Carbon::parse('2026-03-29 08:35:00'),
        ]);

        $this->upsertRecord('marketplace_sync_logs', [
            'tenant_id' => $tenantId,
            'marketplace' => 'shopee',
            'sync_type' => 'stock',
            'status' => 'pending',
            'created_at' => Carbon::parse('2026-03-29 12:10:00'),
        ], [
            'branch_id' => $branches['surabaya']->id,
            'marketplace_shop_id' => $shopeeShop->id,
            'direction' => 'outbound',
            'entity_type' => 'inventory',
            'payload' => ['queued_items' => 4],
        ]);

        $this->upsertRecord('marketplace_sync_logs', [
            'tenant_id' => $tenantId,
            'marketplace' => 'shopee',
            'sync_type' => 'pricing',
            'status' => 'failed',
            'created_at' => Carbon::parse('2026-03-29 12:15:00'),
        ], [
            'branch_id' => $branches['surabaya']->id,
            'marketplace_shop_id' => $shopeeShop->id,
            'direction' => 'outbound',
            'entity_type' => 'product',
            'external_entity_id' => 'SHP-PROD-7002',
            'error_message' => 'Rate limit dari marketplace, akan dicoba ulang otomatis.',
            'request_payload' => ['sku' => 'SHP-MRJ-460'],
            'response_payload' => ['code' => 'RATE_LIMIT'],
        ]);
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     * @param  array<string, object>  $purchases
     */
    private function seedAuditTrail(string $tenantId, array $branches, array $users, array $purchases): void
    {
        $this->upsertRecord('audit_logs', [
            'tenant_id' => $tenantId,
            'event' => 'seed.demo.completed',
            'auditable_type' => 'DatabaseSeeder',
            'auditable_id' => null,
            'created_at' => Carbon::parse('2026-03-29 18:00:00'),
        ], [
            'branch_id' => $branches['main']->id,
            'user_id' => $users['admin']->id,
            'new_values' => ['tenant_code' => 'MAK', 'modules' => ['sales', 'purchase', 'crm', 'marketplace']],
            'url' => '/artisan/db:seed',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Artisan Seeder',
            'tags' => 'seeder,demo',
            'meta' => ['seed' => 'RealisticDemoSeeder'],
        ]);

        $this->upsertRecord('audit_logs', [
            'tenant_id' => $tenantId,
            'event' => 'purchase.received',
            'auditable_type' => PurchaseOrder::class,
            'auditable_id' => $purchases['po-002']->id,
            'created_at' => Carbon::parse('2026-03-26 16:45:00'),
        ], [
            'branch_id' => $branches['main']->id,
            'user_id' => $users['purchasing']->id,
            'new_values' => ['purchase_no' => $purchases['po-002']->purchase_no, 'status' => 'received'],
            'url' => '/purchases/'.$purchases['po-002']->id,
            'ip_address' => '10.10.10.24',
            'user_agent' => 'Mozilla/5.0 Demo Purchase Client',
            'tags' => 'purchase,inventory',
        ]);
    }

    /**
     * @param  array<string, object>  $branches
     * @param  array<string, object>  $users
     */
    private function seedNotifications(string $tenantId, array $branches, array $users): void
    {
        $this->upsertRecord('notifications_log', [
            'tenant_id' => $tenantId,
            'type' => 'stock.alert',
            'channel' => 'database',
            'title' => 'Stok Aqua Surabaya mendekati buffer minimum',
        ], [
            'branch_id' => $branches['surabaya']->id,
            'user_id' => $users['inventory']->id,
            'message' => 'Periksa buffer stok Aqua 600 ml sebelum promo akhir pekan dimulai.',
            'payload' => ['sku' => 'MAK-AQUA-600', 'recommended_restock' => 24],
            'status' => 'pending',
        ]);

        $this->upsertRecord('notifications_log', [
            'tenant_id' => $tenantId,
            'type' => 'crm.follow_up',
            'channel' => 'whatsapp',
            'title' => 'Reminder follow-up Kantin Karya Mandiri',
        ], [
            'branch_id' => $branches['bandung']->id,
            'user_id' => $users['crm']->id,
            'message' => 'Kunjungan follow-up dijadwalkan 1 April 2026 pukul 14:00.',
            'payload' => ['lead_no' => 'LD-MAK-202603-002'],
            'status' => 'pending',
        ]);

        $this->upsertRecord('notifications_log', [
            'tenant_id' => $tenantId,
            'type' => 'marketplace.sync',
            'channel' => 'email',
            'title' => 'Sinkronisasi marketplace Tokopedia berhasil',
        ], [
            'branch_id' => $branches['main']->id,
            'user_id' => $users['omnichannel']->id,
            'message' => '1 order baru berhasil diimpor dari Tokopedia.',
            'payload' => ['marketplace' => 'tokopedia', 'orders' => 1],
            'status' => 'sent',
            'sent_at' => Carbon::parse('2026-03-29 08:36:00'),
        ]);
    }

    private function createPurchaseOrder(
        string $tenantId,
        string $branchId,
        string $supplierId,
        string $createdBy,
        string $approvedBy,
        string $purchaseNo,
        Carbon $orderDate,
        ?Carbon $expectedDate,
        string $status,
        string $paymentStatus,
        float $shippingAmount,
        string $paymentMethod,
        ?float $paymentAmount,
        array $items,
        string $notes,
    ): object {
        $subtotal = collect($items)->sum('line_total');
        $discountAmount = collect($items)->sum('discount_amount');
        $taxAmount = collect($items)->sum('tax_amount');
        $grandTotal = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

        $purchaseOrder = $this->upsertRecord('purchase_orders', [
            'tenant_id' => $tenantId,
            'purchase_no' => $purchaseNo,
        ], [
            'branch_id' => $branchId,
            'supplier_id' => $supplierId,
            'order_date' => $orderDate,
            'expected_date' => $expectedDate,
            'status' => $status,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'grand_total' => $grandTotal,
            'payment_status' => $paymentStatus,
            'notes' => $notes,
            'created_by' => $createdBy,
            'approved_by' => $approvedBy,
        ]);

        foreach ($items as $index => $item) {
            $this->upsertRecord('purchase_order_items', [
                'tenant_id' => $tenantId,
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
            ], [
                'qty' => $item['qty'],
                'received_qty' => $item['qty'],
                'purchase_price' => $item['purchase_price'],
                'discount_amount' => $item['discount_amount'],
                'tax_amount' => $item['tax_amount'],
                'line_total' => $item['line_total'],
            ]);

            if (in_array($status, ['received', 'completed'], true)) {
                $this->stockService->recordMovement([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $purchaseOrder->id,
                    'movement_type' => 'purchase',
                    'qty' => $item['qty'],
                    'unit_cost' => $item['purchase_price'],
                    'performed_by' => $createdBy,
                    'notes' => 'Penerimaan barang '.$purchaseNo.' item #'.($index + 1),
                ]);
            }
        }

        $paidAmount = $paymentAmount ?? ($paymentStatus === 'paid' ? $grandTotal : null);

        if ($paidAmount !== null && $paidAmount > 0) {
            $this->upsertRecord('purchase_payments', [
                'tenant_id' => $tenantId,
                'purchase_order_id' => $purchaseOrder->id,
                'payment_no' => 'PAY-'.Str::after($purchaseNo, 'PO-'),
            ], [
                'branch_id' => $branchId,
                'payment_date' => $orderDate->copy()->addDay()->setTime(14, 0),
                'amount' => $paidAmount,
                'payment_method' => $paymentMethod,
                'reference_no' => 'REF-'.Str::after($purchaseNo, 'PO-'),
                'notes' => 'Pembayaran untuk '.$purchaseNo,
                'created_by' => $createdBy,
            ]);
        }

        return $purchaseOrder;
    }

    private function createTransfer(
        string $tenantId,
        string $fromBranchId,
        string $toBranchId,
        string $requestedBy,
        string $approvedBy,
        string $transferNo,
        Carbon $sentAt,
        ?Carbon $receivedAt,
        string $status,
        array $items,
        string $notes,
    ): object {
        $transfer = $this->upsertRecord('stock_transfers', [
            'tenant_id' => $tenantId,
            'transfer_no' => $transferNo,
        ], [
            'from_branch_id' => $fromBranchId,
            'to_branch_id' => $toBranchId,
            'status' => $status,
            'requested_by' => $requestedBy,
            'approved_by' => $approvedBy,
            'sent_at' => $sentAt,
            'received_at' => $receivedAt,
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            $this->upsertRecord('stock_transfer_items', [
                'tenant_id' => $tenantId,
                'stock_transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
            ], [
                'qty' => $item['qty'],
                'received_qty' => $item['qty'],
                'notes' => $notes,
            ]);

            $this->stockService->recordMovement([
                'tenant_id' => $tenantId,
                'branch_id' => $fromBranchId,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'reference_type' => StockTransfer::class,
                'reference_id' => $transfer->id,
                'movement_type' => 'transfer_out',
                'qty' => -1 * $item['qty'],
                'performed_by' => $requestedBy,
                'notes' => 'Transfer keluar '.$transferNo,
            ]);

            $this->stockService->recordMovement([
                'tenant_id' => $tenantId,
                'branch_id' => $toBranchId,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'reference_type' => StockTransfer::class,
                'reference_id' => $transfer->id,
                'movement_type' => 'transfer_in',
                'qty' => $item['qty'],
                'performed_by' => $approvedBy,
                'notes' => 'Transfer masuk '.$transferNo,
            ]);
        }

        return $transfer;
    }

    private function createSale(
        string $tenantId,
        string $branchId,
        ?string $customerId,
        string $saleNo,
        Carbon $saleDate,
        string $paymentMethod,
        string $createdBy,
        ?string $cashRegisterSessionId,
        ?string $voucherId,
        float $discountAmount,
        string $notes,
        array $lines,
    ): object {
        $subtotal = collect($lines)->sum('line_total');
        $taxAmount = collect($lines)->sum('tax_amount');
        $grandTotal = $subtotal - $discountAmount + $taxAmount;

        $sale = $this->upsertRecord('sales', [
            'tenant_id' => $tenantId,
            'sale_no' => $saleNo,
        ], [
            'branch_id' => $branchId,
            'cash_register_session_id' => $cashRegisterSessionId,
            'customer_id' => $customerId,
            'sale_date' => $saleDate,
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'paid_amount' => $grandTotal,
            'due_amount' => 0,
            'notes' => $notes,
            'created_by' => $createdBy,
        ]);

        foreach ($lines as $index => $line) {
            $this->upsertRecord('sale_items', [
                'tenant_id' => $tenantId,
                'sale_id' => $sale->id,
                'product_id' => $line['product_id'],
                'product_variant_id' => $line['product_variant_id'],
            ], [
                'qty' => $line['qty'],
                'unit_price' => $line['unit_price'],
                'discount_amount' => $line['discount_amount'],
                'tax_amount' => $line['tax_amount'],
                'line_total' => $line['line_total'],
            ]);

            $this->stockService->recordMovement([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'product_id' => $line['product_id'],
                'product_variant_id' => $line['product_variant_id'],
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'movement_type' => 'sale',
                'qty' => -1 * $line['qty'],
                'unit_cost' => $line['unit_cost'],
                'performed_by' => $createdBy,
                'notes' => 'Penjualan '.$saleNo.' item #'.($index + 1),
            ]);
        }

        $this->upsertRecord('sale_payments', [
            'tenant_id' => $tenantId,
            'sale_id' => $sale->id,
            'payment_no' => 'PAY-'.Str::after($saleNo, 'SAL-'),
        ], [
            'payment_date' => $saleDate,
            'amount' => $grandTotal,
            'payment_method' => $paymentMethod,
            'reference_no' => strtoupper($paymentMethod).'-'.Str::after($saleNo, 'SAL-'),
            'notes' => 'Pembayaran penjualan '.$saleNo,
            'created_by' => $createdBy,
        ]);

        if ($paymentMethod === 'cash' && $cashRegisterSessionId) {
            DB::table('cash_register_sessions')
                ->where('id', $cashRegisterSessionId)
                ->increment('total_cash_sales', $grandTotal);
        } elseif ($cashRegisterSessionId) {
            DB::table('cash_register_sessions')
                ->where('id', $cashRegisterSessionId)
                ->increment('total_non_cash_sales', $grandTotal);
        }

        if ($customerId !== null) {
            $customer = DB::table('customers')->where('id', $customerId)->first();

            if ($customer) {
                DB::table('customers')
                    ->where('id', $customerId)
                    ->update([
                        'total_spent' => (float) $customer->total_spent + $grandTotal,
                        'total_orders' => (int) $customer->total_orders + 1,
                        'last_purchase_date' => $saleDate,
                        'updated_at' => $this->now,
                    ]);
            }

            $saleModel = Sale::query()->findOrFail($sale->id);
            $this->loyaltyService->awardPointsForSale($saleModel);

            $this->upsertRecord('customer_timelines', [
                'tenant_id' => $tenantId,
                'customer_id' => $customerId,
                'reference_id' => $sale->id,
                'event_type' => 'sale_completed',
            ], [
                'reference_type' => Sale::class,
                'sale_id' => $sale->id,
                'description' => 'Transaksi '.$saleNo.' senilai Rp '.number_format($grandTotal, 0, ',', '.'),
                'meta' => [
                    'sale_no' => $saleNo,
                    'payment_method' => $paymentMethod,
                    'discount_amount' => $discountAmount,
                    'items' => collect($lines)->map(fn (array $line): array => [
                        'product_id' => $line['product_id'],
                        'product_variant_id' => $line['product_variant_id'],
                        'qty' => $line['qty'],
                        'unit_price' => $line['unit_price'],
                    ])->values()->all(),
                ],
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);
        }

        if ($voucherId !== null) {
            DB::table('vouchers')->where('id', $voucherId)->increment('used_count');
        }

        return $sale;
    }

    private function createProposal(
        string $tenantId,
        ?string $branchId,
        ?string $customerId,
        ?string $leadId,
        string $createdBy,
        string $proposalNo,
        Carbon $proposalDate,
        ?Carbon $validUntil,
        string $status,
        string $notes,
        array $items,
    ): object {
        $subtotal = collect($items)->sum('total');
        $discountAmount = collect($items)->sum('discount');
        $taxAmount = collect($items)->sum('tax');
        $totalAmount = $subtotal - $discountAmount + $taxAmount;

        $proposal = $this->upsertRecord('proposals', [
            'tenant_id' => $tenantId,
            'proposal_no' => $proposalNo,
        ], [
            'customer_id' => $customerId,
            'lead_id' => $leadId,
            'proposal_date' => $proposalDate,
            'valid_until' => $validUntil,
            'status' => $status,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'notes' => $notes,
            'created_by' => $createdBy,
            'branch_id' => $branchId,
        ]);

        foreach ($items as $index => $item) {
            $this->upsertRecord('proposal_items', [
                'proposal_id' => $proposal->id,
                'description' => $item['description'],
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
            ], [
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'],
                'tax' => $item['tax'],
                'total' => $item['total'],
                'notes' => 'Item proposal #'.($index + 1),
            ]);
        }

        return $proposal;
    }

    /**
     * @param  array<string, mixed>  $recurring
     */
    private function createFollowUp(
        string $tenantId,
        string $followableType,
        string $followableId,
        string $type,
        Carbon $scheduledAt,
        ?Carbon $completedAt,
        string $status,
        ?string $performedBy,
        string $notes,
        array $recurring = [],
    ): object {
        return $this->upsertRecord('follow_ups', [
            'tenant_id' => $tenantId,
            'followable_type' => $followableType,
            'followable_id' => $followableId,
            'type' => $type,
            'scheduled_at' => $scheduledAt,
        ], array_merge([
            'completed_at' => $completedAt,
            'notes' => $notes,
            'status' => $status,
            'performed_by' => $performedBy,
        ], $recurring));
    }

    private function recordAdjustmentLine(
        string $tenantId,
        string $branchId,
        string $adjustmentId,
        string $performedBy,
        object $product,
        ?object $variant,
        float $delta,
        string $notes,
    ): void {
        $inventory = DB::table('inventories')
            ->where('branch_id', $branchId)
            ->where('product_id', $product->id)
            ->when($variant !== null, fn ($query) => $query->where('product_variant_id', $variant->id), fn ($query) => $query->whereNull('product_variant_id'))
            ->first();

        $beforeQty = $inventory ? (float) $inventory->qty_on_hand : 0;
        $afterQty = $beforeQty + $delta;
        $unitCost = (float) ($variant->cost_price ?? $product->cost_price ?? 0);

        $this->upsertRecord('stock_adjustment_items', [
            'tenant_id' => $tenantId,
            'stock_adjustment_id' => $adjustmentId,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
        ], [
            'before_qty' => $beforeQty,
            'adjusted_qty' => $delta,
            'after_qty' => $afterQty,
            'unit_cost' => $unitCost,
            'notes' => $notes,
        ]);

        $this->stockService->recordMovement([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'reference_type' => StockAdjustment::class,
            'reference_id' => $adjustmentId,
            'movement_type' => 'adjustment',
            'qty' => $delta,
            'unit_cost' => $unitCost,
            'performed_by' => $performedBy,
            'notes' => $notes,
        ]);
    }

    private function purchaseLine(object $product, ?object $variant, float $qty, float $purchasePrice): array
    {
        return [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'qty' => $qty,
            'purchase_price' => $purchasePrice,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'line_total' => $qty * $purchasePrice,
        ];
    }

    private function transferLine(object $product, ?object $variant, float $qty): array
    {
        return [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'qty' => $qty,
        ];
    }

    private function saleLine(object $product, ?object $variant, float $qty, float $unitPrice): array
    {
        return [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'line_total' => $qty * $unitPrice,
            'unit_cost' => (float) ($variant->cost_price ?? $product->cost_price ?? 0),
        ];
    }

    private function proposalLine(object $product, ?object $variant, string $description, float $quantity, float $unitPrice): array
    {
        return [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => 0,
            'tax' => 0,
            'total' => $quantity * $unitPrice,
        ];
    }

    /**
     * @param  array<string, object>  $branches
     * @param  list<string>  $allowedBranches
     */
    private function syncBranchMemberships(
        string $tenantId,
        string $userId,
        array $branches,
        array $allowedBranches,
        string $defaultBranch,
    ): void {
        DB::table('branch_user')
            ->where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->delete();

        foreach ($allowedBranches as $branchKey) {
            $branch = $branches[$branchKey];

            $this->upsertRecord('branch_user', [
                'tenant_id' => $tenantId,
                'branch_id' => $branch->id,
                'user_id' => $userId,
            ], [
                'is_default' => $branchKey === $defaultBranch,
            ]);
        }
    }

    private function attachRole(string $userId, string $roleName): void
    {
        $roleId = DB::table('roles')->where('name', $roleName)->value('id');

        if (! $roleId) {
            return;
        }

        DB::table('model_has_roles')->insertOrIgnore([
            'role_id' => $roleId,
            'model_type' => 'App\\Models\\User',
            'model_id' => $userId,
        ]);
    }

    private function upsertRecord(string $table, array $uniqueBy, array $attributes): object
    {
        $record = $this->firstWhere($table, $uniqueBy);
        $payload = $this->normalizePayload($table, array_merge($uniqueBy, $attributes));

        if ($record) {
            unset($payload['id'], $payload['created_at']);

            if ($this->hasColumn($table, 'updated_at') && ! array_key_exists('updated_at', $payload)) {
                $payload['updated_at'] = $this->serializeValue($table, 'updated_at', $this->now);
            }

            DB::table($table)->where('id', $record->id)->update($payload);

            return DB::table($table)->where('id', $record->id)->first();
        }

        if ($this->hasColumn($table, 'id') && ! array_key_exists('id', $payload)) {
            $payload['id'] = (string) Str::uuid();
        }

        if ($this->hasColumn($table, 'created_at') && ! array_key_exists('created_at', $payload)) {
            $payload['created_at'] = $this->serializeValue($table, 'created_at', $this->now);
        }

        if ($this->hasColumn($table, 'updated_at') && ! array_key_exists('updated_at', $payload)) {
            $payload['updated_at'] = $this->serializeValue($table, 'updated_at', $this->now);
        }

        DB::table($table)->insert($payload);

        return DB::table($table)->where('id', $payload['id'])->first();
    }

    private function firstWhere(string $table, array $conditions): ?object
    {
        $query = DB::table($table);

        foreach ($conditions as $column => $value) {
            if ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $this->serializeValue($table, $column, $value));
            }
        }

        return $query->first();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function normalizePayload(string $table, array $attributes): array
    {
        $columns = $this->columnsForTable($table);
        $payload = [];

        foreach ($attributes as $column => $value) {
            if (! in_array($column, $columns, true)) {
                continue;
            }

            $payload[$column] = $this->serializeValue($table, $column, $value);
        }

        return $payload;
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    private function serializeValue(string $table, string $column, $value)
    {
        if ($value instanceof CarbonInterface) {
            $columnType = $this->columnType($table, $column);

            if ($columnType === 'date') {
                return $value->toDateString();
            }

            return $value->toDateTimeString();
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $columnType = $this->columnType($table, $column);

        if (in_array($columnType, ['json', 'jsonb'], true) && $value !== null) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $value;
    }

    /**
     * @return list<string>
     */
    private function columnsForTable(string $table): array
    {
        if (! array_key_exists($table, $this->tableColumns)) {
            $this->tableColumns[$table] = Schema::getColumnListing($table);
        }

        return $this->tableColumns[$table];
    }

    private function columnType(string $table, string $column): string
    {
        if (! array_key_exists($table, $this->tableColumnTypes)) {
            $this->tableColumnTypes[$table] = [];
        }

        if (! array_key_exists($column, $this->tableColumnTypes[$table])) {
            $this->tableColumnTypes[$table][$column] = Schema::getColumnType($table, $column);
        }

        return $this->tableColumnTypes[$table][$column];
    }

    private function hasColumn(string $table, string $column): bool
    {
        return in_array($column, $this->columnsForTable($table), true);
    }
}
