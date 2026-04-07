<?php

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Tenant;
use App\Models\User;
use App\Models\JournalEntry;
use App\Models\AccountCategory;
use App\Constants\NormalBalance;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    // Create required categories
    $assetCategory = AccountCategory::create([
        'tenant_id' => $this->tenant->id,
        'code' => '1000',
        'name' => 'Aset Lancar',
        'type' => 'asset',
        'normal_balance' => 'debit',
        'report_type' => 'balance_sheet',
    ]);

    // Create required accounts
    $this->cashAccount = ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '1111',
        'account_name' => 'Kas Kantor',
        'type' => 'asset',
        'account_category_id' => $assetCategory->id,
        'normal_balance' => NormalBalance::DEBIT->value,
        'is_active' => true,
    ]);

    $this->receivableAccount = ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '1121',
        'account_name' => 'Piutang Usaha',
        'type' => 'asset',
        'account_category_id' => $assetCategory->id,
        'normal_balance' => NormalBalance::DEBIT->value,
        'is_active' => true,
    ]);
});

it('creates and posts manual journal entry', function () {
    $service = app(JournalEntryService::class);
    
    // Act as user
    $this->actingAs($this->user);

    // 1. Create Draft Entry
    $entry = $service->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'date' => now()->toDateString(),
        'description' => 'Manual Cash Receipt',
        'lines' => [
            [
                'account_id' => $this->cashAccount->id,
                'debit' => 1000000,
                'credit' => 0,
                'description' => 'Debit Cash'
            ],
            [
                'account_id' => $this->receivableAccount->id,
                'debit' => 0,
                'credit' => 1000000,
                'description' => 'Credit Receivable'
            ]
        ]
    ]);

    expect($entry->is_posted)->toBeFalse();
    expect((float)$entry->total_debit)->toBe(1000000.0);
    expect($entry->is_balanced)->toBeTrue();

    // 2. Post Entry
    $service->post($entry);

    $entry->refresh();
    expect($entry->is_posted)->toBeTrue();
    expect($entry->status)->toBe('posted');

    // 3. Verify Account Balances
    // Cash balance should increase by 1,000,000
    $this->cashAccount->refresh();
    expect((float)$this->cashAccount->current_balance)->toBe(1000000.0);

    // Receivable balance should decrease by 1,000,000 (Debit-normal account, Credit line)
    $this->receivableAccount->refresh();
    expect((float)$this->receivableAccount->current_balance)->toBe(-1000000.0);
});

it('prevents posting unbalanced entries', function () {
    $service = app(JournalEntryService::class);
    $this->actingAs($this->user);

    $entry = $service->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'date' => now()->toDateString(),
        'description' => 'Unbalanced Entry',
        'lines' => [
            [
                'account_id' => $this->cashAccount->id,
                'debit' => 1000000,
                'credit' => 0,
            ]
        ]
    ]);

    expect(fn() => $service->post($entry))->toThrow(\InvalidArgumentException::class);
});
