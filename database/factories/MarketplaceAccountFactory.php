<?php

namespace Database\Factories;

use App\Models\MarketplaceAccount;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketplaceAccountFactory extends Factory
{
    protected $model = MarketplaceAccount::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'marketplace' => $this->faker->randomElement(['tokopedia', 'shopee', 'lazada']),
            'name' => $this->faker->company . ' Store',
            'status' => 'active',
        ];
    }
}
