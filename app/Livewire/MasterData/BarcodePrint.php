<?php

namespace App\Livewire\MasterData;

use App\Models\Product;
use App\Models\BarcodeLabelTemplate;
use App\Services\BarcodeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class BarcodePrint extends Component
{
    use AuthorizesRequests, WithPagination;

    public array $selectedProducts = [];
    public ?string $selectedTemplateId = null;
    public int $quantityPerProduct = 1;
    public bool $showPreview = false;
    public bool $selectAll = false;
    public string $search = '';
    public string $categoryFilter = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    protected BarcodeService $barcodeService;

    public function boot(BarcodeService $barcodeService): void
    {
        $this->barcodeService = $barcodeService;
    }

    public function mount(): void
    {
        $this->authorize('products.view');
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedProducts = $this->getFilteredProductsQuery()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedProducts = [];
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedProducts = [];
    }

    public function toggleProductSelection(string $productId): void
    {
        if (in_array($productId, $this->selectedProducts)) {
            $this->selectedProducts = array_diff($this->selectedProducts, [$productId]);
        } else {
            $this->selectedProducts[] = $productId;
        }
        $this->selectAll = false;
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getFilteredProductsQuery()
    {
        return Product::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('sku', 'ilike', '%' . $this->search . '%')
                        ->orWhere('barcode', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->where('is_active', true)
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function getProductsProperty()
    {
        return $this->getFilteredProductsQuery()->paginate(20);
    }

    public function getCategoriesProperty()
    {
        return \App\Models\Category::active()->orderBy('name')->get();
    }

    public function getLabelTemplatesProperty()
    {
        return BarcodeLabelTemplate::active()
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function getSelectedProductsCountProperty(): int
    {
        return count($this->selectedProducts);
    }

    public function openPreview(): void
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih minimal 1 produk untuk print label');
            return;
        }

        if (!$this->selectedTemplateId) {
            // Use default template
            $defaultTemplate = BarcodeLabelTemplate::default()->first();
            if (!$defaultTemplate) {
                $this->dispatch('notify', type: 'error', message: 'Tidak ada template label yang tersedia');
                return;
            }
            $this->selectedTemplateId = $defaultTemplate->id;
        }

        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    public function generatePrintData(): array
    {
        if (empty($this->selectedProducts)) {
            return [];
        }

        $products = Product::whereIn('id', $this->selectedProducts)
            ->where('is_active', true)
            ->get();

        $template = BarcodeLabelTemplate::find($this->selectedTemplateId);

        if (!$template) {
            $template = BarcodeLabelTemplate::default()->first();
        }

        $labels = [];
        foreach ($products as $product) {
            for ($i = 0; $i < $this->quantityPerProduct; $i++) {
                $labels[] = [
                    'product' => $product,
                    'html' => $this->barcodeService->generateLabelHTML($product, $template),
                    'barcode' => $product->barcode ?? $product->sku ?? $product->id,
                ];
            }
        }

        return [
            'labels' => $labels,
            'template' => $template,
            'totalCount' => count($labels),
        ];
    }

    public function printLabels(): void
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih minimal 1 produk untuk print label');
            return;
        }

        $this->dispatch('trigger-print');
    }

    public function clearSelection(): void
    {
        $this->selectedProducts = [];
        $this->selectAll = false;
    }

    public function render()
    {
        return view('livewire.master-data.barcode-print', [
            'products' => $this->products,
            'categories' => $this->categories,
            'labelTemplates' => $this->labelTemplates,
            'selectedCount' => $this->selectedProductsCount,
        ])->layout('components.layouts.app');
    }
}
