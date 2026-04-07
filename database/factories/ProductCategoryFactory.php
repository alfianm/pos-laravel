<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'tenant_id' => Tenant::factory(),
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => ProductCategory::factory(),
        ]);
    }
}
