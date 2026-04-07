<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => \App\Models\Tenant::factory(),
            'branch_id' => \App\Models\Branch::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'created_by' => \App\Models\User::factory(),
            'purchase_no' => 'PO-' . now()->format('Ymd') . '-' . strtoupper($this->faker->unique()->bothify('####')),
            'order_date' => now(),
            'grand_total' => 0,
            'status' => 'received',
        ];
    }
}
