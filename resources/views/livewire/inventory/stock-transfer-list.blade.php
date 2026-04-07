<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Mutasi Antar Cabang</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">Lacak pengiriman dan penerimaan stok antar gudang/cabang.</p>
            </div>
            <div>
                <a href="{{ route('inventory.transfers.create') }}" wire:navigate class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-2xl font-black text-sm text-white hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 whitespace-nowrap uppercase tracking-widest leading-none">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Buat Mutasi
                </a>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 mx-4 sm:mx-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters & Search --}}
        <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-[2rem] border border-slate-100 dark:border-slate-700/50 p-6 mb-8 mx-4 sm:mx-0">
            <div class="relative max-w-lg">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Mutasi atau Catatan..." class="block w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">No. Mutasi & Tanggal</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Rute (From ➔ To)</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($transfers as $tr)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all">
                            <td class="px-8 py-6">
                                <div class="text-sm font-black text-slate-900 dark:text-white tracking-tight uppercase">{{ $tr->transfer_no }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $tr->transfer_date?->format('d M Y') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-black uppercase tracking-widest">{{ $tr->fromBranch->name }}</span>
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-black uppercase tracking-widest">{{ $tr->toBranch->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ 
                                    $tr->status === 'received' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 
                                    ($tr->status === 'sent' ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-slate-50 text-slate-600 dark:bg-slate-700 dark:text-slate-400')
                                }}">
                                    {{ $tr->status }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                 <a href="{{ route('inventory.transfers.show', $tr->id) }}" wire:navigate class="p-2.5 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                     <div class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] mb-4">
                                        <svg class="w-16 h-16 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    </div>
                                    <p class="text-slate-400 font-bold">Belum ada mutasi stok.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-50 dark:border-slate-700">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
