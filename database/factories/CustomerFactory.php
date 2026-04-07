<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_group_id' => null,
            'code' => 'CST-'.fake()->unique()->numberBetween(10000, 99999),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'total_spent' => fake()->randomFloat(2, 0, 10000000),
            'status' => 'active',
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    public function withGroup(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_group_id' => CustomerGroup::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function withTotalSpent(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'total_spent' => $amount,
        ]);
    }
}
