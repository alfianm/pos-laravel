<?php

use App\Livewire\Reports\CrmConversionReport;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    Permission::findOrCreate('view reports', 'web');
    $this->user->givePermissionTo('view reports');

    $this->source = LeadSource::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Website',
    ]);
    $this->stage = LeadStage::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Qualified',
        'sort_order' => 1,
    ]);

    $this->actingAs($this->user);
});

it('displays the crm conversion report page for authorized users', function (): void {
    $this->get(route('reports.crm.conversion'))
        ->assertSuccessful()
        ->assertSee('CRM Conversion Report');
});

it('summarizes crm conversion metrics for the active tenant only', function (): void {
    $convertedCustomer = Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'CUST-CRM-001',
        'name' => 'Converted Customer',
    ]);

    Lead::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'lead_source_id' => $this->source->id,
        'lead_stage_id' => $this->stage->id,
        'name' => 'Converted Lead',
        'email' => 'converted@example.test',
        'status' => 'converted',
        'converted_at' => now(),
        'converted_customer_id' => $convertedCustomer->id,
    ]);

    Lead::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'lead_source_id' => $this->source->id,
        'lead_stage_id' => $this->stage->id,
        'name' => 'Lost Lead',
        'email' => 'lost@example.test',
        'status' => 'lost',
    ]);

    Lead::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'lead_source_id' => $this->source->id,
        'lead_stage_id' => $this->stage->id,
        'name' => 'New Lead',
        'email' => 'new@example.test',
        'status' => 'new',
    ]);

    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create([
        'tenant_id' => $otherTenant->id,
    ]);
    $otherSource = LeadSource::create([
        'tenant_id' => $otherTenant->id,
        'name' => 'Referral',
    ]);
    $otherStage = LeadStage::create([
        'tenant_id' => $otherTenant->id,
        'name' => 'Prospect',
        'sort_order' => 1,
    ]);

    Lead::create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'lead_source_id' => $otherSource->id,
        'lead_stage_id' => $otherStage->id,
        'name' => 'Other Tenant Lead',
        'status' => 'converted',
        'converted_at' => now(),
    ]);

    $component = Livewire::test(CrmConversionReport::class)
        ->set('date_from', now()->subDay()->format('Y-m-d'))
        ->set('date_to', now()->addDay()->format('Y-m-d'));

    $grandTotals = $component->get('grandTotals');
    $sourceConversion = $component->get('sourceConversion');
    $recentConversions = $component->get('recentConversions');

    expect((int) $grandTotals->total_leads)->toBe(3)
        ->and((int) $grandTotals->converted_count)->toBe(1)
        ->and((int) $grandTotals->lost_count)->toBe(1)
        ->and((int) $grandTotals->new_count)->toBe(1)
        ->and((float) $grandTotals->conversion_rate)->toBe(33.3)
        ->and($sourceConversion)->toHaveCount(1)
        ->and((int) $sourceConversion->first()->total_leads)->toBe(3)
        ->and((int) $sourceConversion->first()->converted_count)->toBe(1)
        ->and($recentConversions->total())->toBe(1);

    $component->assertSee('Converted Lead')
        ->assertDontSee('Other Tenant Lead');
});
