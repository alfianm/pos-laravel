<?php

namespace App\Services;

use App\Events\SaleCreated;
use App\Models\CashRegisterSession;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    protected $stockService;

    protected $loyaltyService;

    public function __construct(StockService $stockService, LoyaltyService $loyaltyService)
    {
        $this->stockService = $stockService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Complete the checkout process.
     */
    public function checkout(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $tenantId = $user->tenant_id;
            $branchId = $user->active_branch_id;

            if (! $tenantId || ! $branchId) {
                throw new \Exception('User tidak terhubung ke Tenant atau Cabang aktif.');
            }

            // 1. Create Sale Header
            $sale = Sale::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'created_by' => $user->id,
                'customer_id' => $data['customer_id'] ?? null,
                'cash_register_session_id' => $data['cash_register_session_id'] ?? null,
                'sale_no' => $this->generateSaleNo($tenantId),
                'sale_date' => now(),
                'status' => 'completed',
                'payment_status' => 'paid',
                'subtotal' => $data['subtotal'],
                'discount_amount' => $data['discount'] ?? 0,
                'tax_amount' => $data['tax_amount'],
                'grand_total' => $data['total'],
                'paid_amount' => $data['paid_amount'] ?? $data['total'],
                'due_amount' => max(0, $data['total'] - ($data['paid_amount'] ?? $data['total'])),
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Create Sale Items & Update Stock
            foreach ($data['cart'] as $item) {
                $product = Product::find($item['id']);

                SaleItem::create([
                    'tenant_id' => $tenantId,
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'line_total' => $item['price'] * $item['qty'],
                ]);

                // Record Stock Movement (Reduction)
                $this->stockService->recordMovement([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'] * -1,
                    'movement_type' => 'sale',
                    'reference_type' => 'Sale',
                    'reference_id' => $sale->id,
                    'reference_no' => $sale->sale_no,
                    'performed_by' => $user->id,
                ]);
            }

            // 3. Create Payment Record
            $paymentMethod = $data['payment_method'] ?? 'cash';
            SalePayment::create([
                'tenant_id' => $tenantId,
                'sale_id' => $sale->id,
                'amount' => $data['paid_amount'] ?? $data['total'],
                'payment_method' => $paymentMethod,
                'payment_no' => 'PAY-'.strtoupper(Str::random(8)),
                'payment_date' => now(),
                'reference_no' => null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            if (isset($data['cash_register_session_id'])) {
                $session = CashRegisterSession::find($data['cash_register_session_id']);
                if ($session) {
                    if ($paymentMethod === 'cash') {
                        $session->increment('total_cash_sales', $data['total']);
                    } else {
                        $session->increment('total_non_cash_sales', $data['total']);
                    }
                }
            }

            // 4. Update Customer Total Spent
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $customer->increment('total_spent', (float) $sale->grand_total);
                    $customer->update(['last_purchase_date' => now()]);
                }
            }

            // 5. Award Loyalty Points (if loyalty is enabled)
            if (config('loyalty.enabled', false)) {
                $this->loyaltyService->awardPointsForSale($sale);
            }

            // 6. Dispatch SaleCreated event (triggers timeline, etc.)
            event(new SaleCreated($sale));

            return $sale;
        });
    }

    /**
     * Generate a unique sale number.
     */
    private function generateSaleNo($tenantId)
    {
        $prefix = 'INV/'.date('Y/m/d');
        $count = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', date('Y-m-d'))
            ->count();

        return $prefix.'/'.str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
