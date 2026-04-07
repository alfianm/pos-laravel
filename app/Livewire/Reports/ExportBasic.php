<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class ExportBasic extends Component
{
    public function exportSales()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sales_report_' . date('Y-m-d') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $sales = Sale::with(['branch', 'customer', 'cashier'])->latest()->get();

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Transaksi', 'Cabang', 'Customer', 'Kasir', 'Total', 'Payment', 'Tanggal']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_no,
                    $sale->branch->name,
                    $sale->customer->name ?? 'Walk-in',
                    $sale->cashier->name,
                    $sale->total,
                    $sale->payment_method,
                    $sale->created_at->format('Y-m-d H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportInventory()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=inventory_report_' . date('Y-m-d') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $inventories = Inventory::with(['branch', 'product', 'variant'])->get();

        $callback = function() use ($inventories) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Cabang', 'Produk', 'Varian', 'SKU', 'Stok Tersedia']);

            foreach ($inventories as $inv) {
                fputcsv($file, [
                    $inv->branch->name,
                    $inv->product->name,
                    $inv->variant->name ?? '-',
                    $inv->variant ? $inv->variant->sku : $inv->product->sku,
                    $inv->qty_on_hand
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.reports.export-basic');
    }
}
