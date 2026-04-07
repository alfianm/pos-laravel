<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'branch_id' => Branch::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'qty_on_hand' => fake()->numberBetween(0, 1000),
            'qty_reserved' => 0,
            'qty_available' => fake()->numberBetween(0, 1000),
            'avg_cost' => fake()->numberBetween(1000, 100000),
            'reorder_level' => 10,
        ];
    }
}
