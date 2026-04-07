<?php

namespace App\Livewire\MasterData;

use App\Constants\Subscription;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Exceptions\QuotaExceededException;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Services\QuotaService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;

    public $code;

    public $sku;

    public $barcode;

    public $name;

    public $type = 'single';

    public $category_id;

    public $brand_id;

    public $unit_id;

    public $purchase_price = 0;

    public $selling_price = 0;

    public $cost_price = 0;

    public $track_stock = true;

    public $allow_decimal = false;

    public $has_expiry = false;

    public $is_active = true;

    public $description;

    public $image;

    public $old_image_url;

    public $variants = [];

    public $isEdit = false;

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:products,code,'.$this->productId,
            'sku' => 'required|string|max:100|unique:products,sku,'.$this->productId,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,'.$this->productId,
            'name' => 'required|string|max:200',
            'type' => 'required|in:single,variable,service',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'purchase_price' => 'numeric|min:0',
            'selling_price' => 'numeric|min:0',
            'cost_price' => 'numeric|min:0',
            'track_stock' => 'boolean',
            'allow_decimal' => 'boolean',
            'has_expiry' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ];
    }

    public function mount($productId = null)
    {
        if ($productId) {
            $this->productId = $productId;
            $product = Product::findOrFail($productId);
            $this->code = $product->code;
            $this->sku = $product->sku;
            $this->barcode = $product->barcode;
            $this->name = $product->name;
            $this->type = $product->type;
            $this->category_id = $product->category_id;
            $this->brand_id = $product->brand_id;
            $this->unit_id = $product->unit_id;
            $this->purchase_price = $product->purchase_price;
            $this->selling_price = $product->selling_price;
            $this->cost_price = $product->cost_price;
            $this->track_stock = $product->track_stock;
            $this->allow_decimal = $product->allow_decimal;
            $this->has_expiry = $product->has_expiry;
            $this->is_active = $product->is_active;
            $this->description = $product->description;
            $this->old_image_url = $product->image_url;
            $this->isEdit = true;

            // Load variants if multiple
            if ($this->type === 'variable') {
                $this->variants = $product->variants()->get()->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'sku' => $v->sku,
                        'price' => (float) $v->selling_price,
                        'cost' => (float) $v->cost_price,
                    ];
                })->toArray();
            }
        } else {
            $this->code = 'PRD-'.strtoupper(Str::random(8));
            $this->sku = 'SKU-'.strtoupper(Str::random(8));
        }
    }

    public function addVariant()
    {
        $this->variants[] = [
            'name' => '',
            'sku' => $this->sku.'-'.(count($this->variants) + 1),
            'price' => $this->selling_price,
            'cost' => $this->cost_price,
        ];
    }

    public function removeVariant($index)
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function save()
    {
        $this->validate();

        if (! $this->isEdit) {
            try {
                $quotaService = app(QuotaService::class);
                $tenantId = auth()->user()->tenant_id;
                $quotaService->enforceQuota($tenantId, Subscription::QUOTA_PRODUCTS, 1);
            } catch (QuotaExceededException $e) {
                session()->flash('error', $e->getMessage());

                return;
            }
        }

        $data = [
            'code' => $this->code,
            'sku' => $this->sku,
            'barcode' => $this->barcode ?: null,
            'name' => $this->name,
            'type' => $this->type,
            'category_id' => $this->category_id ?: null,
            'brand_id' => $this->brand_id ?: null,
            'unit_id' => $this->unit_id ?: null,
            'purchase_price' => $this->purchase_price ?: 0,
            'selling_price' => $this->selling_price ?: 0,
            'cost_price' => $this->cost_price ?: 0,
            'track_stock' => $this->track_stock,
            'allow_decimal' => $this->allow_decimal,
            'has_expiry' => $this->has_expiry,
            'is_active' => $this->is_active,
            'description' => $this->description ?: null,
        ];

        DB::transaction(function () use ($data) {
            if ($this->isEdit) {
                $product = Product::findOrFail($this->productId);
                $product->update($data);
                event(new ProductUpdated($product));

                if ($this->image) {
                    $product->addMedia($this->image->getRealPath())
                        ->usingFileName($this->image->getClientOriginalName())
                        ->toMediaCollection('main_image');
                }

                if ($this->type === 'variable') {
                    // Sync Variants
                    $keepIds = collect($this->variants)->pluck('id')->filter()->toArray();
                    $product->variants()->whereNotIn('id', $keepIds)->delete();

                    foreach ($this->variants as $v) {
                        $product->variants()->updateOrCreate(
                            ['id' => $v['id'] ?? null],
                            [
                                'tenant_id' => $product->tenant_id,
                                'name' => $v['name'],
                                'sku' => $v['sku'],
                                'selling_price' => $v['price'],
                                'cost_price' => $v['cost'] ?? 0,
                            ]
                        );
                    }
                }

                $this->dispatch('message', 'Produk Berhasil Diperbarui.');
            } else {
                $product = Product::create($data);
                event(new ProductCreated($product));

                if ($this->image) {
                    $product->addMedia($this->image->getRealPath())
                        ->usingFileName($this->image->getClientOriginalName())
                        ->toMediaCollection('main_image');
                }

                if ($this->type === 'variable') {
                    foreach ($this->variants as $v) {
                        $product->variants()->create([
                            'tenant_id' => $product->tenant_id,
                            'name' => $v['name'],
                            'sku' => $v['sku'],
                            'selling_price' => $v['price'],
                            'cost_price' => $v['cost'] ?? 0,
                        ]);
                    }
                }

                $this->dispatch('message', 'Produk Berhasil Ditambahkan.');
            }
        });

        return redirect()->route('master-data.products');
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        $categories = Cache::remember("categories_tenant_{$tenantId}", 3600, function () use ($tenantId) {
            return ProductCategory::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        });
        $categories = collect($categories)->map(fn ($c) => (object) $c);

        $brands = Cache::remember("brands_tenant_{$tenantId}", 3600, function () use ($tenantId) {
            return Brand::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        });
        $brands = collect($brands)->map(fn ($b) => (object) $b);

        $units = Cache::remember("units_tenant_{$tenantId}", 3600, function () use ($tenantId) {
            return Unit::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        });
        $units = collect($units)->map(fn ($u) => (object) $u);

        return view('livewire.master-data.product-form', [
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
        ])->layout('layouts.app');
    }
}
