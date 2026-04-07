<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerImportService extends BaseImportService
{
    /**
     * Process a single row of data for Customer.
     */
    protected function processRow(array $data, int $rowNumber)
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $this->batch->tenant_id;

            // 1. Resolve Customer Group
            $groupId = null;
            if (!empty($data['customer_group'] ?? $data['grup_pelanggan'])) {
                $groupName = $data['customer_group'] ?? $data['grup_pelanggan'];
                $group = CustomerGroup::firstOrCreate(
                    ['tenant_id' => $tenantId, 'name' => $groupName],
                    ['id' => (string) Str::uuid(), 'slug' => Str::slug($groupName)]
                );
                $groupId = $group->id;
            }

            // 2. Create or Update Customer
            $customer = Customer::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'phone' => $data['phone'] ?? $data['no_hp'] ?? null
                ],
                [
                    'name' => $data['name'] ?? $data['nama_pelanggan'],
                    'email' => $data['email'] ?? null,
                    'address' => $data['address'] ?? $data['alamat'] ?? null,
                    'customer_group_id' => $groupId,
                    'loyalty_points' => $data['loyalty_points'] ?? $data['poin'] ?? 0,
                    'total_spent' => 0,
                    'is_active' => true,
                ]
            );

            return $customer;
        });
    }
}
