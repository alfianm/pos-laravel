<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard Owner</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Monitoring performa bisnis antar cabang secara real-time.</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="px-5 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm font-black text-xs uppercase tracking-widest">
                    <div class="relative flex h-2 w-2">
                         <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                         <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                    Sistem Aktif
                </span>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            {{-- Revenue Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-all"></div>
                <div class="px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] inline-block mb-4">Omzet Total</div>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Rp{{ number_format($totalSales, 0, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs font-bold text-emerald-500 tracking-tighter">↑ 12%</span>
                    <span class="text-xs text-slate-400 font-medium tracking-tight">Dibanding kemarin</span>
                </div>
            </div>

            {{-- Profit Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-all"></div>
                <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] inline-block mb-4">Estimasi Laba Kotor</div>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Rp{{ number_format($totalProfit, 0, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400/80 uppercase tracking-widest">Estimasi</span>
                </div>
            </div>

            {{-- Stock Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
                @if($lowStockItems > 0)
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-all"></div>
                @endif
                <div class="px-4 py-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] inline-block mb-4">Critical Stock</div>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $lowStockItems }} Item</h3>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('inventory.index') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 underline decoration-2 underline-offset-4 decoration-amber-600/30">Cek Inventori →</a>
                </div>
            </div>
        </div>

        {{-- Branch Performance Section --}}
        <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-xl border border-slate-100 dark:border-slate-700/50 overflow-hidden">
             <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-700/80 flex items-center justify-between">
                <div>
                     <h3 class="text-xl font-black text-slate-900 dark:text-white">Performa Cabang</h3>
                     <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Laporan Penjualan Per Lokasi</p>
                </div>
                <button wire:click="loadStats" class="p-3 bg-slate-50 dark:bg-slate-900 rounded-2xl text-slate-400 hover:text-indigo-600 transition-all active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
             </div>

             <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                             <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Cabang</th>
                             <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Total Transaksi</th>
                             <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Omzet</th>
                             <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Peringatan Stok</th>
                             <th class="px-8 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($branchPerformance as $branch)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all">
                                 <td class="px-8 py-6">
                                     <div class="font-black text-slate-900 dark:text-white">{{ $branch['name'] }}</div>
                                     <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">ID: {{ substr($branch['id'], 0, 8) }}</div>
                                 </td>
                                 <td class="px-8 py-6 font-bold text-slate-600 dark:text-slate-400">{{ number_format($branch['sales_count']) }} Order</td>
                                 <td class="px-8 py-6 font-black text-indigo-600 dark:text-indigo-400">Rp{{ number_format($branch['total_revenue'], 0, ',', '.') }}</td>
                                 <td class="px-8 py-6">
                                     @if($branch['low_stock'] > 0)
                                         <span class="px-3 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap">{{ $branch['low_stock'] }} Low Stock</span>
                                     @else
                                         <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap">Stok Aman</span>
                                     @endif
                                 </td>
                                 <td class="px-8 py-6 text-right">
                                     <button class="p-2.5 text-slate-400 hover:text-indigo-600 transition-colors">
                                         <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                     </button>
                                 </td>
                            </tr>
                        @empty
                            <tr>
                                 <td colspan="5" class="py-20 text-center font-bold text-slate-400">Belum ada data performa cabang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
        </div>
    </div>
</div>
