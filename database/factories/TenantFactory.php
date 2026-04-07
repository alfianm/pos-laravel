<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'code' => strtoupper(\Illuminate\Support\Str::random(5)),
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'status' => 'active',
        ];
    }
}
