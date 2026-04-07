<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Voucher;
use App\Models\CashRegisterSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $selected_category = 'all';
    public $cart = [];
    public $customer_id = '';
    public $customer_search = '';
    public $order_type = 'Take Away';
    public $tax_rate = 0.11; // 11% tax default
    public $voucher_code = '';
    public $applied_voucher = null;
    public $voucher_error = '';
    public $payment_method = 'cash';

    public $points_to_redeem = 0;
    public $points_discount = 0;
    public $discount = 0;

    public $activeSession = null;

    protected $listeners = ['refreshPOS' => '$refresh'];

    public function mount()
    {
        $this->cart = session()->get('pos_cart', []);
        $this->checkActiveSession();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function checkActiveSession()
    {
        $this->activeSession = CashRegisterSession::where('user_id', Auth::id())
            ->where('branch_id', Auth::user()->active_branch_id)
            ->where('status', 'open')
            ->first();
    }

    public function addToCart($productId)
    {
        $this->checkActiveSession();
        
        if (!$this->activeSession) {
            $this->dispatch('openCashRegister', mode: 'open');
            return;
        }

        // Force refresh from DB to ensure validity with eager loading
        $product = Product::with(['activeBranchPrice'])->find($productId);
        
        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan atau sudah dihapus.');
            return;
        }

        // Use branch price if exists, otherwise fallback to global selling price
        $price = $product->activeBranchPrice ? $product->activeBranchPrice->retail_price : $product->selling_price;
        
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id, 
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float)$price,
                'qty' => 1,
                'image' => $product->image_url,
            ];
        }
        
        $this->saveCart();
    }

    public function incrementQty($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
            $this->saveCart();
        }
    }

    public function decrementQty($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] > 1) {
                $this->cart[$productId]['qty']--;
            } else {
                unset($this->cart[$productId]);
            }
            $this->saveCart();
        }
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->saveCart();
    }

    public function resetOrder()
    {
        $this->cart = [];
        $this->voucher_code = '';
        $this->applied_voucher = null;
        $this->voucher_error = '';
        $this->points_to_redeem = 0;
        $this->points_discount = 0;
        $this->discount = 0;
        $this->saveCart();
    }

    public function applyVoucher(\App\Services\LoyaltyService $loyaltyService)
    {
        if (empty($this->voucher_code)) {
            $this->applied_voucher = null;
            $this->discount = 0;
            return;
        }

        $result = $loyaltyService->validateVoucher(
            $this->voucher_code, 
            Auth::user()->tenant_id, 
            $this->subtotal, 
            $this->customer_id ?: null,
            $this->cart
        );

        if (!$result['valid']) {
            $this->voucher_error = $result['message'];
            $this->applied_voucher = null;
            $this->discount = 0;
            return;
        }

        $this->applied_voucher = $result['voucher'];
        $this->voucher_error = '';
        
        $this->calculateDiscount($loyaltyService);

        session()->flash('success', 'Voucher berhasil digunakan!');
    }

    public function redeemPoints(\App\Services\LoyaltyService $loyaltyService)
    {
        if (!$this->customer_id) return;

        $customer = Customer::find($this->customer_id);
        if (!$customer) return;

        $pointsBalance = $customer->points_balance;

        if ($this->points_to_redeem > $pointsBalance) {
            $this->points_to_redeem = $pointsBalance;
        }

        if ($this->points_to_redeem < 0) {
            $this->points_to_redeem = 0;
        }

        // Move value per point logic to LoyaltyService
        $this->points_discount = $this->points_to_redeem * config('loyalty.value_per_point', 10);
        
        $this->calculateDiscount($loyaltyService);
    }

    private function calculateDiscount(\App\Services\LoyaltyService $loyaltyService)
    {
        $voucherDiscount = 0;
        if ($this->applied_voucher) {
            $voucherDiscount = $loyaltyService->calculateVoucherDiscount($this->applied_voucher, $this->subtotal);
        }

        $this->discount = $voucherDiscount + $this->points_discount;
    }

    public function checkout(\App\Services\SaleService $saleService)
    {
        $this->checkActiveSession();
        
        if (!$this->activeSession) {
            $this->dispatch('openCashRegister', mode: 'open');
            return;
        }

        if (count($this->cart) === 0) return;

        try {
            $sale = $saleService->checkout([
                'cart' => $this->cart,
                'customer_id' => $this->customer_id ?: null,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount' => (float)$this->discount,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'cash_register_session_id' => $this->activeSession->id,
                'paid_amount' => $this->total, 
                'voucher_id' => $this->applied_voucher?->id,
                'points_redeemed' => $this->points_to_redeem,
                'points_value' => $this->points_discount,
            ]);

            $this->resetOrder();
            $this->discount = 0;
            
            session()->flash('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function openSession()
    {
        $this->dispatch('openCashRegister', mode: 'open');
    }

    public function closeSession()
    {
        $this->dispatch('openCashRegister', mode: 'close');
    }

    private function saveCart()
    {
        session()->put('pos_cart', $this->cart);

        // Re-validate voucher if exists to ensure rules still met
        if ($this->applied_voucher) {
            $this->applyVoucher(app(\App\Services\LoyaltyService::class));
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function getTaxAmountProperty()
    {
        return $this->subtotal * $this->tax_rate;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->tax_amount - $this->discount;
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $branchId = Auth::user()->active_branch_id;

        // Optimasi: Gunakan pagination dan eager loading yang tepat
        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with(['inventories' => function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }, 'category:id,name', 'activeBranchPrice'])
            ->when($this->search, function($query) {
                $query->where(fn($q) => 
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('sku', 'ilike', '%' . $this->search . '%')
                );
            })
            ->when($this->selected_category !== 'all', fn($query) => 
                $query->where('category_id', $this->selected_category)
            )
            ->paginate(12);

        // Optimasi: Caching kategori (1 jam)
        $categories = Cache::remember("categories_tenant_{$tenantId}", 3600, fn() => 
            ProductCategory::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray()
        );
        $categories = collect($categories)->map(fn($c) => (object)$c);

        $customers = Customer::query()
            ->with(['loyaltyAccount' => function($q) {
                $q->select('id', 'customer_id', 'points_balance', 'membership_tier_id')
                  ->with('membershipTier:id,name');
            }])
            ->where('tenant_id', $tenantId)
            ->when($this->customer_search, fn($query) => 
                $query->where(fn($q) =>
                    $q->where('name', 'ilike', '%' . $this->customer_search . '%')
                      ->orWhere('phone', 'ilike', '%' . $this->customer_search . '%')
                )
            )
            ->limit(5)
            ->get(['id', 'name', 'phone']);

        return view('livewire.p-o-s.index', [
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
        ]);
    }
}
