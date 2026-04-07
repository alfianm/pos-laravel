<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.transfers.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Detail Mutasi Stok</h1>
                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">#{{ $transfer->transfer_no }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest
                @if($transfer->status === 'sent') bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300
                @elseif($transfer->status === 'received') bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300
                @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 @endif">
                {{ $transfer->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Transfer Details --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                <div class="p-10 border-b border-slate-100 dark:border-slate-700/50 flex flex-col md:flex-row justify-between gap-8">
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Dari Cabang</p>
                            <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $transfer->fromBranch->name }}</p>
                        </div>
                        <div class="flex items-center justify-center py-2 h-8 w-8 rounded-full bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ke Cabang</p>
                            <p class="text-sm font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-tight">{{ $transfer->toBranch->name }}</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right space-y-4">
                        <div class="text-sm space-y-1">
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Tanggal Dikirim</p>
                            <p class="font-black text-slate-700 dark:text-slate-200">{{ $transfer->transfer_date?->format('d F Y') ?? '-' }}</p>
                        </div>
                        @if($transfer->received_date)
                        <div class="text-sm space-y-1">
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Tanggal Diterima</p>
                            <p class="font-black text-slate-700 dark:text-slate-200">{{ $transfer->received_date->format('d F Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Table Items --}}
                <div class="p-10">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-700">
                                <th class="pb-6">Item Produk</th>
                                <th class="pb-6 text-right">Qty Transfer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/30">
                            @foreach($transfer->items as $item)
                                <tr class="group">
                                    <td class="py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center border border-slate-100 dark:border-slate-700 text-xl overflow-hidden flex-shrink-0">
                                                📦
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-black text-slate-900 dark:text-white leading-tight truncate">{{ $item->product->name }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $item->product->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 text-right text-sm font-black text-slate-900 dark:text-white">{{ number_format($item->qty, 0) }} Unit</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($transfer->notes)
                <div class="bg-indigo-50 dark:bg-indigo-500/10 rounded-[2rem] p-8 border border-indigo-100 dark:border-indigo-700/30">
                    <h4 class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Catatan</h4>
                    <p class="text-sm text-indigo-800 dark:text-indigo-200 leading-relaxed italic">"{{ $transfer->notes }}"</p>
                </div>
            @endif
        </div>

        {{-- Right Column: Audit Log --}}
        <div class="space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-8 shadow-sm space-y-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Informasi Mutasi</h3>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="mt-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-500/10"></div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-0.5">Diajukan Oleh</p>
                            <p class="text-sm font-bold text-slate-500">{{ $transfer->requestedBy->name ?? 'System' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="mt-1.5 w-2 h-2 rounded-full bg-indigo-50 ring-4 ring-indigo-500/10"></div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-0.5">Status Alur Stok</p>
                            <div class="mt-2 space-y-1">
                                <span class="block text-[10px] font-bold text-slate-400">Pengurangan: {{ $transfer->fromBranch->name }}</span>
                                <span class="block text-[10px] font-bold text-slate-400">Penambahan: {{ $transfer->toBranch->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
