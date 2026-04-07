<?php

namespace Database\Seeders;

use App\Models\AccountCategory;
use App\Models\Tenant;
use App\Constants\AccountCategoryType;
use Illuminate\Database\Seeder;

class AccountCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Assets
            [
                'code' => '1100',
                'name' => 'Aset Lancar',
                'type' => AccountCategoryType::ASSET->value,
                'normal_balance' => 1,
                'description' => 'Aset yang dapat dicairkan dalam waktu kurang dari satu tahun',
                'sort_order' => 1,
            ],
            [
                'code' => '1200',
                'name' => 'Aset Tetap',
                'type' => AccountCategoryType::ASSET->value,
                'normal_balance' => 1,
                'description' => 'Aset jangka panjang yang digunakan dalam operasional',
                'sort_order' => 2,
            ],
            [
                'code' => '1300',
                'name' => 'Aset Lainnya',
                'type' => AccountCategoryType::ASSET->value,
                'normal_balance' => 1,
                'description' => 'Aset tidak lancar lainnya',
                'sort_order' => 3,
            ],

            // Liabilities
            [
                'code' => '2100',
                'name' => 'Kewajiban Jangka Pendek',
                'type' => AccountCategoryType::LIABILITY->value,
                'normal_balance' => -1,
                'description' => 'Utang yang jatuh tempo dalam satu tahun',
                'sort_order' => 4,
            ],
            [
                'code' => '2200',
                'name' => 'Kewajiban Jangka Panjang',
                'type' => AccountCategoryType::LIABILITY->value,
                'normal_balance' => -1,
                'description' => 'Utang dengan jangka waktu lebih dari satu tahun',
                'sort_order' => 5,
            ],

            // Equity
            [
                'code' => '3100',
                'name' => 'Ekuitas',
                'type' => AccountCategoryType::EQUITY->value,
                'normal_balance' => -1,
                'description' => 'Modal dan laba ditahan',
                'sort_order' => 6,
            ],

            // Revenue
            [
                'code' => '4100',
                'name' => 'Pendapatan Penjualan',
                'type' => AccountCategoryType::REVENUE->value,
                'normal_balance' => -1,
                'description' => 'Pendapatan dari penjualan produk utama',
                'sort_order' => 7,
            ],
            [
                'code' => '4200',
                'name' => 'Pendapatan Lainnya',
                'type' => AccountCategoryType::REVENUE->value,
                'normal_balance' => -1,
                'description' => 'Pendapatan di luar penjualan utama',
                'sort_order' => 8,
            ],

            // Expenses
            [
                'code' => '5100',
                'name' => 'Beban Pokok Penjualan',
                'type' => AccountCategoryType::EXPENSE->value,
                'normal_balance' => 1,
                'description' => 'Beban yang berhubungan langsung dengan penjualan',
                'sort_order' => 9,
            ],
            [
                'code' => '5200',
                'name' => 'Beban Operasional',
                'type' => AccountCategoryType::EXPENSE->value,
                'normal_balance' => 1,
                'description' => 'Beban operasional perusahaan',
                'sort_order' => 10,
            ],
            [
                'code' => '5300',
                'name' => 'Beban Lainnya',
                'type' => AccountCategoryType::EXPENSE->value,
                'normal_balance' => 1,
                'description' => 'Beban di luar operasional',
                'sort_order' => 11,
            ],
        ];

        $tenantId = $tenantId ?? Tenant::where('code', 'MAK')->first()?->id;

        if (!$tenantId) {
            $tenantId = Tenant::first()?->id;
        }

        foreach ($categories as $category) {
            AccountCategory::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'code' => $category['code'],
                ],
                array_merge($category, ['tenant_id' => $tenantId])
            );
        }
    }
}
