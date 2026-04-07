<?php

namespace Database\Factories;

use App\Models\LeadStage;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadStageFactory extends Factory
{
    protected $model = LeadStage::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->word,
        ];
    }
}
