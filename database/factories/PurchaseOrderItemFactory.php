<?php

namespace Database\Factories;

use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 10);
        $unitCost = $this->faker->numberBetween(1000, 100000);

        return [
            'purchase_order_id' => \App\Models\PurchaseOrder::factory(),
            'product_id' => \App\Models\Product::factory(),
            'qty' => $qty,
            'received_qty' => $qty,
            'unit_cost' => $unitCost,
            'subtotal' => $qty * $unitCost,
        ];
    }
}
