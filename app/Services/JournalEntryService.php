<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalEntryService
{
    /**
     * Create a new journal entry with lines.
     *
     * @param array $data Entry data including lines
     * @return JournalEntry
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function create(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $entry = JournalEntry::create([
                'tenant_id' => $data['tenant_id'],
                'branch_id' => $data['branch_id'],
                'journal_number' => $data['journal_number'] ?? $this->generateEntryNumber($data['tenant_id']),
                'entry_date' => $data['entry_date'] ?? $data['date'] ?? now(),
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'description' => $data['description'],
                'notes' => $data['notes'] ?? null,
                'total_debit' => 0,
                'total_credit' => 0,
                'is_posted' => false,
            ]);

            if (!empty($data['lines'])) {
                $this->createLines($entry, $data['lines']);
                $entry->calculateTotals();
            }

            Log::info('Journal entry created', [
                'entry_id' => $entry->id,
                'journal_number' => $entry->journal_number,
                'tenant_id' => $entry->tenant_id,
            ]);

            return $entry->fresh(['lines.account']);
        });
    }

    /**
     * Update an existing journal entry.
     *
     * @param JournalEntry $entry
     * @param array $data
     * @return JournalEntry
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function update(JournalEntry $entry, array $data): JournalEntry
    {
        if ($entry->isPosted()) {
            throw new \InvalidArgumentException('Cannot edit posted journal entry.');
        }

        return DB::transaction(function () use ($entry, $data) {
            $entry->update([
                'entry_date' => $data['date'] ?? $entry->entry_date,
                'description' => $data['description'] ?? $entry->description,
                'notes' => $data['notes'] ?? $entry->notes,
            ]);

            if (!empty($data['lines'])) {
                // Delete existing lines
                $entry->lines()->delete();
                // Recreate lines
                $this->createLines($entry, $data['lines']);
                $entry->calculateTotals();
            }

            Log::info('Journal entry updated', [
                'entry_id' => $entry->id,
                'journal_number' => $entry->journal_number,
            ]);

            return $entry->fresh(['lines.account']);
        });
    }

    /**
     * Post a journal entry to the general ledger.
     *
     * @param JournalEntry $entry
     * @return JournalEntry
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function post(JournalEntry $entry): JournalEntry
    {
        if ($entry->isPosted()) {
            throw new \InvalidArgumentException('Journal entry is already posted.');
        }

        if (!$entry->isBalanced()) {
            throw new \InvalidArgumentException('Journal entry is not balanced. Total debit must equal total credit.');
        }

        if ($entry->lines()->count() === 0) {
            throw new \InvalidArgumentException('Journal entry has no lines.');
        }

        return DB::transaction(function () use ($entry) {
            $entry->update([
                'is_posted' => true,
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            // Update account balances
            $this->updateAccountBalances($entry);

            Log::info('Journal entry posted', [
                'entry_id' => $entry->id,
                'journal_number' => $entry->journal_number,
                'posted_by' => Auth::id(),
            ]);

            return $entry->fresh();
        });
    }

    /**
     * Unpost a journal entry, reverting its impact on account balances.
     *
     * @param JournalEntry $entry
     * @return JournalEntry
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function unpost(JournalEntry $entry): JournalEntry
    {
        if (!$entry->isPosted()) {
            throw new \InvalidArgumentException('Journal entry is not posted.');
        }

        return DB::transaction(function () use ($entry) {
            // Reverse account balances (using negative values)
            foreach ($entry->lines as $line) {
                $balanceService = new AccountBalanceService;
                $balanceService->updateBalance(
                    $entry->tenant_id,
                    $line->account_id,
                    $entry->entry_date->format('Y-m-d'),
                    -(float) $line->debit,
                    -(float) $line->credit
                );
            }

            $entry->update([
                'is_posted' => false,
                'status' => 'draft',
                'posted_at' => null,
                'posted_by' => null,
            ]);

            Log::info('Journal entry unposted', [
                'entry_id' => $entry->id,
                'journal_number' => $entry->journal_number,
                'unposted_by' => Auth::id(),
            ]);

            return $entry->fresh();
        });
    }

    /**
     * Reverse a posted journal entry by creating a new offsetting entry.
     *
     * @param JournalEntry $entry
     * @param string|null $reason
     * @return JournalEntry The new reversing entry
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function reverse(JournalEntry $entry, ?string $reason = null): JournalEntry
    {
        if (!$entry->isPosted()) {
            throw new \InvalidArgumentException('Cannot reverse unposted journal entry.');
        }

        if ($entry->reversingEntry()->exists() || $entry->status === 'reversed') {
            throw new \InvalidArgumentException('Journal entry has already been reversed.');
        }

        return DB::transaction(function () use ($entry, $reason) {
            $reversingEntry = JournalEntry::create([
                'tenant_id' => $entry->tenant_id,
                'branch_id' => $entry->branch_id,
                'journal_number' => $this->generateEntryNumber($entry->tenant_id),
                'entry_date' => now(),
                'reference_type' => 'reversal',
                'reference_id' => $entry->id,
                'description' => 'Reversal of entry #' . $entry->journal_number . ($reason ? ': ' . $reason : ''),
                'total_debit' => $entry->total_credit,
                'total_credit' => $entry->total_debit,
                'is_posted' => true,
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            // Create reversing lines (swap debit/credit)
            foreach ($entry->lines as $line) {
                JournalEntryLine::create([
                    'tenant_id' => $reversingEntry->tenant_id,
                    'journal_entry_id' => $reversingEntry->id,
                    'account_id' => $line->account_id,
                    'description' => $line->description,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'line_number' => $line->line_number,
                ]);
            }

            // Update status of original entry
            $entry->update([
                'status' => 'reversed',
            ]);

            // Update account balances for reversing entry
            $this->updateAccountBalances($reversingEntry);

            Log::info('Journal entry reversed', [
                'original_entry_id' => $entry->id,
                'reversing_entry_id' => $reversingEntry->id,
                'reversed_by' => Auth::id(),
            ]);

            return $reversingEntry->fresh(['lines.account']);
        });
    }

    /**
     * Delete an unposted journal entry.
     *
     * @param JournalEntry $entry
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function delete(JournalEntry $entry): bool
    {
        if ($entry->isPosted()) {
            throw new \InvalidArgumentException('Cannot delete posted journal entry.');
        }

        return DB::transaction(function () use ($entry) {
            $entry->lines()->delete();
            $deleted = $entry->delete();

            Log::info('Journal entry deleted', [
                'entry_id' => $entry->id,
                'entry_number' => $entry->entry_number,
            ]);

            return $deleted;
        });
    }

    /**
     * Create journal entry lines.
     *
     * @param JournalEntry $entry
     * @param array $lines
     * @return void
     */
    private function createLines(JournalEntry $entry, array $lines): void
    {
        $lineNumber = 1;

        foreach ($lines as $line) {
            // Validate account exists
            $account = ChartOfAccount::where('id', $line['account_id'])
                ->where('tenant_id', $entry->tenant_id)
                ->first();

            if (!$account) {
                throw new \InvalidArgumentException('Invalid account selected: ' . $line['account_id']);
            }

            // Ensure at least one of debit or credit is set
            $debit = $line['debit'] ?? 0;
            $credit = $line['credit'] ?? 0;

            if ($debit == 0 && $credit == 0) {
                continue; // Skip empty lines
            }

            if ($debit > 0 && $credit > 0) {
                throw new \InvalidArgumentException('Line cannot have both debit and credit amounts.');
            }

            JournalEntryLine::create([
                'tenant_id' => $entry->tenant_id,
                'journal_entry_id' => $entry->id,
                'account_id' => $line['account_id'],
                'description' => $line['description'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
                'line_number' => $lineNumber++,
            ]);
        }
    }

    /**
     * Update account balances when posting.
     *
     * @param JournalEntry $entry
     * @return void
     */
    private function updateAccountBalances(JournalEntry $entry): void
    {
        foreach ($entry->lines as $line) {
            $account = $line->account;
            if (!$account) {
                continue;
            }

            $balanceService = new AccountBalanceService;
            $balanceService->updateBalance(
                $entry->tenant_id,
                $line->account_id,
                $entry->entry_date->format('Y-m-d'),
                (float) $line->debit,
                (float) $line->credit
            );
        }
    }

    /**
     * Generate unique entry number.
     *
     * @param string $tenantId
     * @return string
     */
    public function generateEntryNumber(string $tenantId): string
    {
        $prefix = 'JV-' . date('Ym') . '-';
        $count = JournalEntry::where('tenant_id', $tenantId)
            ->where('journal_number', 'like', $prefix . '%')
            ->count();

        return $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate entry balance.
     *
     * @param array $lines
     * @return array ['is_balanced' => bool, 'total_debit' => float, 'total_credit' => float, 'difference' => float]
     */
    public function validateBalance(array $lines): array
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += $line['debit'] ?? 0;
            $totalCredit += $line['credit'] ?? 0;
        }

        $difference = abs($totalDebit - $totalCredit);

        return [
            'is_balanced' => $difference < 0.01,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'difference' => $difference,
        ];
    }
}
