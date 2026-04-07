<?php

use Livewire\Volt\Component;
use App\Services\FinancialReportService;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $asOfDate;
    public $branchId = 'all';
    public $reportData = null;

    public function mount()
    {
        $this->asOfDate = now()->toDateString();
        $this->generateReport();
    }

    public function generateReport()
    {
        $service = new FinancialReportService();
        $this->reportData = $service->getBalanceSheet(
            Auth::user()->tenant_id,
            $this->asOfDate,
            $this->branchId === 'all' ? null : $this->branchId
        );
    }

    public function getBranchesProperty()
    {
        return Branch::where('tenant_id', Auth::user()->tenant_id)->get();
    }

    public function formatMoney($value)
    {
        return 'Rp ' . number_format((float)$value, 2, ',', '.');
    }

    public function exportPdf()
    {
        $service = new FinancialReportService();
        $data = $service->getBalanceSheet(
            Auth::user()->tenant_id,
            $this->asOfDate,
            $this->branchId === 'all' ? null : $this->branchId
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.accounting.balance-sheet', [
            'reportData' => $data,
            'tenant' => Auth::user()->tenant,
            'branch' => $this->branchId !== 'all' ? Branch::find($this->branchId) : null,
            'asOfDate' => $this->asOfDate,
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "balance-sheet-as-of-{$this->asOfDate}.pdf");
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Accounting\BalanceSheetExport(
                Auth::user()->tenant_id,
                $this->asOfDate,
                $this->branchId === 'all' ? null : $this->branchId
            ),
            "balance-sheet-as-of-{$this->asOfDate}.xlsx"
        );
    }
}; ?>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Laporan Neraca (Balance Sheet)</h1>
                <p class="text-gray-500 mt-1">Snapshot posisi keuangan bisnis Anda (Aset, Kewajiban, & Modal).</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="exportPdf" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </button>
                <button wire:click="exportExcel" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Excel
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Tanggal (As of Date)</label>
                    <input type="date" wire:model="asOfDate" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cabang / Branch</label>
                    <select wire:model="branchId" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                        <option value="all">Semua Cabang</option>
                        @foreach($this->branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button wire:click="generateReport" class="w-full bg-gray-900 text-white rounded-xl py-2.5 text-sm font-semibold hover:bg-gray-800 transition duration-200 shadow-lg shadow-gray-200">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        @if($reportData)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <!-- Assets Column -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-emerald-50/30">
                        <h2 class="text-xl font-bold text-emerald-900">AKTIVA (Assets)</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-6 py-4 font-semibold uppercase tracking-wider">Akun</th>
                                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($reportData['assets'] as $asset)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-mono text-xs text-gray-400">{{ $asset['code'] }}</div>
                                            <a href="{{ route('accounting.journal-entries.index', ['account' => $asset['account_id'], 'to' => $asOfDate]) }}" class="text-gray-700 font-medium hover:text-indigo-600 transition-colors underline decoration-gray-200 decoration-1 underline-offset-4">
                                                {{ $asset['name'] }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-gray-900">{{ $this->formatMoney($asset['total']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-6 py-4 text-center text-gray-400">Tidak ada data aset</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-emerald-600 text-white font-bold">
                                <tr>
                                    <td class="px-6 py-6 text-right uppercase tracking-wider">TOTAL AKTIVA</td>
                                    <td class="px-6 py-6 text-right text-xl">{{ $this->formatMoney($reportData['total_assets']) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Liabilities & Equity Column -->
                <div class="space-y-8">
                    <!-- Liabilities -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-rose-50/30">
                            <h2 class="text-xl font-bold text-rose-900">KEWAJIBAN (Liabilities)</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($reportData['liabilities'] as $liability)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="font-mono text-xs text-gray-400">{{ $liability['code'] }}</div>
                                                <a href="{{ route('accounting.journal-entries.index', ['account' => $liability['account_id'], 'to' => $asOfDate]) }}" class="text-gray-700 font-medium hover:text-indigo-600 transition-colors underline decoration-gray-200 decoration-1 underline-offset-4">
                                                    {{ $liability['name'] }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-right font-medium text-gray-900">{{ $this->formatMoney($liability['total']) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="px-6 py-4 text-center text-gray-400 italic">Tidak ada data kewajiban</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold border-t border-gray-100">
                                    <tr>
                                        <td class="px-6 py-4 text-right text-rose-700">TOTAL KEWAJIBAN</td>
                                        <td class="px-6 py-4 text-right text-gray-900">{{ $this->formatMoney($reportData['total_liabilities']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Equity -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-indigo-50/30">
                            <h2 class="text-xl font-bold text-indigo-900">MODAL (Equity)</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($reportData['equity'] as $eq)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="font-mono text-xs text-gray-400">{{ $eq['code'] }}</div>
                                                <a href="{{ route('accounting.journal-entries.index', ['account' => $eq['account_id'], 'to' => $asOfDate]) }}" class="text-gray-700 font-medium hover:text-indigo-600 transition-colors underline decoration-gray-200 decoration-1 underline-offset-4">
                                                    {{ $eq['name'] }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-right font-medium text-gray-900">{{ $this->formatMoney($eq['total']) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="px-6 py-4 text-center text-gray-400 italic">Tidak ada data modal</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold border-t border-gray-100">
                                    <tr>
                                        <td class="px-6 py-4 text-right text-indigo-700">TOTAL MODAL</td>
                                        <td class="px-6 py-4 text-right text-gray-900">{{ $this->formatMoney($reportData['total_equity']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Total Passiva -->
                    <div class="bg-indigo-600 rounded-2xl p-8 text-white shadow-xl shadow-indigo-100 flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium opacity-80 uppercase tracking-widest leading-loose">TOTAL PASIVA (Kewajiban + Modal)</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $this->formatMoney($reportData['total_liabilities'] + $reportData['total_equity']) }}</h3>
                        </div>
                        <div class="bg-white/10 p-3 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    <!-- Information Alert -->
                    @if(abs($reportData['total_assets'] - ($reportData['total_liabilities'] + $reportData['total_equity'])) > 0.01)
                        <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 flex items-center text-rose-800">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span class="text-xs font-medium">Peringatan: Neraca tidak seimbang. Selisih: {{ $this->formatMoney(abs($reportData['total_assets'] - ($reportData['total_liabilities'] + $reportData['total_equity']))) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
