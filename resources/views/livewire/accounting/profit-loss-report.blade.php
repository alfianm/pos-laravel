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
        $this->reportData = $service->getProfitAndLoss(
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
        return 'Rp ' . number_format((float)$value, 2, ',', '.');
    }

    public function exportPdf()
    {
        $service = new FinancialReportService();
        $data = $service->getProfitAndLoss(
            Auth::user()->tenant_id,
            $this->startDate,
            $this->endDate,
            $this->branchId === 'all' ? null : $this->branchId
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.accounting.profit-loss', [
            'reportData' => $data,
            'tenant' => Auth::user()->tenant,
            'branch' => $this->branchId !== 'all' ? Branch::find($this->branchId) : null,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "profit-loss-{$this->startDate}-to-{$this->endDate}.pdf");
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Accounting\ProfitLossExport(
                Auth::user()->tenant_id,
                $this->startDate,
                $this->endDate,
                $this->branchId === 'all' ? null : $this->branchId
            ),
            "profit-loss-{$this->startDate}-to-{$this->endDate}.xlsx"
        );
    }
}; ?>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Laporan Laba Rugi</h1>
                <p class="text-gray-500 mt-1">Pantau performa keuangan bisnis Anda dalam periode tertentu.</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="generateReport" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh
                </button>
                <button wire:click="exportPdf" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </button>
                <button wire:click="exportExcel" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
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
                    <input type="date" wire:model="startDate" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" wire:model="endDate" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
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
                        Filter Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        @if($reportData)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Summary Cards -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-xl shadow-emerald-100">
                    <p class="text-emerald-100 text-sm font-medium opacity-80">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold mt-1">{{ $this->formatMoney($reportData['total_revenue']) }}</h3>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl shadow-amber-100">
                    <p class="text-amber-100 text-sm font-medium opacity-80">Laba Kotor (Gross)</p>
                    <h3 class="text-2xl font-bold mt-1">{{ $this->formatMoney($reportData['gross_profit']) }}</h3>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl shadow-indigo-100">
                    <p class="text-indigo-100 text-sm font-medium opacity-80">Laba Bersih (Net)</p>
                    <h3 class="text-2xl font-bold mt-1">{{ $this->formatMoney($reportData['net_profit']) }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Rincian Laporan</h2>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 uppercase tracking-wider">
                        {{ Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Kode Akun</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Akun</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- Pendapatan Section -->
                            <tr class="bg-indigo-50/30">
                                <td colspan="3" class="px-6 py-3 font-bold text-indigo-700 uppercase tracking-tight text-xs">I. Pendapatan (Revenue)</td>
                            </tr>
                            @forelse($reportData['revenues'] as $revenue)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 font-mono text-gray-500">{{ $revenue['code'] }}</td>
                                    <td class="px-6 py-4 text-gray-700 tracking-tight">
                                        <a href="{{ route('accounting.journal-entries.index', ['account' => $revenue['account_id'], 'from' => $startDate, 'to' => $endDate]) }}" class="hover:text-indigo-600 transition duration-150 underline decoration-gray-200 decoration-1 underline-offset-4">
                                            {{ $revenue['name'] }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-emerald-600">{{ $this->formatMoney($revenue['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400 italic">Tidak ada data pendapatan</td></tr>
                            @endforelse
                            <tr class="bg-gray-50 font-bold border-t border-gray-100">
                                <td colspan="2" class="px-6 py-4 text-right text-gray-600">Total Pendapatan</td>
                                <td class="px-6 py-4 text-right text-emerald-700">{{ $this->formatMoney($reportData['total_revenue']) }}</td>
                            </tr>

                            <!-- HPP Section -->
                            <tr class="bg-amber-50/30">
                                <td colspan="3" class="px-6 py-3 font-bold text-amber-700 uppercase tracking-tight text-xs">II. Harga Pokok Penjualan (COGS)</td>
                            </tr>
                            @forelse($reportData['cogs'] as $cog)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 font-mono text-gray-500">{{ $cog['code'] }}</td>
                                    <td class="px-6 py-4 text-gray-700 tracking-tight">
                                        <a href="{{ route('accounting.journal-entries.index', ['account' => $cog['account_id'], 'from' => $startDate, 'to' => $endDate]) }}" class="hover:text-indigo-600 transition duration-150 underline decoration-gray-200 decoration-1 underline-offset-4">
                                            {{ $cog['name'] }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-amber-600">({{ $this->formatMoney($cog['total']) }})</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400 italic">Tidak ada data HPP</td></tr>
                            @endforelse
                            <tr class="bg-amber-50/50 font-bold">
                                <td colspan="2" class="px-6 py-4 text-right text-amber-700">Laba Kotor</td>
                                <td class="px-6 py-4 text-right text-amber-800 underline decoration-double">{{ $this->formatMoney($reportData['gross_profit']) }}</td>
                            </tr>

                            <!-- Biaya Section -->
                            <tr class="bg-rose-50/30 text-rose-700">
                                <td colspan="3" class="px-6 py-3 font-bold uppercase tracking-tight text-xs">III. Biaya Operasional & Lainnya (Expenses)</td>
                            </tr>
                            @forelse($reportData['expenses'] as $expense)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 font-mono text-gray-500">{{ $expense['code'] }}</td>
                                    <td class="px-6 py-4 text-gray-700 tracking-tight">
                                        <a href="{{ route('accounting.journal-entries.index', ['account' => $expense['account_id'], 'from' => $startDate, 'to' => $endDate]) }}" class="hover:text-indigo-600 transition duration-150 underline decoration-gray-200 decoration-1 underline-offset-4">
                                            {{ $expense['name'] }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-rose-600">({{ $this->formatMoney($expense['total']) }})</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400 italic">Tidak ada data biaya</td></tr>
                            @endforelse
                            <tr class="bg-gray-50 font-bold border-t border-gray-100">
                                <td colspan="2" class="px-6 py-4 text-right text-gray-600">Total Biaya</td>
                                <td class="px-6 py-4 text-right text-rose-700">({{ $this->formatMoney($reportData['total_expenses']) }})</td>
                            </tr>

                            <!-- Net Profit Section -->
                            <tr class="bg-indigo-600 text-white font-bold h-16">
                                <td colspan="2" class="px-6 py-4 text-right uppercase text-lg">Laba / (Rugi) Bersih</td>
                                <td class="px-6 py-4 text-right text-xl ring-inset ring-2 ring-white/20">{{ $this->formatMoney($reportData['net_profit']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 p-6 flex flex-col md:flex-row md:items-center md:justify-between border-t border-gray-100 gap-4">
                    <div class="text-xs text-gray-500 space-y-1">
                        <p>* Laporan ini dihasilkan secara otomatis berdasarkan data transaksi POS yang masuk.</p>
                        <p>* Pastikan semua transaksi telah dilakukan posting ke jurnal umum untuk keakuratan data.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-20 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Belum ada data tersedia</h3>
                <p class="text-gray-500 max-w-sm mt-2">Silakan pilih rentang tanggal dan klik "Filter Report" untuk melihat performa keuangan Anda.</p>
            </div>
        @endif
    </div>
</div>
