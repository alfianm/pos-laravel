<?php
declare(strict_types=1);
namespace App\Services;

use App\Constants\InvoiceStatus;
use App\Constants\PaymentStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Tenant;
use App\Events\InvoiceGenerated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function createFromSale(Sale $sale, array $data): Invoice
    {
        return DB::transaction(function () use ($sale, $data) {
            $invoice = Invoice::create([
                'tenant_id' => $sale->tenant_id,
                'branch_id' => $sale->branch_id,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'invoice_number' => $this->generateInvoiceNumber($sale->branch_id),
                'invoice_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'] ?? now()->addDays(14),
                'subtotal' => $sale->subtotal,
                'tax_amount' => $sale->tax_amount,
                'discount_amount' => $sale->discount_amount,
                'total_amount' => $sale->total_amount,
                'paid_amount' => 0,
                'balance_due' => $sale->total_amount,
                'status' => InvoiceStatus::SENT,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($sale->items as $saleItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $saleItem->product_id,
                    'description' => $saleItem->product?->name ?? 'Produk',
                    'quantity' => $saleItem->quantity,
                    'unit_price' => $saleItem->unit_price,
                    'discount_amount' => $saleItem->discount_amount,
                    'line_total' => $saleItem->total_price,
                ]);
            }

            $tenant = Tenant::find($invoice->tenant_id);
            if ($tenant) {
                event(new InvoiceGenerated($tenant, $invoice->toArray()));
            }

            return $invoice;
        });
    }

    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $payment = Payment::create([
                'tenant_id' => $invoice->tenant_id,
                'branch_id' => $invoice->branch_id,
                'invoice_id' => $invoice->id,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date' => $data['payment_date'] ?? now(),
                'status' => PaymentStatus::COMPLETED->value,
                'notes' => $data['notes'] ?? null,
                'processed_by' => auth()->id(),
            ]);

            $this->updateInvoiceStatus($invoice);

            return $payment;
        });
    }

    public function updateInvoiceStatus(Invoice $invoice): void
    {
        $totalPaid = $invoice->payments()
            ->where('status', PaymentStatus::COMPLETED)
            ->sum('amount');

        $invoice->paid_amount = $totalPaid;
        $invoice->balance_due = $invoice->total_amount - $totalPaid;

        if ($invoice->balance_due <= 0) {
            $invoice->status = InvoiceStatus::PAID;
            $invoice->paid_at = now();
        } elseif ($totalPaid > 0) {
            $invoice->status = InvoiceStatus::PARTIAL;
        } else {
            $invoice->status = InvoiceStatus::SENT;
        }

        if ($invoice->due_date < now() && $invoice->status !== InvoiceStatus::PAID) {
            $invoice->status = InvoiceStatus::OVERDUE;
        }

        $invoice->save();
    }

    public function cancelInvoice(Invoice $invoice, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($invoice, $reason) {
            if ($invoice->status === InvoiceStatus::PAID) {
                return false;
            }

            $invoice->payments()->update(['status' => PaymentStatus::CANCELLED->value]);

            $invoice->update([
                'status' => InvoiceStatus::CANCELLED,
                'notes' => $reason ? ($invoice->notes . "\n[Dibatalkan]: " . $reason) : $invoice->notes,
            ]);

            return true;
        });
    }

    private function generateInvoiceNumber(int $branchId): string
    {
        $prefix = 'INV';
        $branchCode = str_pad((string) $branchId, 3, '0', STR_PAD_LEFT);
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return "{$prefix}-{$branchCode}-{$date}-{$random}";
    }
}
