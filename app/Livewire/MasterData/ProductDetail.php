<?php

namespace App\Livewire\MasterData;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductDetail extends Component
{
    public Product $product;
    public $activeTab = 'info';

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'brand', 'unit', 'variants', 'inventories.branch']);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.master-data.product-detail');
    }
}
