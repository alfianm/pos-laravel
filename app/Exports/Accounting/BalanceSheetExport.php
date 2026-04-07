<?php

namespace App\Exports\Accounting;

use App\Services\FinancialReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BalanceSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected string $tenantId,
        protected string $asOfDate,
        protected ?string $branchId = null
    ) {}

    public function collection()
    {
        $service = new FinancialReportService();
        $data = $service->getBalanceSheet($this->tenantId, $this->asOfDate, $this->branchId);
        
        $rows = collect();
        
        // Assets
        $rows->push(['section' => 'ASSETS']);
        foreach ($data['assets'] as $a) {
            $rows->push(['code' => $a['code'], 'name' => $a['name'], 'value' => $a['total']]);
        }
        $rows->push(['name' => 'TOTAL ASSETS', 'value' => $data['total_assets']]);
        $rows->push([]);
        
        // Liabilities
        $rows->push(['section' => 'LIABILITIES']);
        foreach ($data['liabilities'] as $l) {
            $rows->push(['code' => $l['code'], 'name' => $l['name'], 'value' => $l['total']]);
        }
        $rows->push(['name' => 'TOTAL LIABILITIES', 'value' => $data['total_liabilities']]);
        $rows->push([]);
        
        // Equity
        $rows->push(['section' => 'EQUITY']);
        foreach ($data['equity'] as $e) {
            $rows->push(['code' => $e['code'], 'name' => $e['name'], 'value' => $e['total']]);
        }
        $rows->push(['name' => 'TOTAL EQUITY', 'value' => $data['total_equity']]);
        $rows->push([]);
        
        $rows->push(['name' => 'TOTAL LIABILITIES & EQUITY', 'value' => $data['total_liabilities'] + $data['total_equity']]);
        
        return $rows;
    }

    public function title(): string
    {
        return 'Balance Sheet as of ' . $this->asOfDate;
    }

    public function headings(): array
    {
        return [
            'Account Code',
            'Account Name',
            'Amount',
        ];
    }

    public function map($row): array
    {
        if (isset($row['section'])) {
            return [$row['section'], '', ''];
        }
        
        return [
            $row['code'] ?? '',
            $row['name'] ?? '',
            $row['value'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
