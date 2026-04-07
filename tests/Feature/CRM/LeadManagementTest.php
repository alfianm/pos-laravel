<?php

namespace Tests\Feature\CRM;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Livewire\CRM\LeadForm;
use App\Livewire\CRM\LeadList;
use App\Livewire\CRM\FollowUpList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $tenant;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::factory()->create();
        $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'active_branch_id' => $this->branch->id
        ]);
        
        LeadSource::create(['tenant_id' => $this->tenant->id, 'name' => 'Web']);
        LeadStage::create(['tenant_id' => $this->tenant->id, 'name' => 'New']);
    }

    public function test_can_view_lead_list()
    {
        $this->actingAs($this->user);
        
        Lead::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

        Livewire::test(LeadList::class)
            ->assertStatus(200)
            ->assertViewHas('leads', function($leads) {
                return $leads->count() === 3;
            });
    }

    public function test_can_create_new_lead()
    {
        $this->actingAs($this->user);
        
        $source = LeadSource::first();
        $stage = LeadStage::first();

        Livewire::test(LeadForm::class)
            ->set('name', 'John Lead')
            ->set('email', 'john@lead.com')
            ->set('phone', '0812345678')
            ->set('lead_source_id', $source->id)
            ->set('lead_stage_id', $stage->id)
            ->call('save')
            ->assertRedirect(route('crm.leads.index'));

        $this->assertDatabaseHas('leads', [
            'name' => 'John Lead',
            'email' => 'john@lead.com',
            'tenant_id' => $this->tenant->id
        ]);
    }

    public function test_can_log_follow_up()
    {
        $this->actingAs($this->user);
        
        $lead = Lead::factory()->create(['tenant_id' => $this->tenant->id]);

        Livewire::test(FollowUpList::class, ['leadId' => $lead->id])
            ->set('interaction_type', 'call')
            ->set('notes', 'Called the client, very interested.')
            ->call('saveFollowUp')
            ->assertEmitted('refreshTimeline');

        $this->assertDatabaseHas('follow_ups', [
            'lead_id' => $lead->id,
            'interaction_type' => 'call',
            'notes' => 'Called the client, very interested.'
        ]);
    }
}
