<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Neraca Saldo</h1>
            <p class="text-gray-500 mt-1">Trial Balance Report - Summary of all ledger account balances.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <input type="month" wire:model.live="period" class="bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-700 p-0">
            </div>

            <button wire:click="exportPdf" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-indigo-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                PDF
            </button>
            <button wire:click="exportExcel" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-emerald-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Excel
            </button>
        </div>
    </div>

    <!-- Alert Balance Status -->
    @if($isBalanced)
        <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-emerald-100 rounded-full">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="font-bold">Balanced</p>
                <p class="text-sm opacity-90">Total debits match total credits. Financial records are consistent for this period.</p>
            </div>
        </div>
    @else
        <div class="mb-6 flex items-center gap-3 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-700 animate-bounce-subtle">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-rose-100 rounded-full">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="font-bold">Out of Balance</p>
                <p class="text-sm opacity-90">Warning: Total debits ({{ number_format($totalDebit, 2) }}) do not match total credits ({{ number_format($totalCredit, 2) }}). Please check for unposted or errors in journal entries.</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Kode Akun</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Nama Akun</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-right">Debit</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($trialBalance as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-gray-600 px-2 py-1 bg-gray-100 rounded group-hover:bg-white group-hover:shadow-sm transition-all italic">
                                    {{ $item['code'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('accounting.journal-entries.index', ['account' => $item['account_id'], 'from' => $period.'-01', 'to' => \Carbon\Carbon::parse($period.'-01')->endOfMonth()->format('Y-m-d')]) }}" 
                                   class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors underline decoration-gray-200 hover:decoration-indigo-300 underline-offset-4">
                                    {{ $item['name'] }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-700">
                                {{ $item['display_debit'] > 0 ? number_format($item['display_debit'], 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-700">
                                {{ $item['display_credit'] > 0 ? number_format($item['display_credit'], 2) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span class="text-lg">No data found for the selected period.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50/80 font-black">
                        <td colspan="2" class="px-6 py-4 text-right text-sm text-gray-500 uppercase tracking-wider">Total Saldo</td>
                        <td class="px-6 py-4 text-right text-lg text-indigo-600 tabular-nums">
                            {{ number_format($totalDebit, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-lg text-indigo-600 tabular-nums">
                            {{ number_format($totalCredit, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <style>
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 3s ease-in-out infinite;
        }
    </style>
</div>
