<?php

use App\Events\SaleCreated;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Product;
use App\Models\JournalEntry;
use App\Models\AccountCategory;
use App\Constants\NormalBalance;

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

    $revenueCategory = AccountCategory::create([
        'tenant_id' => $this->tenant->id,
        'code' => '4000',
        'name' => 'Pendapatan',
        'type' => 'revenue',
        'normal_balance' => NormalBalance::CREDIT->value,
        'report_type' => 'profit_loss',
    ]);

    $expenseCategory = AccountCategory::create([
        'tenant_id' => $this->tenant->id,
        'code' => '5000',
        'name' => 'Beban Pokok Penjualan',
        'type' => 'expense',
        'normal_balance' => 'debit',
        'report_type' => 'profit_loss',
    ]);

    // Create required accounts
    ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '1111',
        'account_name' => 'Kas Kantor',
        'type' => 'asset',
        'account_category_id' => $assetCategory->id,
        'normal_balance' => NormalBalance::DEBIT->value,
        'is_active' => true,
    ]);

    ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '4101',
        'account_name' => 'Pendapatan Penjualan',
        'type' => 'revenue',
        'account_category_id' => $revenueCategory->id,
        'normal_balance' => 'credit',
        'is_active' => true,
    ]);

    ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '5101',
        'account_name' => 'HPP Penjualan',
        'type' => 'expense',
        'account_category_id' => $expenseCategory->id,
        'normal_balance' => NormalBalance::DEBIT->value,
        'is_active' => true,
    ]);

    ChartOfAccount::create([
        'tenant_id' => $this->tenant->id,
        'account_code' => '1131',
        'account_name' => 'Persediaan Barang Dagangan',
        'type' => 'asset',
        'account_category_id' => $assetCategory->id,
        'normal_balance' => 'debit',
        'is_active' => true,
    ]);
});

it('creates journal entries when sale is created', function () {
    $product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'cost_price' => 300000,
        'selling_price' => 500000,
    ]);

    $sale = Sale::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'sale_no' => 'SALE-' . now()->timestamp,
        'sale_date' => now(),
        'subtotal' => 500000,
        'grand_total' => 500000,
        'payment_status' => 'paid',
        'created_by' => $this->user->id,
    ]);

    $sale->items()->create([
        'tenant_id' => $this->tenant->id,
        'product_id' => $product->id,
        'qty' => 1,
        'unit_price' => 500000,
        'line_total' => 500000,
    ]);

    // Dispatch event
    event(new SaleCreated($sale));

    // 1. Assert Revenue Journal Entry exists
    $this->assertDatabaseHas('journal_entries', [
        'tenant_id' => $this->tenant->id,
        'reference_type' => 'Sale',
        'reference_id' => $sale->id,
        'description' => 'Jurnal Penjualan #' . $sale->sale_no,
    ]);

    // 2. Assert COGS Journal Entry exists
    $this->assertDatabaseHas('journal_entries', [
        'tenant_id' => $this->tenant->id,
        'reference_type' => 'Sale',
        'reference_id' => $sale->id,
        'description' => 'Jurnal HPP & Persediaan #' . $sale->sale_no,
    ]);

    $revenueJournal = JournalEntry::where('reference_id', $sale->id)
        ->where('description', 'like', 'Jurnal Penjualan%')
        ->first();
    
    // Debit Kas (1111)
    $this->assertDatabaseHas('journal_entry_lines', [
        'journal_entry_id' => $revenueJournal->id,
        'account_id' => ChartOfAccount::where('account_code', '1111')->first()->id,
        'debit' => 500000,
        'credit' => 0,
    ]);

    // Credit Revenue (4101)
    $this->assertDatabaseHas('journal_entry_lines', [
        'journal_entry_id' => $revenueJournal->id,
        'account_id' => ChartOfAccount::where('account_code', '4101')->first()->id,
        'debit' => 0,
        'credit' => 500000,
    ]);

    $cogsJournal = JournalEntry::where('reference_id', $sale->id)
        ->where('description', 'like', 'Jurnal HPP%')
        ->first();

    // Debit COGS (5101)
    $this->assertDatabaseHas('journal_entry_lines', [
        'journal_entry_id' => $cogsJournal->id,
        'account_id' => ChartOfAccount::where('account_code', '5101')->first()->id,
        'debit' => 300000,
        'credit' => 0,
    ]);

    // Credit Inventory (1131)
    $this->assertDatabaseHas('journal_entry_lines', [
        'journal_entry_id' => $cogsJournal->id,
        'account_id' => ChartOfAccount::where('account_code', '1131')->first()->id,
        'debit' => 0,
        'credit' => 300000,
    ]);
});
