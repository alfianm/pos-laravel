<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\Branch;
use App\Models\LeadSource;
use App\Models\LeadStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'branch_id' => Branch::factory(),
            'lead_no' => 'LEAD-' . strtoupper(bin2hex(random_bytes(3))),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'status' => 'new',
            'lead_source_id' => LeadSource::factory(),
            'lead_stage_id' => LeadStage::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
