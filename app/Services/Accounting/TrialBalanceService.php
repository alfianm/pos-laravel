<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\AccountBalance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrialBalanceService
{
    /**
     * Get trial balance data for a specific period.
     *
     * @param string $tenantId
     * @param string $period (Format: Y-m)
     * @param string|null $branchId
     * @return Collection
     */
    public function getTrialBalance(string $tenantId, string $period, ?string $branchId = null): Collection
    {
        // Get all accounts with their balances for the period
        $accounts = ChartOfAccount::where('tenant_id', $tenantId)
            ->with(['balances' => function ($query) use ($period) {
                $query->where('period', $period);
            }])
            ->orderBy('account_code')
            ->get();

        return $accounts->map(function (ChartOfAccount $account) {
            $balance = $account->balances->first();

            return [
                'account_id' => $account->id,
                'code' => $account->account_code,
                'name' => $account->account_name,
                'opening_balance' => $balance ? $balance->opening_balance : 0,
                'debit' => $balance ? $balance->debit : 0,
                'credit' => $balance ? $balance->credit : 0,
                'closing_balance' => $balance ? $balance->closing_balance : 0,
                // For Trial Balance display:
                'display_debit' => $this->getDisplayDebit($account, $balance),
                'display_credit' => $this->getDisplayCredit($account, $balance),
            ];
        });
    }

    /**
     * Determine if closing balance should be shown in Debit column of Trial Balance.
     */
    private function getDisplayDebit(ChartOfAccount $account, ?AccountBalance $balance): float
    {
        if (!$balance) return 0;
        
        $closing = (float) $balance->closing_balance;
        
        // Return positive value if it matches normal balance or is currently positive
        // Usually Trial Balance shows Debit column if Balance > 0 for Debit-normal accounts
        // or if it's a net debit for any account.
        return $closing > 0 ? $closing : 0;
    }

    /**
     * Determine if closing balance should be shown in Credit column of Trial Balance.
     */
    private function getDisplayCredit(ChartOfAccount $account, ?AccountBalance $balance): float
    {
        if (!$balance) return 0;
        
        $closing = (float) $balance->closing_balance;
        
        // Simple display: if closing is negative, it's a net credit (flip to positive for display)
        return $closing < 0 ? abs($closing) : 0;
    }
}
