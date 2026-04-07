<?php

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tenant;
use App\Models\Unit;
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

    $this->category = ProductCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->brand = Brand::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->unit = Unit::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
});

it('can list products', function () {
    Product::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
    ]);

    $response = $this->get(route('products.index'));

    $response->assertSuccessful();
    $response->assertSee('products');
});

it('can create a product', function () {
    $productData = [
        'name' => 'Test Product',
        'code' => 'PROD-001',
        'sku' => 'SKU-001',
        'barcode' => '123456789',
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
        'description' => 'Test product description',
        'base_price' => 100000,
        'cost_price' => 80000,
        'is_active' => true,
        'is_stockable' => true,
        'min_stock' => 10,
        'max_stock' => 100,
    ];

    $response = $this->post(route('products.store'), $productData);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'code' => 'PROD-001',
        'sku' => 'SKU-001',
        'tenant_id' => $this->tenant->id,
    ]);
});

it('can view product detail', function () {
    $product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
    ]);

    $response = $this->get(route('products.show', $product));

    $response->assertSuccessful();
    $response->assertSee($product->name);
});

it('can update a product', function () {
    $product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
        'name' => 'Old Name',
    ]);

    $updateData = [
        'name' => 'Updated Product Name',
        'code' => $product->code,
        'sku' => $product->sku,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
        'base_price' => 150000,
        'cost_price' => 100000,
    ];

    $response = $this->put(route('products.update', $product), $updateData);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product Name',
        'base_price' => 150000,
    ]);
});

it('can delete a product', function () {
    $product = Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
    ]);

    $response = $this->delete(route('products.destroy', $product));

    $response->assertRedirect();

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});

it('validates required fields when creating product', function () {
    $response = $this->post(route('products.store'), []);

    $response->assertSessionHasErrors(['name', 'code', 'category_id', 'unit_id']);
});

it('generates unique product code automatically', function () {
    $productData = [
        'name' => 'Auto Code Product',
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
        'base_price' => 100000,
    ];

    $response = $this->post(route('products.store'), $productData);

    $response->assertRedirect();

    $product = Product::where('name', 'Auto Code Product')->first();
    expect($product->code)->not->toBeEmpty();
    expect($product->code)->toStartWith('P');
});

it('enforces unique sku per tenant', function () {
    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'sku' => 'UNIQUE-SKU-001',
    ]);

    $response = $this->post(route('products.store'), [
        'name' => 'Duplicate SKU Product',
        'code' => 'PROD-002',
        'sku' => 'UNIQUE-SKU-001',
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'unit_id' => $this->unit->id,
        'base_price' => 100000,
    ]);

    $response->assertSessionHasErrors('sku');
});

it('can search products by name', function () {
    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'name' => 'Special Searchable Product',
    ]);

    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'name' => 'Other Product',
    ]);

    $response = $this->get(route('products.index', ['search' => 'Special']));

    $response->assertSuccessful();
    $response->assertSee('Special Searchable Product');
    $response->assertDontSee('Other Product');
});

it('can filter products by category', function () {
    $otherCategory = ProductCategory::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Other Category',
    ]);

    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'name' => 'Category A Product',
    ]);

    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $otherCategory->id,
        'name' => 'Category B Product',
    ]);

    $response = $this->get(route('products.index', ['category_id' => $this->category->id]));

    $response->assertSuccessful();
    $response->assertSee('Category A Product');
    $response->assertDontSee('Category B Product');
});

it('can filter products by status', function () {
    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'name' => 'Active Product',
        'is_active' => true,
    ]);

    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'name' => 'Inactive Product',
        'is_active' => false,
    ]);

    $response = $this->get(route('products.index', ['is_active' => 1]));

    $response->assertSuccessful();
    $response->assertSee('Active Product');
    $response->assertDontSee('Inactive Product');
});

it('scopes products to current tenant', function () {
    $otherTenant = Tenant::factory()->create();

    Product::factory()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->category->id,
        'name' => 'My Tenant Product',
    ]);

    Product::factory()->create([
        'tenant_id' => $otherTenant->id,
        'category_id' => $this->category->id,
        'name' => 'Other Tenant Product',
    ]);

    $response = $this->get(route('products.index'));

    $response->assertSuccessful();
    $response->assertSee('My Tenant Product');
    $response->assertDontSee('Other Tenant Product');
});
