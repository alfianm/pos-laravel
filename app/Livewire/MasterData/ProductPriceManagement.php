<?php

namespace App\Livewire\MasterData;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBranchPrice;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductPriceManagement extends Component
{
    public $product;
    public $branches;
    public $prices = []; // [branch_id => [retail_price, wholesale_price, member_price]]

    public function mount($product)
    {
        $this->product = Product::with(['variants'])->find($product);
        $this->branches = Branch::where('status', 'active')->get();
        
        $existingPrices = ProductBranchPrice::where('product_id', $this->product->id)->get();
        
        foreach ($this->branches as $branch) {
            $price = $existingPrices->where('branch_id', $branch->id)->first();
            $this->prices[$branch->id] = [
                'retail_price' => $price ? (float)$price->retail_price : (float)$this->product->price,
                'wholesale_price' => $price ? (float)$price->wholesale_price : 0,
                'member_price' => $price ? (float)$price->member_price : 0,
            ];
        }
    }

    public function save()
    {
        foreach ($this->prices as $branchId => $data) {
            ProductBranchPrice::updateOrCreate(
                [
                    'tenant_id' => auth()->user()->tenant_id,
                    'branch_id' => $branchId,
                    'product_id' => $this->product->id,
                    'product_variant_id' => null, // Multi-variant price per variant coming next if needed
                ],
                $data
            );
        }

        session()->flash('message', 'Harga cabang berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.master-data.product-price-management');
    }
}
