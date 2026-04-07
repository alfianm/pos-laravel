<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->city . ' Store',
            'code' => strtoupper(\Illuminate\Support\Str::random(3)),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'status' => 'active',
        ];
    }
}
