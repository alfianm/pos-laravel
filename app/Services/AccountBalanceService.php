<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountBalance;
use App\Models\ChartOfAccount;
use App\Constants\NormalBalance;
use Illuminate\Support\Facades\Log;

class AccountBalanceService
{
    /**
     * Update account balance for a given period.
     *
     * @param string $tenantId
     * @param string $accountId
     * @param string $date
     * @param float $debit
     * @param float $credit
     * @return AccountBalance
     */
    public function updateBalance(string $tenantId, string $accountId, string $date, float $debit, float $credit): AccountBalance
    {
        $period = date('Y-m', strtotime($date));

        $balance = AccountBalance::firstOrNew([
            'tenant_id' => $tenantId,
            'account_id' => $accountId,
            'period_month' => $period,
            'branch_id' => null, // Company wide for now, can be expanded
        ]);

        if (!$balance->exists) {
            // Get previous period balance as opening balance
            $previousPeriod = date('Y-m', strtotime($date . ' -1 month'));
            $previousBalance = AccountBalance::where('tenant_id', $tenantId)
                ->where('account_id', $accountId)
                ->where('period_month', $previousPeriod)
                ->first();

            $balance->balance_date = $date;
            $balance->opening_balance = $previousBalance ? $previousBalance->closing_balance : 0;
            $balance->debit_movement = 0;
            $balance->credit_movement = 0;
        }

        $balance->debit_movement += $debit;
        $balance->credit_movement += $credit;
        $balance->calculateClosingBalance();
        $balance->save();

        // Also update the global current_balance on the account itself
        $account = ChartOfAccount::find($accountId);
        if ($account) {
            $account->updateBalance($debit, $credit);
        }

        return $balance;
    }

    /**
     * Get account balance at a specific date.
     *
     * @param string $accountId
     * @param string $asOfDate
     * @return float
     */
    public function getBalanceAsOf(string $accountId, string $asOfDate): float
    {
        $period = date('Y-m', strtotime($asOfDate));

        // Get balance up to the period
        $balance = AccountBalance::where('account_id', $accountId)
            ->where('period_month', '<=', $period)
            ->orderBy('period_month', 'desc')
            ->first();

        return $balance ? (float)$balance->closing_balance : 0;
    }

    /**
     * Recalculate all account balances for a tenant.
     *
     * @param string $tenantId
     * @param string|null $fromPeriod
     * @return void
     */
    public function recalculateBalances(string $tenantId, ?string $fromPeriod = null): void
    {
        $query = \App\Models\JournalEntry::where('tenant_id', $tenantId)
            ->where('is_posted', true);

        if ($fromPeriod) {
            $query->whereRaw("TO_CHAR(entry_date, 'YYYY-MM') >= ?", [$fromPeriod]);
        }

        $entries = $query->with('lines')->get();

        // Reset balances
        AccountBalance::where('tenant_id', $tenantId)
            ->where('period_month', '>=', $fromPeriod ?? '2000-01')
            ->delete();

        foreach ($entries as $entry) {
            foreach ($entry->lines as $line) {
                $this->updateBalance(
                    $tenantId,
                    $line->account_id,
                    $entry->entry_date->format('Y-m-d'),
                    (float)$line->debit,
                    (float)$line->credit
                );
            }
        }

        Log::info('Account balances recalculated', [
            'tenant_id' => $tenantId,
            'from_period' => $fromPeriod,
            'entries_processed' => $entries->count(),
        ]);
    }
}
