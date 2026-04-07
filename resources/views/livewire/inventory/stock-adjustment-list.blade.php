<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Stock Adjustments</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sesuaikan stok produk di cabang Anda karena kerusakan, kehilangan, atau audit.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.adjustments.create') }}" wire:navigate class="px-5 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Buat Adjustment
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/50 p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.input 
                label="Cari No. Adjustment"
                placeholder="ADJ-2026..."
                model="search"
            />
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/50 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Adjustment</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Alasan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Oleh</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                    @forelse($adjustments as $adj)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/40 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 dark:text-white">{{ $adj->adjustment_no }}</span>
                                    <span class="text-[11px] font-bold text-slate-400 mt-0.5">{{ $adj->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-600 dark:text-slate-300">
                                    {{ $adj->reason }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($adj->status === 'completed')
                                    <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-lg">Selesai</span>
                                @elseif($adj->status === 'draft')
                                    <span class="px-2.5 py-1 bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-lg">Draft</span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase tracking-widest rounded-lg">{{ $adj->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-500">{{ $adj->performedBy->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Belum ada penyesuaian stok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
            {{ $adjustments->links() }}
        </div>
    </div>
</div>
