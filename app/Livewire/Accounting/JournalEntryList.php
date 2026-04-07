<?php

declare(strict_types=1);

namespace App\Livewire\Accounting;

use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class JournalEntryList extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'status', except: '')]
    public string $filterStatus = '';

    #[Url(as: 'from', except: '')]
    public string $filterDateFrom = '';

    #[Url(as: 'to', except: '')]
    public string $filterDateTo = '';

    #[Url(as: 'branch', except: '')]
    public string $filterBranch = '';

    #[Url(as: 'account', except: '')]
    public string $filterAccount = '';

    #[Url(as: 'sort', except: 'entry_date')]
    public string $sortColumn = 'entry_date';

    #[Url(as: 'dir', except: 'desc')]
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->sortColumn = 'entry_date';
        $this->sortDirection = 'desc';
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedFilterBranch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAccount(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function journalEntries(): LengthAwarePaginator
    {
        return JournalEntry::query()
            ->with(['branch', 'journalEntryLines.chartOfAccount'])
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $q): void {
                    $q->where('reference_number', 'ilike', "%{$this->search}%")
                        ->orWhere('description', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, function (Builder $query): void {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDateFrom, function (Builder $query): void {
                $query->whereDate('entry_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function (Builder $query): void {
                $query->whereDate('entry_date', '<=', $this->filterDateTo);
            })
            ->when($this->filterBranch, function (Builder $query): void {
                $query->where('branch_id', $this->filterBranch);
            })
            ->when($this->filterAccount, function (Builder $query): void {
                $query->whereHas('journalEntryLines', function ($q) {
                    $q->where('account_id', $this->filterAccount);
                });
            })
            ->when(Auth::user()?->current_branch_id, function (Builder $query, $branchId): void {
                $query->where('branch_id', $branchId);
            })
            ->when($this->sortColumn && $this->sortDirection, function (Builder $query): void {
                $query->orderBy($this->sortColumn, $this->sortDirection);
            })
            ->paginate(15);
    }

    #[Computed]
    public function statusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'posted' => 'Posted',
            'reversed' => 'Reversed',
        ];
    }

    public function delete(string $id): void
    {
        $entry = JournalEntry::findOrFail($id);

        if ($entry->status !== 'draft') {
            $this->dispatch('toast', type: 'error', message: 'Only draft journal entries can be deleted.');

            return;
        }

        $entry->delete();
        $this->dispatch('toast', type: 'success', message: 'Journal entry deleted successfully.');
    }

    public function post(string $id): void
    {
        $entry = JournalEntry::findOrFail($id);

        if ($entry->status !== 'draft') {
            $this->dispatch('toast', type: 'error', message: 'Only draft entries can be posted.');

            return;
        }

        $service = new JournalEntryService;
        $result = $service->post($entry);

        if ($result['success']) {
            $this->dispatch('toast', type: 'success', message: 'Journal entry posted successfully.');
        } else {
            $this->dispatch('toast', type: 'error', message: $result['message'] ?? 'Failed to post journal entry.');
        }
    }

    public function unpost(string $id): void
    {
        $entry = JournalEntry::findOrFail($id);

        if ($entry->status !== 'posted') {
            $this->dispatch('toast', type: 'error', message: 'Only posted entries can be unposted.');

            return;
        }

        $service = new JournalEntryService;
        $result = $service->unpost($entry);

        if ($result['success']) {
            $this->dispatch('toast', type: 'success', message: 'Journal entry unposted successfully.');
        } else {
            $this->dispatch('toast', type: 'error', message: $result['message'] ?? 'Failed to unpost journal entry.');
        }
    }

    public function render(): View
    {
        return view('livewire.accounting.journal-entry-list');
    }
}
