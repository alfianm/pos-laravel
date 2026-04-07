<?php

namespace App\Exports\Accounting;

use App\Services\Accounting\TrialBalanceService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TrialBalanceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected string $tenantId,
        protected string $period,
        protected ?string $branchId = null
    ) {}

    public function collection()
    {
        $service = new TrialBalanceService();
        return $service->getTrialBalance($this->tenantId, $this->period, $this->branchId);
    }

    public function title(): string
    {
        return 'Trial Balance ' . $this->period;
    }

    public function headings(): array
    {
        return [
            'Account Code',
            'Account Name',
            'Opening Balance',
            'Debit',
            'Credit',
            'Closing Balance',
            'Debit (Trial Balance)',
            'Credit (Trial Balance)',
        ];
    }

    public function map($row): array
    {
        return [
            $row['code'],
            $row['name'],
            $row['opening_balance'],
            $row['debit'],
            $row['credit'],
            $row['closing_balance'],
            $row['display_debit'],
            $row['display_credit'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
