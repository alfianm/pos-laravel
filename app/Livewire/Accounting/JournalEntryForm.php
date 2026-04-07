<?php

declare(strict_types=1);

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class JournalEntryForm extends Component
{
    public ?string $journalEntryId = null;
    public string $entryDate = '';
    public string $referenceNumber = '';
    public string $description = '';
    public ?string $branchId = null;

    public array $lines = [];

    protected JournalEntryService $journalEntryService;

    public function boot(JournalEntryService $journalEntryService): void
    {
        $this->journalEntryService = $journalEntryService;
    }

    public function mount(?string $id = null): void
    {
        $this->journalEntryId = $id;
        $this->branchId = Auth::user()?->current_branch_id ?? null;

        if ($id) {
            $entry = JournalEntry::with('lines')->findOrFail($id);
            $this->entryDate = $entry->entry_date->format('Y-m-d');
            $this->referenceNumber = $entry->reference_number ?? '';
            $this->description = $entry->description ?? '';
            $this->branchId = $entry->branch_id;

            $this->lines = $entry->lines->map(fn($line) => [
                'id' => $line->id,
                'account_id' => $line->account_id,
                'description' => $line->description,
                'debit' => $line->debit > 0 ? (string) $line->debit : '',
                'credit' => $line->credit > 0 ? (string) $line->credit : '',
            ])->toArray();
        } else {
            $this->entryDate = now()->format('Y-m-d');
            $this->addLine();
            $this->addLine();
        }
    }

    public function addLine(): void
    {
        $this->lines[] = [
            'id' => null,
            'account_id' => '',
            'description' => '',
            'debit' => '',
            'credit' => '',
        ];
    }

    public function removeLine(int $index): void
    {
        if (count($this->lines) > 2) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
        }
    }

    public function getTotalDebitProperty(): float
    {
        return collect($this->lines)->sum(fn($line) => (float) ($line['debit'] ?? 0));
    }

    public function getTotalCreditProperty(): float
    {
        return collect($this->lines)->sum(fn($line) => (float) ($line['credit'] ?? 0));
    }

    public function getIsBalancedProperty(): bool
    {
        return abs($this->totalDebit - $this->totalCredit) < 0.01;
    }

    public function save(): void
    {
        $this->validate([
            'entryDate' => 'required|date',
            'referenceNumber' => 'nullable|string|max:50',
            'description' => 'required|string|max:500',
            'branchId' => 'nullable|uuid',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|uuid',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        if (!$this->isBalanced) {
            $this->dispatch('toast', type: 'error', message: 'Journal entry must be balanced (total debit = total credit).');

            return;
        }

        if ($this->totalDebit <= 0) {
            $this->dispatch('toast', type: 'error', message: 'Journal entry must have a non-zero amount.');

            return;
        }

        try {
            DB::transaction(function () {
                $data = [
                    'tenant_id' => Auth::user()?->tenant_id,
                    'branch_id' => $this->branchId,
                    'entry_date' => $this->entryDate,
                    'reference_number' => $this->referenceNumber ?: null,
                    'description' => $this->description,
                    'status' => 'draft',
                ];

                if ($this->journalEntryId) {
                    $entry = JournalEntry::findOrFail($this->journalEntryId);
                    if ($entry->status !== 'draft') {
                        throw new \Exception('Only draft entries can be edited.');
                    }
                    $entry->update($data);
                    $entry->lines()->delete();
                } else {
                    $entry = JournalEntry::create($data);
                }

                foreach ($this->lines as $line) {
                    $debit = (float) ($line['debit'] ?: 0);
                    $credit = (float) ($line['credit'] ?: 0);

                    if ($debit > 0 || $credit > 0) {
                        JournalEntryLine::create([
                            'journal_entry_id' => $entry->id,
                            'account_id' => $line['account_id'],
                            'description' => $line['description'] ?: null,
                            'debit' => $debit,
                            'credit' => $credit,
                        ]);
                    }
                }

                $this->journalEntryService->recalculateEntryBalances($entry->id);
            });

            $message = $this->journalEntryId ? 'Journal entry updated successfully.' : 'Journal entry created successfully.';
            $this->dispatch('toast', type: 'success', message: $message);

            if (!$this->journalEntryId) {
                $this->redirect(route('accounting.journal-entries.index'), navigate: true);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function getAccountsProperty()
    {
        return ChartOfAccount::where('tenant_id', Auth::user()?->tenant_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.accounting.journal-entry-form');
    }
}
