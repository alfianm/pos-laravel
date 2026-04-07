<?php

namespace App\Exports\Accounting;

use App\Services\FinancialReportService;
use Maatwebsite\Excel\Concerns\ArrayExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
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
        $data = $service->getProfitAndLoss($this->tenantId, $this->startDate, $this->endDate, $this->branchId);
        
        $rows = collect();
        
        // Revenue
        $rows->push(['section' => 'I. REVENUE']);
        foreach ($data['revenues'] as $r) {
            $rows->push([
                'code' => $r['code'],
                'name' => $r['name'],
                'value' => $r['total']
            ]);
        }
        $rows->push(['name' => 'Total Revenue', 'value' => $data['total_revenue']]);
        $rows->push([]); // Spacer
        
        // COGS
        $rows->push(['section' => 'II. COGS']);
        foreach ($data['cogs'] as $c) {
            $rows->push([
                'code' => $c['code'],
                'name' => $c['name'],
                'value' => $c['total'] * -1
            ]);
        }
        $rows->push(['name' => 'Total COGS', 'value' => $data['total_cogs'] * -1]);
        $rows->push(['name' => 'Gross Profit', 'value' => $data['gross_profit']]);
        $rows->push([]); // Spacer
        
        // Expenses
        $rows->push(['section' => 'III. EXPENSES']);
        foreach ($data['expenses'] as $e) {
            $rows->push([
                'code' => $e['code'],
                'name' => $e['name'],
                'value' => $e['total'] * -1
            ]);
        }
        $rows->push(['name' => 'Total Expenses', 'value' => $data['total_expenses'] * -1]);
        $rows->push([]); // Spacer
        
        // Net Profit
        $rows->push(['name' => 'NET PROFIT / LOSS', 'value' => $data['net_profit']]);
        
        return $rows;
    }

    public function title(): string
    {
        return 'Profit Loss ' . $this->startDate . ' - ' . $this->endDate;
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
