<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => null,
            'brand_id' => null,
            'unit_id' => null,
            'code' => 'P'.fake()->unique()->numberBetween(10000, 99999),
            'name' => fake()->words(3, true),
            'sku' => 'SKU-'.fake()->unique()->numberBetween(10000, 99999),
            'barcode' => fake()->ean13(),
            'selling_price' => fake()->numberBetween(10000, 500000),
            'cost_price' => fake()->numberBetween(5000, 250000),
            'is_active' => true,
            'type' => 'single',
            'description' => fake()->sentence(),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => ProductCategory::factory(),
        ]);
    }

    public function withBrand(): static
    {
        return $this->state(fn (array $attributes) => [
            'brand_id' => Brand::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
