<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\MarketplaceAccount;
use App\Models\MarketplaceShop;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketplaceShopFactory extends Factory
{
    protected $model = MarketplaceShop::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'marketplace_account_id' => MarketplaceAccount::factory(),
            'branch_id' => Branch::factory(),
            'external_shop_id' => $this->faker->uuid,
            'marketplace' => 'tokopedia',
            'name' => $this->faker->company . ' Shop',
            'status' => 'active',
        ];
    }
}
