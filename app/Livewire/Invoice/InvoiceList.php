<?php
declare(strict_types=1);
namespace App\Livewire\Invoice;

use App\Constants\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch(): void
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

    public function render()
    {
        $user = Auth::user();
        $branchId = $user->current_branch_id ?? $user->branch_id;

        $query = Invoice::query()
            ->with(['customer', 'sale'])
            ->where('branch_id', $branchId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($cq) {
                        $cq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('invoice_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('invoice_date', '<=', $this->dateTo);
        }

        $invoices = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.invoice.invoice-list', [
            'invoices' => $invoices,
            'statuses' => InvoiceStatus::cases(),
        ]);
    }
}
