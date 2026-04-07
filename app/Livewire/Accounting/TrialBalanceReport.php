<?php

namespace App\Livewire\Accounting;

use App\Services\Accounting\TrialBalanceService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class TrialBalanceReport extends Component
{
    public string $period;
    public ?string $branchId = null;
    
    public function mount()
    {
        $this->period = now()->format('Y-m');
        $this->branchId = auth()->user()->current_branch_id;
    }

    public function render(TrialBalanceService $service)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $data = $service->getTrialBalance($tenantId, $this->period, $this->branchId);
        
        $totalDebit = $data->sum('display_debit');
        $totalCredit = $data->sum('display_credit');
        $isBalanced = round($totalDebit, 2) === round($totalCredit, 2);

        return view('livewire.accounting.trial-balance-report', [
            'trialBalance' => $data,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'isBalanced' => $isBalanced,
        ])->layout('layouts.app');
    }

    public function exportPdf(TrialBalanceService $service)
    {
        $tenantId = auth()->user()->tenant_id;
        $data = $service->getTrialBalance($tenantId, $this->period, $this->branchId);
        $totalDebit = $data->sum('display_debit');
        $totalCredit = $data->sum('display_credit');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.accounting.trial-balance', [
            'trialBalance' => $data,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'period' => $this->period,
            'tenant' => auth()->user()->tenant,
            'branch' => $this->branchId ? \App\Models\Branch::find($this->branchId) : null,
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "trial-balance-{$this->period}.pdf");
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Accounting\TrialBalanceExport(
                auth()->user()->tenant_id,
                $this->period,
                $this->branchId
            ),
            "trial-balance-{$this->period}.xlsx"
        );
    }
}
