<?php

namespace Tests\Feature\CRM;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerTimeline;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;

    protected $branch;

    protected $user;

    protected $customer;

    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'active_branch_id' => $this->branch->id,
        ]);
        $this->actingAs($this->user);

        $category = ProductCategory::factory()->create(['tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'track_stock' => true,
            'selling_price' => 100000,
            'sku' => 'TEST-001',
        ]);

        Inventory::factory()->create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'product_id' => $this->product->id,
            'qty_on_hand' => 100,
            'qty_available' => 100,
        ]);

        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Customer',
        ]);
    }

    public function test_sale_creates_customer_timeline_entry()
    {
        $saleService = app(SaleService::class);

        $saleData = [
            'customer_id' => $this->customer->id,
            'cart' => [
                [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => 2,
                    'price' => 100000,
                ],
            ],
            'subtotal' => 200000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 200000,
            'paid_amount' => 200000,
            'payment_method' => 'cash',
            'notes' => 'Test sale',
        ];

        $sale = $saleService->checkout($saleData);

        $this->assertDatabaseHas('customer_timelines', [
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'sale_id' => $sale->id,
            'type' => 'sale',
        ]);

        $timeline = CustomerTimeline::where('sale_id', $sale->id)->first();
        $this->assertNotNull($timeline);
        $this->assertStringContainsString('Pembelian', $timeline->title);
        $this->assertStringContainsString($sale->sale_no, $timeline->title);
        $this->assertStringContainsString('200.000', $timeline->description);
        $this->assertEquals('sale', $timeline->type);
    }

    public function test_customer_timeline_contains_correct_metadata()
    {
        $saleService = app(SaleService::class);

        $saleData = [
            'customer_id' => $this->customer->id,
            'cart' => [
                [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => 3,
                    'price' => 100000,
                ],
            ],
            'subtotal' => 300000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 300000,
            'paid_amount' => 300000,
            'payment_method' => 'transfer',
            'notes' => 'Test sale with metadata',
        ];

        $sale = $saleService->checkout($saleData);

        $timeline = CustomerTimeline::where('sale_id', $sale->id)->first();
        $this->assertNotNull($timeline);

        $metadata = $timeline->metadata;
        $this->assertEquals($sale->sale_no, $metadata['sale_no']);
        $this->assertEquals(300000, $metadata['grand_total']);
        $this->assertEquals('transfer', $metadata['payment_method']);
        $this->assertIsArray($metadata['items']);
        $this->assertCount(1, $metadata['items']);
        $this->assertEquals($this->product->id, $metadata['items'][0]['product_id']);
        $this->assertEquals(3, $metadata['items'][0]['qty']);
    }

    public function test_sale_without_customer_does_not_create_timeline()
    {
        $saleService = app(SaleService::class);

        $saleData = [
            'customer_id' => null,
            'cart' => [
                [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => 1,
                    'price' => 100000,
                ],
            ],
            'subtotal' => 100000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 100000,
            'paid_amount' => 100000,
            'payment_method' => 'cash',
        ];

        $sale = $saleService->checkout($saleData);

        $this->assertDatabaseMissing('customer_timelines', [
            'sale_id' => $sale->id,
        ]);
    }

    public function test_customer_timeline_scopes_work()
    {
        // Create multiple sales for the customer
        $saleService = app(SaleService::class);

        for ($i = 0; $i < 3; $i++) {
            $saleData = [
                'customer_id' => $this->customer->id,
                'cart' => [
                    [
                        'id' => $this->product->id,
                        'name' => $this->product->name,
                        'qty' => 1,
                        'price' => 100000,
                    ],
                ],
                'subtotal' => 100000,
                'tax_amount' => 0,
                'discount' => 0,
                'total' => 100000,
                'paid_amount' => 100000,
                'payment_method' => 'cash',
            ];
            $saleService->checkout($saleData);
        }

        // Test forCustomer scope
        $customerTimelines = CustomerTimeline::forCustomer($this->customer->id)->get();
        $this->assertCount(3, $customerTimelines);

        // Test ofType scope
        $saleTimelines = CustomerTimeline::ofType('sale')->get();
        $this->assertCount(3, $saleTimelines);

        // Test recent scope (should be ordered by occurred_at desc)
        $recentTimelines = CustomerTimeline::recent()->get();
        $this->assertCount(3, $recentTimelines);

        // Verify ordering (most recent first)
        $first = $recentTimelines->first();
        $last = $recentTimelines->last();
        $this->assertGreaterThanOrEqual($last->occurred_at, $first->occurred_at);
    }

    public function test_customer_timeline_relationships()
    {
        $saleService = app(SaleService::class);

        $saleData = [
            'customer_id' => $this->customer->id,
            'cart' => [
                [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => 1,
                    'price' => 100000,
                ],
            ],
            'subtotal' => 100000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 100000,
            'paid_amount' => 100000,
            'payment_method' => 'cash',
        ];

        $sale = $saleService->checkout($saleData);
        $timeline = CustomerTimeline::where('sale_id', $sale->id)->first();

        $this->assertNotNull($timeline);
        $this->assertEquals($this->customer->id, $timeline->customer->id);
        $this->assertEquals($sale->id, $timeline->sale->id);
        $this->assertEquals($this->branch->id, $timeline->branch->id);
        $this->assertEquals($this->tenant->id, $timeline->tenant->id);
    }
}
