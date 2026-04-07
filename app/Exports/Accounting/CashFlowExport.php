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

class CashFlowExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected string $tenantId,
        protected string $startDate,
        protected string $endDate,
        protected ?string $branchId = null
    ) {}

    public function collection()
    {
        $service = new FinancialReportService();
        $data = $service->getCashFlow($this->tenantId, $this->startDate, $this->endDate, $this->branchId);
        
        $rows = collect();
        
        $rows->push(['name' => 'OPENING BALANCE', 'value' => $data['opening_balance']]);
        $rows->push([]);
        
        foreach (['operating' => 'OPERATING ACTIVITIES', 'investing' => 'INVESTING ACTIVITIES', 'financing' => 'FINANCING ACTIVITIES'] as $key => $label) {
            $rows->push(['section' => $label]);
            
            // Inflows
            foreach ($data[$key]['in'] as $in) {
                $rows->push(['name' => $in['name'], 'in' => $in['total'], 'out' => 0]);
            }
            
            // Outflows
            foreach ($data[$key]['out'] as $out) {
                $rows->push(['name' => $out['name'], 'in' => 0, 'out' => $out['total']]);
            }
            
            $rows->push(['name' => 'Net ' . $label, 'net' => $data[$key]['net']]);
            $rows->push([]);
        }
        
        $rows->push(['name' => 'NET CASH FLOW', 'value' => $data['net_cash_flow']]);
        $rows->push(['name' => 'CLOSING BALANCE', 'value' => $data['closing_balance']]);
        
        return $rows;
    }

    public function title(): string
    {
        return 'Cash Flow ' . $this->startDate . ' - ' . $this->endDate;
    }

    public function headings(): array
    {
        return [
            'Activity / Account',
            'Inflow',
            'Outflow',
            'Net',
        ];
    }

    public function map($row): array
    {
        if (isset($row['section'])) {
            return [$row['section'], '', '', ''];
        }
        
        return [
            $row['name'] ?? '',
            $row['in'] ?? ($row['value'] ?? ''),
            $row['out'] ?? '',
            $row['net'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
