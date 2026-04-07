<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\AccountCategory;
use App\Models\Tenant;
use App\Constants\NormalBalance;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = $tenantId ?? Tenant::where('code', 'MAK')->first()?->id;

        if (!$tenantId) {
            $tenantId = Tenant::first()?->id;
        }

        // Get categories with details
        $categories = AccountCategory::where('tenant_id', $tenantId)
            ->get()
            ->keyBy('code');

        // Structure: [code, name, category_code, level, parent_code, is_cash, is_bank, normal_balance]
        $accounts = [
            // 1100 - Aset Lancar (Major Account)
            ['1100', 'Aset Lancar', '1100', 1, null, false, false, NormalBalance::DEBIT],
            ['1110', 'Kas dan Setara Kas', '1100', 2, '1100', false, false, NormalBalance::DEBIT],
            ['1111', 'Kas Kantor', '1100', 3, '1110', true, false, NormalBalance::DEBIT],
            ['1112', 'Kas Cabang', '1100', 3, '1110', true, false, NormalBalance::DEBIT],
            ['1113', 'Rekening Bank - BCA', '1100', 3, '1110', true, true, NormalBalance::DEBIT],
            ['1114', 'Rekening Bank - Mandiri', '1100', 3, '1110', true, true, NormalBalance::DEBIT],
            ['1115', 'Rekening Bank - BRI', '1100', 3, '1110', true, true, NormalBalance::DEBIT],

            ['1120', 'Piutang Usaha', '1100', 2, '1100', false, false, NormalBalance::DEBIT],
            ['1121', 'Piutang Karyawan', '1100', 3, '1120', false, false, NormalBalance::DEBIT],
            ['1122', 'Piutang Lainnya', '1100', 3, '1120', false, false, NormalBalance::DEBIT],

            ['1130', 'Persediaan', '1100', 2, '1100', false, false, NormalBalance::DEBIT],
            ['1131', 'Persediaan Barang Dagangan', '1100', 3, '1130', false, false, NormalBalance::DEBIT],
            ['1132', 'Persediaan Retur', '1100', 3, '1130', false, false, NormalBalance::DEBIT],

            ['1140', 'Uang Muka Pembelian', '1100', 2, '1100', false, false, NormalBalance::DEBIT],

            // 1200 - Aset Tetap
            ['1200', 'Aset Tetap', '1200', 1, null, false, false, NormalBalance::DEBIT],
            ['1210', 'Tanah dan Bangunan', '1200', 2, '1200', false, false, NormalBalance::DEBIT],
            ['1211', 'Tanah', '1200', 3, '1210', false, false, NormalBalance::DEBIT],
            ['1212', 'Bangunan', '1200', 3, '1210', false, false, NormalBalance::DEBIT],

            ['1220', 'Kendaraan', '1200', 2, '1200', false, false, NormalBalance::DEBIT],
            ['1221', 'Mobil Operasional', '1200', 3, '1220', false, false, NormalBalance::DEBIT],
            ['1222', 'Motor Operasional', '1200', 3, '1220', false, false, NormalBalance::DEBIT],

            ['1230', 'Inventaris dan Peralatan', '1200', 2, '1200', false, false, NormalBalance::DEBIT],
            ['1231', 'Komputer & Laptop', '1200', 3, '1230', false, false, NormalBalance::DEBIT],
            ['1232', 'Perlengkapan Kantor', '1200', 3, '1230', false, false, NormalBalance::DEBIT],
            ['1233', 'Rak & Etalase', '1200', 3, '1230', false, false, NormalBalance::DEBIT],

            // 2100 - Kewajiban Jangka Pendek
            ['2100', 'Kewajiban Jangka Pendek', '2100', 1, null, false, false, NormalBalance::CREDIT],
            ['2110', 'Utang Usaha', '2100', 2, '2100', false, false, NormalBalance::CREDIT],
            ['2111', 'Utang Supplier', '2100', 3, '2110', false, false, NormalBalance::CREDIT],
            ['2112', 'Utang Lainnya', '2100', 3, '2110', false, false, NormalBalance::CREDIT],

            ['2120', 'Utang Pajak', '2100', 2, '2100', false, false, NormalBalance::CREDIT],
            ['2121', 'PPN Masukan', '2100', 3, '2120', false, false, NormalBalance::CREDIT],
            ['2122', 'PPN Keluaran', '2100', 3, '2120', false, false, NormalBalance::CREDIT],
            ['2123', 'PPh 21', '2100', 3, '2120', false, false, NormalBalance::CREDIT],
            ['2124', 'PPh 23', '2100', 3, '2120', false, false, NormalBalance::CREDIT],

            ['2130', 'Uang Muka Penjualan', '2100', 2, '2100', false, false, NormalBalance::CREDIT],

            // 3100 - Ekuitas
            ['3100', 'Ekuitas', '3100', 1, null, false, false, NormalBalance::CREDIT],
            ['3110', 'Modal Pemilik', '3100', 2, '3100', false, false, NormalBalance::CREDIT],
            ['3111', 'Modal Disetor', '3100', 3, '3110', false, false, NormalBalance::CREDIT],
            ['3112', 'Modal Tambahan', '3100', 3, '3110', false, false, NormalBalance::CREDIT],

            ['3120', 'Laba Ditahan', '3100', 2, '3100', false, false, NormalBalance::CREDIT],
            ['3130', 'Laba Tahun Berjalan', '3100', 2, '3100', false, false, NormalBalance::CREDIT],

            // 4100 - Pendapatan Penjualan
            ['4100', 'Pendapatan Penjualan', '4100', 1, null, false, false, NormalBalance::CREDIT],
            ['4101', 'Penjualan Tunai', '4100', 2, '4100', false, false, NormalBalance::CREDIT],
            ['4102', 'Penjualan Kredit', '4100', 2, '4100', false, false, NormalBalance::CREDIT],
            ['4103', 'Penjualan Online', '4100', 2, '4100', false, false, NormalBalance::CREDIT],

            // 4200 - Pendapatan Lainnya
            ['4200', 'Pendapatan Lainnya', '4200', 1, null, false, false, NormalBalance::CREDIT],
            ['4201', 'Diskon Pembelian', '4200', 2, '4200', false, false, NormalBalance::CREDIT],
            ['4202', 'Pendapatan Bunga', '4200', 2, '4200', false, false, NormalBalance::CREDIT],
            ['4203', 'Pendapatan Lainnya', '4200', 2, '4200', false, false, NormalBalance::CREDIT],

            // 5100 - Beban Pokok Penjualan
            ['5100', 'Beban Pokok Penjualan', '5100', 1, null, false, false, NormalBalance::DEBIT],
            ['5101', 'Harga Pokok Penjualan', '5100', 2, '5100', false, false, NormalBalance::DEBIT],
            ['5102', 'Retur Pembelian', '5100', 2, '5100', false, false, NormalBalance::DEBIT],
            ['5103', 'Potongan Pembelian', '5100', 2, '5100', false, false, NormalBalance::DEBIT],

            // 5200 - Beban Operasional
            ['5200', 'Beban Operasional', '5200', 1, null, false, false, NormalBalance::DEBIT],
            ['5201', 'Beban Gaji dan Tunjangan', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5202', 'Beban Sewa', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5203', 'Beban Listrik dan Air', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5204', 'Beban Telepon dan Internet', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5205', 'Beban Transportasi', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5206', 'Beban Pemasaran & Iklan', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5207', 'Beban Perawatan & Pemeliharaan', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5208', 'Beban Perlengkapan Kantor', '5200', 2, '5200', false, false, NormalBalance::DEBIT],
            ['5209', 'Beban Depresiasi', '5200', 2, '5200', false, false, NormalBalance::DEBIT],

            // 5300 - Beban Lainnya
            ['5300', 'Beban Lainnya', '5300', 1, null, false, false, NormalBalance::DEBIT],
            ['5301', 'Beban Bunga Bank', '5300', 2, '5300', false, false, NormalBalance::DEBIT],
            ['5302', 'Beban Administrasi Bank', '5300', 2, '5300', false, false, NormalBalance::DEBIT],
            ['5303', 'Beban Lainnya', '5300', 2, '5300', false, false, NormalBalance::DEBIT],
        ];

        // Build parent ID mapping
        $parentIds = [];

        foreach ($accounts as $account) {
            $parentId = null;
            if ($account[4] !== null) {
                $parentId = $parentIds[$account[4]] ?? null;
            }

            $category = $categories->get($account[2]);

            if (!$category) {
                \Log::warning("Accounting: Category not found for code " . $account[2]);
                continue;
            }

            $coa = ChartOfAccount::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'account_code' => $account[0],
                ],
                [
                    'account_name' => $account[1],
                    'account_category_id' => $category->id,
                    'type' => $category->type,
                    'level' => $account[3],
                    'parent_id' => $parentId,
                    'is_cash_account' => $account[5],
                    'is_bank_account' => $account[6],
                    'normal_balance' => $account[7]->value,
                    'is_active' => true,
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]
            );

            $parentIds[$account[0]] = $coa->id;
        }
    }
}
