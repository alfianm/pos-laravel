<?php

namespace App\Services;

use App\Models\ArApRecord;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ArApService
{
    /**
     * Record a sale as accounts receivable.
     */
    public function recordReceivable(Sale $sale, string $dueDate, string $notes = null): ArApRecord
    {
        return ArApRecord::create([
            'tenant_id' => $sale->tenant_id,
            'branch_id' => $sale->branch_id,
            'type' => 'ar',
            'entity_id' => $sale->customer_id,
            'entity_type' => Customer::class,
            'transaction_id' => $sale->id,
            'transaction_type' => Sale::class,
            'reference_number' => $sale->sale_no,
            'transaction_date' => $sale->sale_date,
            'due_date' => $dueDate,
            'total_amount' => $sale->grand_total,
            'paid_amount' => 0,
            'balance_amount' => $sale->grand_total,
            'status' => 'outstanding',
            'days_overdue' => 0,
            'notes' => $notes,
            'created_by' => $sale->created_by,
        ]);
    }

    /**
     * Record a payment against a receivable.
     */
    public function recordReceivablePayment(ArApRecord $record, float $amount, string $notes = null): void
    {
        if ($record->type !== 'ar') {
            throw new \InvalidArgumentException('Record must be an accounts receivable');
        }

        $record->recordPayment($amount, $notes);
    }

    /**
     * Get AR summary for a customer.
     */
    public function getCustomerReceivablesSummary(Customer $customer): array
    {
        $records = ArApRecord::where('type', 'ar')
            ->where('entity_id', $customer->id)
            ->where('entity_type', Customer::class)
            ->where('tenant_id', $customer->tenant_id)
            ->get();

        return [
            'total_invoices' => $records->count(),
            'total_amount' => $records->sum('total_amount'),
            'total_paid' => $records->sum('paid_amount'),
            'total_outstanding' => $records->sum('balance_amount'),
            'outstanding_count' => $records->where('status', 'outstanding')->count(),
            'partial_count' => $records->where('status', 'partial')->count(),
            'overdue_count' => $records->where('status', 'overdue')->count(),
            'paid_count' => $records->where('status', 'paid')->count(),
            'oldest_due_date' => $records->where('status', '!=', 'paid')->min('due_date'),
            'records' => $records,
        ];
    }

    /**
     * Get AP summary for a supplier.
     */
    public function getSupplierPayablesSummary($supplier): array
    {
        $records = ArApRecord::where('type', 'ap')
            ->where('entity_id', $supplier->id)
            ->where('entity_type', get_class($supplier))
            ->where('tenant_id', $supplier->tenant_id)
            ->get();

        return [
            'total_bills' => $records->count(),
            'total_amount' => $records->sum('total_amount'),
            'total_paid' => $records->sum('paid_amount'),
            'total_outstanding' => $records->sum('balance_amount'),
            'outstanding_count' => $records->where('status', 'outstanding')->count(),
            'partial_count' => $records->where('status', 'partial')->count(),
            'overdue_count' => $records->where('status', 'overdue')->count(),
            'paid_count' => $records->where('status', 'paid')->count(),
            'oldest_due_date' => $records->where('status', '!=', 'paid')->min('due_date'),
            'records' => $records,
        ];
    }

    /**
     * Get AR aging report data.
     */
    public function getReceivablesAging(?string $branchId = null): array
    {
        $query = ArApRecord::where('type', 'ar')
            ->whereIn('status', ['outstanding', 'partial', 'overdue'])
            ->with(['entity']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $records = $query->get();

        $aging = [
            'current' => ['count' => 0, 'amount' => 0],
            '1-30' => ['count' => 0, 'amount' => 0],
            '31-60' => ['count' => 0, 'amount' => 0],
            '61-90' => ['count' => 0, 'amount' => 0],
            '90+' => ['count' => 0, 'amount' => 0],
            'total' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($records as $record) {
            $bucket = $record->aging_bucket;
            $aging[$bucket]['count']++;
            $aging[$bucket]['amount'] += $record->balance_amount;
            $aging['total']['count']++;
            $aging['total']['amount'] += $record->balance_amount;
        }

        return $aging;
    }

    /**
     * Get AP aging report data.
     */
    public function getPayablesAging(?string $branchId = null): array
    {
        $query = ArApRecord::where('type', 'ap')
            ->whereIn('status', ['outstanding', 'partial', 'overdue'])
            ->with(['entity']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $records = $query->get();

        $aging = [
            'current' => ['count' => 0, 'amount' => 0],
            '1-30' => ['count' => 0, 'amount' => 0],
            '31-60' => ['count' => 0, 'amount' => 0],
            '61-90' => ['count' => 0, 'amount' => 0],
            '90+' => ['count' => 0, 'amount' => 0],
            'total' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($records as $record) {
            $bucket = $record->aging_bucket;
            $aging[$bucket]['count']++;
            $aging[$bucket]['amount'] += $record->balance_amount;
            $aging['total']['count']++;
            $aging['total']['amount'] += $record->balance_amount;
        }

        return $aging;
    }

    /**
     * Update all AR/AP records statuses (run periodically).
     */
    public function updateAllStatuses(): int
    {
        $count = 0;
        $records = ArApRecord::whereIn('status', ['outstanding', 'partial'])
            ->where('due_date', '<', now())
            ->chunk(100, function (Collection $chunk) use (&$count) {
                foreach ($chunk as $record) {
                    $record->updateStatus();
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Get total AR/AP summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        $tenantId = app(\App\Services\TenantManager::class)->getTenantId();

        if (!$tenantId) {
            return [
                'ar_total' => 0,
                'ar_overdue' => 0,
                'ap_total' => 0,
                'ap_overdue' => 0,
            ];
        }

        $arData = ArApRecord::where('type', 'ar')
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['outstanding', 'partial', 'overdue'])
            ->select(
                DB::raw('SUM(balance_amount) as total'),
                DB::raw("SUM(CASE WHEN status = 'overdue' THEN balance_amount ELSE 0 END) as overdue")
            )
            ->first();

        $apData = ArApRecord::where('type', 'ap')
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['outstanding', 'partial', 'overdue'])
            ->select(
                DB::raw('SUM(balance_amount) as total'),
                DB::raw("SUM(CASE WHEN status = 'overdue' THEN balance_amount ELSE 0 END) as overdue")
            )
            ->first();

        return [
            'ar_total' => $arData?->total ?? 0,
            'ar_overdue' => $arData?->overdue ?? 0,
            'ap_total' => $apData?->total ?? 0,
            'ap_overdue' => $apData?->overdue ?? 0,
        ];
    }

    /**
     * Get top overdue AR records.
     */
    public function getTopOverdueReceivables(int $limit = 10): Collection
    {
        return ArApRecord::where('type', 'ar')
            ->where('status', 'overdue')
            ->with(['entity'])
            ->orderByDesc('days_overdue')
            ->orderByDesc('balance_amount')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top overdue AP records.
     */
    public function getTopOverduePayables(int $limit = 10): Collection
    {
        return ArApRecord::where('type', 'ap')
            ->where('status', 'overdue')
            ->with(['entity'])
            ->orderByDesc('days_overdue')
            ->orderByDesc('balance_amount')
            ->limit($limit)
            ->get();
    }
}