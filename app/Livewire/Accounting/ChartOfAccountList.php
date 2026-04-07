<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\AccountCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ChartOfAccountList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';
    public ?string $categoryFilter = null;
    public string $sortField = 'account_code';
    public string $sortDirection = 'asc';
    public int $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => null],
        'sortField' => ['except' => 'account_code'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 25],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteChartOfAccount(string $id): void
    {
        $account = ChartOfAccount::findOrFail($id);
        $this->authorize('delete', $account);

        // Check if account has journal entries
        if ($account->journalEntryLines()->exists()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete account with existing journal entries.',
            ]);
            return;
        }

        // Check if account has children
        if ($account->children()->exists()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete account with sub-accounts.',
            ]);
            return;
        }

        $account->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Chart of account deleted successfully.',
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $accounts = ChartOfAccount::query()
            ->with('category', 'parent')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('account_code', 'like', '%' . $this->search . '%')
                        ->orWhere('account_name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('account_category_id', $this->categoryFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $categories = AccountCategory::orderBy('code')->get();

        return view('livewire.accounting.chart-of-account-list', [
            'accounts' => $accounts,
            'categories' => $categories,
        ]);
    }
}
