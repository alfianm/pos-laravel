<?php

namespace App\Livewire\Accounting;

use App\Models\ArApRecord;
use App\Models\Branch;
use App\Services\ArApService;
use Livewire\Component;
use Livewire\WithPagination;

class ArList extends Component
{
    use WithPagination;

    public $search = '';
    public $branchId = '';
    public $status = '';
    public $dateRange = '';
    public $agingBucket = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedBranchId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedAgingBucket()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ArApRecord::receivable()
            ->with(['entity', 'transaction']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('entity', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->agingBucket) {
            $query->where('days_overdue', $this->getDaysOverdueRange($this->agingBucket));
        }

        $records = $query->orderBy('due_date', 'asc')
            ->orderByDesc('balance_amount')
            ->paginate(20);

        $branches = Branch::pluck('name', 'id');
        $summary = app(ArApService::class)->getReceivablesAging($this->branchId ?: null);

        return view('livewire.accounting.ar-list', [
            'records' => $records,
            'branches' => $branches,
            'summary' => $summary,
            'statuses' => [
                'outstanding' => 'Outstanding',
                'partial' => 'Partial',
                'overdue' => 'Overdue',
                'paid' => 'Paid',
            ],
            'agingBuckets' => [
                'current' => 'Current',
                '1-30' => '1-30 Days',
                '31-60' => '31-60 Days',
                '61-90' => '61-90 Days',
                '90+' => '90+ Days',
            ],
        ]);
    }

    private function getDaysOverdueRange(string $bucket)
    {
        return match ($bucket) {
            'current' => ['=', 0],
            '1-30' => ['between', [1, 30]],
            '31-60' => ['between', [31, 60]],
            '61-90' => ['between', [61, 90]],
            '90+' => ['>', 90],
            default => null,
        };
    }
}