<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use App\Constants\AccountCategoryType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportService
{
    /**
     * Get Profit & Loss data for a specific period.
     * 
     * @param string $tenantId
     * @param string $startDate
     * @param string $endDate
     * @param string|null $branchId
     * @return array
     */
    public function getProfitAndLoss(string $tenantId, string $startDate, string $endDate, ?string $branchId = null): array
    {
        // 1. Get Revenues
        $revenues = $this->getCategoryGroupTotals($tenantId, AccountCategoryType::REVENUE->value, $startDate, $endDate, $branchId);
        $totalRevenue = array_sum(array_column($revenues, 'total'));

        // 2. Get COGS (specific expenses related to sales)
        // Usually, COGS is in Category 'Beban Pokok Penjualan' (code 5100 series)
        $cogs = $this->getAccountGroupTotals($tenantId, '5101', $startDate, $endDate, $branchId);
        $totalCogs = array_sum(array_column($cogs, 'total'));

        $grossProfit = $totalRevenue - $totalCogs;

        // 3. Get Other Expenses
        $expenses = $this->getCategoryGroupTotals($tenantId, AccountCategoryType::EXPENSE->value, $startDate, $endDate, $branchId);
        // Exclude COGS if it was already included in category expenses
        $otherExpenses = array_filter($expenses, fn($e) => substr((string) ($e['code'] ?? ''), 0, 2) !== '51');
        $totalExpenses = array_sum(array_column($otherExpenses, 'total'));

        $netProfit = $grossProfit - $totalExpenses;

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'revenues' => $revenues,
            'total_revenue' => $totalRevenue,
            'cogs' => $cogs,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'expenses' => $otherExpenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Get Balance Sheet data as of a specific date.
     * 
     * @param string $tenantId
     * @param string $asOfDate
     * @param string|null $branchId
     * @return array
     */
    public function getBalanceSheet(string $tenantId, string $asOfDate, ?string $branchId = null): array
    {
        $assets = $this->getCategoryGroupTotals($tenantId, AccountCategoryType::ASSET->value, null, $asOfDate, $branchId);
        $liabilities = $this->getCategoryGroupTotals($tenantId, AccountCategoryType::LIABILITY->value, null, $asOfDate, $branchId);
        $equity = $this->getCategoryGroupTotals($tenantId, AccountCategoryType::EQUITY->value, null, $asOfDate, $branchId);

        return [
            'as_of_date' => $asOfDate,
            'assets' => $assets,
            'total_assets' => array_sum(array_column($assets, 'total')),
            'liabilities' => $liabilities,
            'total_liabilities' => array_sum(array_column($liabilities, 'total')),
            'equity' => $equity,
            'total_equity' => array_sum(array_column($equity, 'total')),
        ];
    }

    /**
     * Helper to get totals grouped by account code within a category.
     */
    private function getCategoryGroupTotals(string $tenantId, int $categoryType, ?string $start, string $end, ?string $branchId): array
    {
        $query = JournalEntryLine::query()
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.tenant_id', $tenantId)
            ->where('chart_of_accounts.type', (string) $categoryType)
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '<=', $end);

        if ($start) {
            $query->where('journal_entries.entry_date', '>=', $start);
        }

        if ($branchId) {
            $query->where('journal_entries.branch_id', $branchId);
        }

        // Calculation logic depends on category type
        // Assets/Expenses: Debit - Credit
        // Liabilities/Equity/Revenue: Credit - Debit
        $isDebitNormal = in_array($categoryType, [AccountCategoryType::ASSET->value, AccountCategoryType::EXPENSE->value]);
        $sumExpression = $isDebitNormal ? 'SUM(debit - credit)' : 'SUM(credit - debit)';

        if ($isDebitNormal) {
            $query->select(
                'chart_of_accounts.id as account_id',
                'chart_of_accounts.account_code as code',
                'chart_of_accounts.account_name as name',
                DB::raw('SUM(debit - credit) as total')
            );
        } else {
            $query->select(
                'chart_of_accounts.id as account_id',
                'chart_of_accounts.account_code as code',
                'chart_of_accounts.account_name as name',
                DB::raw('SUM(credit - debit) as total')
            );
        }

        return $query->groupBy('chart_of_accounts.id', 'chart_of_accounts.account_code', 'chart_of_accounts.account_name')
            ->havingRaw("{$sumExpression} <> 0")
            ->orderBy('chart_of_accounts.account_code')
            ->get()
            ->toArray();
    }

    /**
     * Helper to get totals for a specific account code + its sub-accounts.
     */
    private function getAccountGroupTotals(string $tenantId, string $parentCode, ?string $start, string $end, ?string $branchId): array
    {
        $query = JournalEntryLine::query()
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.tenant_id', $tenantId)
            ->where('chart_of_accounts.account_code', 'like', $parentCode . '%')
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '<=', $end);

        if ($start) {
            $query->where('journal_entries.entry_date', '>=', $start);
        }

        if ($branchId) {
            $query->where('journal_entries.branch_id', $branchId);
        }

        // Most accounts in 5xxx are Debit normal (expenses)
        $query->select(
            'chart_of_accounts.id as account_id',
            'chart_of_accounts.account_code as code',
            'chart_of_accounts.account_name as name',
            DB::raw('SUM(debit - credit) as total')
        );

        return $query->groupBy('chart_of_accounts.id', 'chart_of_accounts.account_code', 'chart_of_accounts.account_name')
            ->havingRaw('SUM(debit - credit) <> 0')
            ->orderBy('chart_of_accounts.account_code')
            ->get()
            ->toArray();
    }

    /**
     * Get Cash Flow data for a specific period (Direct Method).
     * 
     * @param string $tenantId
     * @param string $startDate
     * @param string $endDate
     * @param string|null $branchId
     * @return array
     */
    public function getCashFlow(string $tenantId, string $startDate, string $endDate, ?string $branchId = null): array
    {
        // Identify Cash and Bank accounts
        $cashAccountIds = ChartOfAccount::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('account_code', 'like', '1101%') // Kas
                  ->orWhere('account_code', 'like', '1102%'); // Bank
            })
            ->pluck('id')
            ->toArray();

        if (empty($cashAccountIds)) {
            return [
                'operating' => ['in' => [], 'out' => [], 'net' => 0],
                'investing' => ['in' => [], 'out' => [], 'net' => 0],
                'financing' => ['in' => [], 'out' => [], 'net' => 0],
                'net_cash_flow' => 0,
                'opening_balance' => 0,
                'closing_balance' => 0,
            ];
        }

        // Opening Balance
        $openingBalance = JournalEntryLine::join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('account_id', $cashAccountIds)
            ->where('journal_entries.entry_date', '<', $startDate)
            ->where('journal_entries.is_posted', true)
            ->sum(DB::raw('debit - credit'));

        // Cash Inflows (Debit to Cash) - Group by opposing account category
        $inflows = DB::table('journal_entry_lines as l1')
            ->join('journal_entries as j', 'l1.journal_entry_id', '=', 'j.id')
            ->join('journal_entry_lines as l2', 'l1.journal_entry_id', '=', 'l2.journal_entry_id')
            ->join('chart_of_accounts as a2', 'l2.account_id', '=', 'a2.id')
            ->whereIn('l1.account_id', $cashAccountIds)
            ->where('l1.debit', '>', 0)
            ->whereNotIn('l2.account_id', $cashAccountIds) // Opposing side
            ->where('j.entry_date', '>=', $startDate)
            ->where('j.entry_date', '<=', $endDate)
            ->where('j.is_posted', true)
            ->when($branchId, fn($q) => $q->where('j.branch_id', $branchId))
            ->select('a2.type', 'a2.account_name', DB::raw('SUM(l1.debit) as total'))
            ->groupBy('a2.type', 'a2.account_name')
            ->get();

        // Cash Outflows (Credit to Cash)
        $outflows = DB::table('journal_entry_lines as l1')
            ->join('journal_entries as j', 'l1.journal_entry_id', '=', 'j.id')
            ->join('journal_entry_lines as l2', 'l1.journal_entry_id', '=', 'l2.journal_entry_id')
            ->join('chart_of_accounts as a2', 'l2.account_id', '=', 'a2.id')
            ->whereIn('l1.account_id', $cashAccountIds)
            ->where('l1.credit', '>', 0)
            ->whereNotIn('l2.account_id', $cashAccountIds) // Opposing side
            ->where('j.entry_date', '>=', $startDate)
            ->where('j.entry_date', '<=', $endDate)
            ->where('j.is_posted', true)
            ->when($branchId, fn($q) => $q->where('j.branch_id', $branchId))
            ->select('a2.type', 'a2.account_name', DB::raw('SUM(l1.credit) as total'))
            ->groupBy('a2.type', 'a2.account_name')
            ->get();

        // Group by Operating, Investing, Financing
        $report = [
            'operating' => ['in' => [], 'out' => [], 'net' => 0],
            'investing' => ['in' => [], 'out' => [], 'net' => 0],
            'financing' => ['in' => [], 'out' => [], 'net' => 0],
        ];

        foreach ($inflows as $in) {
            $category = $this->classifyCashFlow((int)$in->type);
            $report[$category]['in'][] = ['name' => $in->account_name, 'total' => (float)$in->total];
            $report[$category]['net'] += (float)$in->total;
        }

        foreach ($outflows as $out) {
            $category = $this->classifyCashFlow((int)$out->type);
            $report[$category]['out'][] = ['name' => $out->account_name, 'total' => (float)$out->total];
            $report[$category]['net'] -= (float)$out->total;
        }

        $netCashFlow = $report['operating']['net'] + $report['investing']['net'] + $report['financing']['net'];

        return array_merge($report, [
            'opening_balance' => (float)$openingBalance,
            'net_cash_flow' => $netCashFlow,
            'closing_balance' => (float)$openingBalance + $netCashFlow,
        ]);
    }

    private function classifyCashFlow(int $accountType): string
    {
        return match ($accountType) {
            AccountCategoryType::REVENUE->value, AccountCategoryType::EXPENSE->value => 'operating',
            AccountCategoryType::ASSET->value => 'investing',
            AccountCategoryType::LIABILITY->value, AccountCategoryType::EQUITY->value => 'financing',
            default => 'operating',
        };
    }
}
