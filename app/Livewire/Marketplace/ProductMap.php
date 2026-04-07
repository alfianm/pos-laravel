<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceProductMap;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Layout('layouts.app')]
class ProductMap extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';

    public $marketplace_filter = '';

    public $status_filter = '';

    public $showModal = false;

    public $showImportModal = false;

    public $editId = null;

    public $product_id = '';

    public $product_variant_id = '';

    public $marketplace = '';

    public $external_product_id = '';

    public $external_sku = '';

    public $external_name = '';

    public $import_marketplace = '';

    public $import_file;

    public $importPreview = [];

    public $importErrors = [];

    protected $queryString = ['search', 'marketplace_filter', 'status_filter'];

    protected function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'marketplace' => 'required|string|in:shopee,tokopedia,lazada,bukalapak,blibli',
            'external_product_id' => 'required|string|max:100',
            'external_sku' => 'nullable|string|max:150',
            'external_name' => 'nullable|string|max:255',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editId = $id;
        $map = MarketplaceProductMap::with(['product', 'variant'])->findOrFail($id);

        $this->product_id = $map->product_id;
        $this->product_variant_id = $map->product_variant_id;
        $this->marketplace = $map->marketplace;
        $this->external_product_id = $map->external_product_id;
        $this->external_sku = $map->external_sku;
        $this->external_name = $map->external_name;

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->product_id = '';
        $this->product_variant_id = '';
        $this->marketplace = '';
        $this->external_product_id = '';
        $this->external_sku = '';
        $this->external_name = '';
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id ?: null,
            'marketplace' => $this->marketplace,
            'external_product_id' => $this->external_product_id,
            'external_sku' => $this->external_sku ?: null,
            'external_name' => $this->external_name ?: null,
            'is_active' => true,
        ];

        if ($this->editId) {
            $map = MarketplaceProductMap::findOrFail($this->editId);
            $map->update($data);
            session()->flash('message', 'Mapping berhasil diperbarui.');
        } else {
            MarketplaceProductMap::create($data);
            session()->flash('message', 'Mapping berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $map = MarketplaceProductMap::findOrFail($id);
        $map->delete();
        session()->flash('message', 'Mapping berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $map = MarketplaceProductMap::findOrFail($id);
        $map->update(['is_active' => ! $map->is_active]);
        session()->flash('message', $map->is_active ? 'Mapping diaktifkan.' : 'Mapping dinonaktifkan.');
    }

    public function openImportModal()
    {
        $this->resetImportForm();
        $this->showImportModal = true;
    }

    public function resetImportForm()
    {
        $this->import_marketplace = '';
        $this->import_file = null;
        $this->importPreview = [];
        $this->importErrors = [];
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->resetImportForm();
    }

    public function updatedImportFile()
    {
        $this->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'import_marketplace' => 'required|in:shopee,tokopedia,lazada,bukalapak,blibli',
        ]);

        $this->importErrors = [];
        $this->importPreview = [];

        try {
            $path = $this->import_file->getRealPath();
            $extension = $this->import_file->getClientOriginalExtension();

            if ($extension === 'csv' || $extension === 'txt') {
                $this->parseCsv($path);
            } else {
                $this->parseExcel($path);
            }
        } catch (\Exception $e) {
            $this->importErrors[] = 'Gagal membaca file: '.$e->getMessage();
        }
    }

    protected function parseCsv($path)
    {
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 1000, ',');

        $header = array_map('strtolower', array_map('trim', $header));

        $requiredHeaders = ['sku_internal', 'external_product_id'];
        foreach ($requiredHeaders as $required) {
            if (! in_array($required, $header)) {
                $this->importErrors[] = "Kolom '$required' tidak ditemukan. Pastikan file memiliki kolom: sku_internal, external_product_id, external_sku (opsional), external_name (opsional)";
                fclose($handle);

                return;
            }
        }

        $skuIndex = array_search('sku_internal', $header);
        $externalIdIndex = array_search('external_product_id', $header);
        $externalSkuIndex = array_search('external_sku', $header);
        $externalNameIndex = array_search('external_name', $header);

        $rowNumber = 1;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNumber++;

            if (count($row) < 2) {
                continue;
            }

            $sku = trim($row[$skuIndex] ?? '');
            $externalId = trim($row[$externalIdIndex] ?? '');
            $externalSku = $externalSkuIndex !== false ? trim($row[$externalSkuIndex] ?? '') : null;
            $externalName = $externalNameIndex !== false ? trim($row[$externalNameIndex] ?? '') : null;

            if (empty($sku) || empty($externalId)) {
                continue;
            }

            $product = null;
            $variant = null;

            if (strpos($sku, '/') !== false) {
                [$productSku, $variantSku] = explode('/', $sku, 2);
                $product = Product::where('sku', trim($productSku))->first();
                if ($product) {
                    $variant = ProductVariant::where('product_id', $product->id)
                        ->where('sku', trim($variantSku))
                        ->first();
                }
            } else {
                $product = Product::where('sku', $sku)->first();
            }

            $this->importPreview[] = [
                'row' => $rowNumber,
                'sku_internal' => $sku,
                'external_product_id' => $externalId,
                'external_sku' => $externalSku,
                'external_name' => $externalName,
                'product' => $product,
                'variant' => $variant,
                'status' => $product ? 'valid' : 'not_found',
                'product_name' => $product ? $product->name : null,
            ];
        }

        fclose($handle);
    }

    protected function parseExcel($path)
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $header = array_map('strtolower', array_map('trim', $rows[0] ?? []));

        $requiredHeaders = ['sku_internal', 'external_product_id'];
        foreach ($requiredHeaders as $required) {
            if (! in_array($required, $header)) {
                $this->importErrors[] = "Kolom '$required' tidak ditemukan. Pastikan file memiliki kolom: sku_internal, external_product_id, external_sku (opsional), external_name (opsional)";

                return;
            }
        }

        $skuIndex = array_search('sku_internal', $header);
        $externalIdIndex = array_search('external_product_id', $header);
        $externalSkuIndex = array_search('external_sku', $header);
        $externalNameIndex = array_search('external_name', $header);

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $rowNumber = $i + 1;

            if (count($row) < 2) {
                continue;
            }

            $sku = trim($row[$skuIndex] ?? '');
            $externalId = trim($row[$externalIdIndex] ?? '');
            $externalSku = $externalSkuIndex !== false ? trim($row[$externalSkuIndex] ?? '') : null;
            $externalName = $externalNameIndex !== false ? trim($row[$externalNameIndex] ?? '') : null;

            if (empty($sku) || empty($externalId)) {
                continue;
            }

            $product = null;
            $variant = null;

            if (strpos($sku, '/') !== false) {
                [$productSku, $variantSku] = explode('/', $sku, 2);
                $product = Product::where('sku', trim($productSku))->first();
                if ($product) {
                    $variant = ProductVariant::where('product_id', $product->id)
                        ->where('sku', trim($variantSku))
                        ->first();
                }
            } else {
                $product = Product::where('sku', $sku)->first();
            }

            $this->importPreview[] = [
                'row' => $rowNumber,
                'sku_internal' => $sku,
                'external_product_id' => $externalId,
                'external_sku' => $externalSku,
                'external_name' => $externalName,
                'product' => $product,
                'variant' => $variant,
                'status' => $product ? 'valid' : 'not_found',
                'product_name' => $product ? $product->name : null,
            ];
        }
    }

    public function processImport()
    {
        if (empty($this->importPreview)) {
            session()->flash('error', 'Tidak ada data untuk diimport.');

            return;
        }

        if (empty($this->import_marketplace)) {
            session()->flash('error', 'Pilih marketplace terlebih dahulu.');

            return;
        }

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use (&$imported, &$skipped) {
            foreach ($this->importPreview as $item) {
                if ($item['status'] !== 'valid' || ! $item['product']) {
                    $skipped++;

                    continue;
                }

                $existing = MarketplaceProductMap::where('marketplace', $this->import_marketplace)
                    ->where('external_product_id', $item['external_product_id'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'product_id' => $item['product']->id,
                        'product_variant_id' => $item['variant']?->id,
                        'external_sku' => $item['external_sku'],
                        'external_name' => $item['external_name'],
                        'is_active' => true,
                    ]);
                } else {
                    MarketplaceProductMap::create([
                        'product_id' => $item['product']->id,
                        'product_variant_id' => $item['variant']?->id,
                        'marketplace' => $this->import_marketplace,
                        'external_product_id' => $item['external_product_id'],
                        'external_sku' => $item['external_sku'],
                        'external_name' => $item['external_name'],
                        'is_active' => true,
                    ]);
                }

                $imported++;
            }
        });

        session()->flash('message', "Import selesai: {$imported} mapping berhasil, {$skipped} dilewati.");
        $this->closeImportModal();
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-mapping-template.csv"',
        ];

        $content = "sku_internal,external_product_id,external_sku,external_name\n";
        $content .= "SKU-001,12345678,MP-SKU-001,Produk Contoh 1\n";
        $content .= "SKU-002/VR-001,87654321,MP-SKU-002,Produk Contoh dengan Varian\n";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'product-mapping-template.csv', $headers);
    }

    public function getPlatformLabel($platform)
    {
        return match ($platform) {
            'shopee' => 'Shopee',
            'tokopedia' => 'Tokopedia',
            'lazada' => 'Lazada',
            'bukalapak' => 'Bukalapak',
            'blibli' => 'Blibli',
            default => ucfirst($platform),
        };
    }

    public function getPlatformColor($platform)
    {
        return match ($platform) {
            'shopee' => 'orange',
            'tokopedia' => 'emerald',
            'lazada' => 'blue',
            'bukalapak' => 'rose',
            'blibli' => 'indigo',
            default => 'gray',
        };
    }

    public function render()
    {
        $mappings = MarketplaceProductMap::with(['product', 'variant'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('external_product_id', 'ilike', '%'.$this->search.'%')
                        ->orWhere('external_sku', 'ilike', '%'.$this->search.'%')
                        ->orWhere('external_name', 'ilike', '%'.$this->search.'%')
                        ->orWhereHas('product', function ($pq) {
                            $pq->where('name', 'ilike', '%'.$this->search.'%')
                                ->orWhere('sku', 'ilike', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->marketplace_filter, function ($query) {
                $query->where('marketplace', $this->marketplace_filter);
            })
            ->when($this->status_filter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->status_filter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->latest()
            ->paginate(15);

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        $variants = [];
        if ($this->product_id) {
            $variants = ProductVariant::where('product_id', $this->product_id)->get();
        }

        return view('livewire.marketplace.product-map', [
            'mappings' => $mappings,
            'products' => $products,
            'variants' => $variants,
        ]);
    }
}
