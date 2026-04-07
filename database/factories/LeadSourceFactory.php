<?php

namespace Database\Factories;

use App\Models\LeadSource;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadSourceFactory extends Factory
{
    protected $model = LeadSource::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->word,
        ];
    }
}
