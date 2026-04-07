<?php

use Livewire\Volt\Component;
use App\Services\FinancialReportService;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $startDate;
    public $endDate;
    public $branchId = 'all';
    public $reportData = null;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
        $this->generateReport();
    }

    public function generateReport()
    {
        $service = new FinancialReportService();
        $this->reportData = $service->getCashFlow(
            Auth::user()->tenant_id,
            $this->startDate,
            $this->endDate,
            $this->branchId === 'all' ? null : $this->branchId
        );
    }

    public function getBranchesProperty()
    {
        return Branch::where('tenant_id', Auth::user()->tenant_id)->get();
    }

    public function formatMoney($value)
    {
        $prefix = $value < 0 ? '- Rp ' : 'Rp ';
        return $prefix . number_format(abs((float)$value), 2, ',', '.');
    }

    public function exportPdf()
    {
        $service = new FinancialReportService();
        $data = $service->getCashFlow(
            Auth::user()->tenant_id,
            $this->startDate,
            $this->endDate,
            $this->branchId === 'all' ? null : $this->branchId
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.accounting.cash-flow', [
            'reportData' => $data,
            'tenant' => Auth::user()->tenant,
            'branch' => $this->branchId !== 'all' ? Branch::find($this->branchId) : null,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "cash-flow-{$this->startDate}-to-{$this->endDate}.pdf");
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Accounting\CashFlowExport(
                Auth::user()->tenant_id,
                $this->startDate,
                $this->endDate,
                $this->branchId === 'all' ? null : $this->branchId
            ),
            "cash-flow-{$this->startDate}-to-{$this->endDate}.xlsx"
        );
    }
}; ?>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Laporan Arus Kas (Cash Flow)</h1>
                <p class="text-gray-500 mt-1">Pantau pergerakan uang tunai masuk dan keluar bisnis Anda.</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="exportPdf" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </button>
                <button wire:click="exportExcel" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Excel
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" wire:model="startDate" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" wire:model="endDate" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cabang</label>
                    <select wire:model="branchId" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="all">Semua Cabang</option>
                        @foreach($this->branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button wire:click="generateReport" class="w-full bg-gray-900 text-white rounded-xl py-2.5 text-sm font-semibold hover:bg-gray-800 transition duration-200">
                        Filter Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        @if($reportData)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Saldo Awal</p>
                    <h3 class="text-2xl font-bold mt-2 text-gray-900">{{ $this->formatMoney($reportData['opening_balance']) }}</h3>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Arus Kas Bersih</p>
                    <h3 @class([
                        'text-2xl font-bold mt-2',
                        'text-emerald-600' => $reportData['net_cash_flow'] >= 0,
                        'text-rose-600' => $reportData['net_cash_flow'] < 0,
                    ])>
                        {{ $this->formatMoney($reportData['net_cash_flow']) }}
                    </h3>
                </div>
                <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-indigo-100">
                    <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider">Saldo Akhir</p>
                    <h3 class="text-2xl font-bold mt-2">{{ $this->formatMoney($reportData['closing_balance']) }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kategori / Aktivitas</th>
                            <th class="px-6 py-4 text-right">Inflow (Masuk)</th>
                            <th class="px-6 py-4 text-right">Outflow (Keluar)</th>
                            <th class="px-6 py-4 text-right">Netto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach(['operating' => 'Aktivitas Operasional', 'investing' => 'Aktivitas Investasi', 'financing' => 'Aktivitas Pendanaan'] as $key => $label)
                            <tr class="bg-gray-50/50">
                                <td class="px-6 py-3 font-bold text-gray-900">{{ $label }}</td>
                                <td colspan="3"></td>
                            </tr>
                            @foreach($reportData[$key]['in'] as $in)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 pl-10 text-gray-600">{{ $in['name'] }}</td>
                                    <td class="px-6 py-3 text-right text-emerald-600">{{ $this->formatMoney($in['total']) }}</td>
                                    <td class="px-6 py-3 text-right text-gray-300">-</td>
                                    <td class="px-6 py-3 text-right"></td>
                                </tr>
                            @endforeach
                            @foreach($reportData[$key]['out'] as $out)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 pl-10 text-gray-600">{{ $out['name'] }}</td>
                                    <td class="px-6 py-3 text-right text-gray-300">-</td>
                                    <td class="px-6 py-3 text-right text-rose-600">({{ $this->formatMoney($out['total']) }})</td>
                                    <td class="px-6 py-3 text-right"></td>
                                </tr>
                            @endforeach
                            <tr class="bg-white font-bold border-t border-gray-100">
                                <td class="px-6 py-3 pl-6 text-gray-900">Total {{ $label }}</td>
                                <td colspan="2"></td>
                                <td @class([
                                    'px-6 py-3 text-right',
                                    'text-emerald-700' => $reportData[$key]['net'] >= 0,
                                    'text-rose-700' => $reportData[$key]['net'] < 0,
                                ])>
                                    {{ $this->formatMoney($reportData[$key]['net']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-900 text-white font-bold">
                        <tr>
                            <td class="px-6 py-4 uppercase">Kenaikan / (Penurunan) Kas Bersih</td>
                            <td colspan="2"></td>
                            <td class="px-6 py-4 text-right text-lg">{{ $this->formatMoney($reportData['net_cash_flow']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
