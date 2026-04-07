<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportService extends BaseImportService
{
    /**
     * Process a single row of data for Product.
     */
    protected function processRow(array $data, int $rowNumber)
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $this->batch->tenant_id;

            // 1. Resolve Category
            $categoryId = null;
            if (!empty($data['category'])) {
                $category = ProductCategory::firstOrCreate(
                    ['tenant_id' => $tenantId, 'name' => $data['category']],
                    ['id' => (string) Str::uuid(), 'slug' => Str::slug($data['category'])]
                );
                $categoryId = $category->id;
            }

            // 2. Resolve Brand
            $brandId = null;
            if (!empty($data['brand'])) {
                $brand = Brand::firstOrCreate(
                    ['tenant_id' => $tenantId, 'name' => $data['brand']],
                    ['id' => (string) Str::uuid(), 'slug' => Str::slug($data['brand'])]
                );
                $brandId = $brand->id;
            }

            // 3. Resolve Unit
            $unitId = null;
            if (!empty($data['unit'])) {
                $unit = Unit::firstOrCreate(
                    ['tenant_id' => $tenantId, 'name' => $data['unit']],
                    ['id' => (string) Str::uuid(), 'short_name' => strtolower($data['unit'])]
                );
                $unitId = $unit->id;
            }

            // 4. Create or Update Product
            $product = Product::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'sku' => $data['sku'] ?? $data['kode_barang'] ?? null
                ],
                [
                    'name' => $data['name'] ?? $data['nama_barang'],
                    'barcode' => $data['barcode'] ?? null,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'unit_id' => $unitId,
                    'cost_price' => $data['cost_price'] ?? $data['harga_modal'] ?? 0,
                    'selling_price' => $data['selling_price'] ?? $data['harga_jual'] ?? 0,
                    'description' => $data['description'] ?? null,
                    'is_active' => true,
                    'track_stock' => $data['track_stock'] ?? true,
                ]
            );

            return $product;
        });
    }
}
