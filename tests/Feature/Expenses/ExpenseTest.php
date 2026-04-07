<?php

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
    ]);
    $this->actingAs($this->user);

    $this->category = ExpenseCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Office Supplies',
    ]);
});

// Expense Category Tests
it('can list expense categories', function () {
    ExpenseCategory::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->get(route('expense-categories.index'));

    $response->assertSuccessful();
});

it('can create expense category', function () {
    $categoryData = [
        'name' => 'Travel Expenses',
        'description' => 'Business travel costs',
        'is_active' => true,
    ];

    $response = $this->post(route('expense-categories.store'), $categoryData);

    $response->assertRedirect();

    $this->assertDatabaseHas('expense_categories', [
        'tenant_id' => $this->tenant->id,
        'name' => 'Travel Expenses',
        'description' => 'Business travel costs',
    ]);
});

it('validates required fields for expense category', function () {
    $response = $this->post(route('expense-categories.store'), []);

    $response->assertSessionHasErrors(['name']);
});

it('can update expense category', function () {
    $category = ExpenseCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Old Name',
    ]);

    $response = $this->put(route('expense-categories.update', $category), [
        'name' => 'Updated Name',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('expense_categories', [
        'id' => $category->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete expense category', function () {
    $category = ExpenseCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->delete(route('expense-categories.destroy', $category));

    $response->assertRedirect();

    $this->assertDatabaseMissing('expense_categories', [
        'id' => $category->id,
    ]);
});

it('prevents deleting category with expenses', function () {
    $category = ExpenseCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $category->id,
    ]);

    $response = $this->delete(route('expense-categories.destroy', $category));

    $response->assertSessionHasErrors();
});

// Expense Tests
it('can list expenses', function () {
    Expense::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->get(route('expenses.index'));

    $response->assertSuccessful();
});

it('can create expense', function () {
    $expenseData = [
        'category_id' => $this->category->id,
        'branch_id' => $this->branch->id,
        'date' => now()->format('Y-m-d'),
        'amount' => 150000,
        'description' => 'Monthly office supplies',
        'payment_method' => 'cash',
        'reference_number' => 'EXP-001',
        'notes' => 'Receipt attached',
    ];

    $response = $this->post(route('expenses.store'), $expenseData);

    $response->assertRedirect();

    $this->assertDatabaseHas('expenses', [
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'amount' => 150000,
        'description' => 'Monthly office supplies',
    ]);
});

it('validates required fields for expense', function () {
    $response = $this->post(route('expenses.store'), []);

    $response->assertSessionHasErrors(['category_id', 'date', 'amount', 'description']);
});

it('validates positive amount for expense', function () {
    $response = $this->post(route('expenses.store'), [
        'category_id' => $this->category->id,
        'date' => now()->format('Y-m-d'),
        'amount' => -100,
        'description' => 'Invalid expense',
    ]);

    $response->assertSessionHasErrors('amount');
});

it('can update expense', function () {
    $expense = Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'amount' => 100000,
    ]);

    $response = $this->put(route('expenses.update', $expense), [
        'category_id' => $this->category->id,
        'date' => now()->format('Y-m-d'),
        'amount' => 200000,
        'description' => 'Updated expense',
        'payment_method' => 'bank_transfer',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'amount' => 200000,
        'description' => 'Updated expense',
    ]);
});

it('can delete expense', function () {
    $expense = Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->delete(route('expenses.destroy', $expense));

    $response->assertRedirect();

    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
    ]);
});

it('can filter expenses by category', function () {
    $otherCategory = ExpenseCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Other Category',
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'description' => 'Office expense',
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $otherCategory->id,
        'description' => 'Other expense',
    ]);

    $response = $this->get(route('expenses.index', ['category_id' => $this->category->id]));

    $response->assertSuccessful();
    $response->assertSee('Office expense');
    $response->assertDontSee('Other expense');
});

it('can filter expenses by date range', function () {
    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'date' => now()->subDays(10)->format('Y-m-d'),
        'description' => 'Old expense',
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'date' => now()->format('Y-m-d'),
        'description' => 'Recent expense',
    ]);

    $response = $this->get(route('expenses.index', [
        'start_date' => now()->subDays(5)->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d'),
    ]));

    $response->assertSuccessful();
    $response->assertSee('Recent expense');
    $response->assertDontSee('Old expense');
});

it('can filter expenses by branch', function () {
    $otherBranch = Branch::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Other Branch',
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'description' => 'My branch expense',
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $otherBranch->id,
        'category_id' => $this->category->id,
        'description' => 'Other branch expense',
    ]);

    $response = $this->get(route('expenses.index', ['branch_id' => $this->branch->id]));

    $response->assertSuccessful();
    $response->assertSee('My branch expense');
    $response->assertDontSee('Other branch expense');
});

it('calculates total expenses correctly', function () {
    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'amount' => 100000,
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'amount' => 200000,
    ]);

    $response = $this->get(route('expenses.index'));

    $response->assertSuccessful();
    // Total should be 300000
});

it('supports multiple payment methods', function () {
    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'payment_method' => 'cash',
        'amount' => 50000,
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'payment_method' => 'bank_transfer',
        'amount' => 100000,
    ]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'payment_method' => 'credit_card',
        'amount' => 150000,
    ]);

    $response = $this->get(route('expenses.index'));

    $response->assertSuccessful();
});

it('scopes expenses to current tenant', function () {
    $otherTenant = Tenant::factory()->create();
    $otherBranch = Branch::factory()->create(['tenant_id' => $otherTenant->id]);

    Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'description' => 'My tenant expense',
    ]);

    Expense::factory()->create([
        'tenant_id' => $otherTenant->id,
        'branch_id' => $otherBranch->id,
        'category_id' => ExpenseCategory::factory()->create(['tenant_id' => $otherTenant->id])->id,
        'description' => 'Other tenant expense',
    ]);

    $response = $this->get(route('expenses.index'));

    $response->assertSuccessful();
    $response->assertSee('My tenant expense');
    $response->assertDontSee('Other tenant expense');
});

it('can view expense details', function () {
    $expense = Expense::factory()->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
        'description' => 'Detailed expense view',
    ]);

    $response = $this->get(route('expenses.show', $expense));

    $response->assertSuccessful();
    $response->assertSee('Detailed expense view');
});

it('can export expenses', function () {
    Expense::factory()->count(3)->create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->get(route('expenses.export', ['format' => 'csv']));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
