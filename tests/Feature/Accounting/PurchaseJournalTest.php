<?php

use App\Events\PurchaseOrderReceived;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\PurchaseOrder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Supplier;
use App\Models\JournalEntry;


beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    // Create required categories
    $assetCategory = \App\Models\AccountCategory::create([
        'tenant_id' => $this->tenant->id,
        'code' => '1000',
        'name' => 'Aset Lancar',
        'type' => 'asset',
        'normal_balance' => 'debit',
        'report_type' => 'balance_sheet',
    ]);

    $liabilityCategory = \App\Models\AccountCategory::create([
        'tenant_id' => $this->tenant->id,
        'code' => '2000',
        'name' => 'Kewajiban Jangka Pendek',
        'type' => 'liability',
        'normal_balance' => 'credit',
        'report_type' => 'balance_sheet',
    ]);

    // Create required accounts
    ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '1131',
        'account_name' => 'Persediaan Barang Dagangan',
        'type' => 'asset',
        'account_category_id' => $assetCategory->id,
        'normal_balance' => 'debit',
        'is_active' => true,
    ]);

    ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '2111',
        'account_name' => 'Hutang Usaha',
        'type' => 'liability',
        'account_category_id' => $liabilityCategory->id,
        'normal_balance' => 'credit',
        'is_active' => true,
    ]);
});

it('creates journal entry when purchase order is received', function () {
    $po = PurchaseOrder::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'purchase_no' => 'PO-' . now()->timestamp,
        'supplier_id' => Supplier::factory()->create(['tenant_id' => $this->tenant->id])->id,
        'order_date' => now(),
        'status' => 'draft',
        'grand_total' => 500000,
        'created_by' => $this->user->id,
    ]);

    // Dispatch event
    event(new PurchaseOrderReceived($po));

    // Assert journal entry exists
    $this->assertDatabaseHas('journal_entries', [
        'tenant_id' => $this->tenant->id,
        'reference_type' => 'PurchaseOrder',
        'reference_id' => $po->id,
    ]);

    $journal = JournalEntry::where('reference_id', $po->id)->first();
    
    // Assert lines
    expect($journal->lines)->toHaveCount(2);
    
    // Debit Inventory (1131)
    $this->assertDatabaseHas('journal_entry_lines', [
        'journal_entry_id' => $journal->id,
        'account_id' => ChartOfAccount::where('account_code', '1131')->first()->id,
        'debit' => 500000,
        'credit' => 0,
    ]);

    // Credit AP (2111)
    $this->assertDatabaseHas('journal_entry_lines', [
        'journal_entry_id' => $journal->id,
        'account_id' => ChartOfAccount::where('account_code', '2111')->first()->id,
        'debit' => 0,
        'credit' => 500000,
    ]);
});
