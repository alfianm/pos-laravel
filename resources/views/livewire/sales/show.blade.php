<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('sales.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Detail Transaksi</h1>
                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">#{{ $sale->sale_no }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.receipt', $sale->id) }}" target="_blank" class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-750 transition-all flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Struk
            </a>
            <button class="px-6 py-3 bg-emerald-600 text-white font-black text-sm rounded-2xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Kirim Invoice
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Invoice Content --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                {{-- Invoice Header --}}
                <div class="p-10 border-b border-slate-100 dark:border-slate-700/50 flex flex-col md:flex-row justify-between gap-8">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/30">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <span class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">ChainPOS</span>
                        </div>
                        <div class="text-sm text-slate-500 space-y-1">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $sale->branch->name }}</p>
                            <p>{{ $sale->branch->address ?? 'Alamat belum diatur' }}</p>
                            <p>{{ $sale->branch->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right space-y-4">
                        <h2 class="text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tighter opacity-10">INVOICE</h2>
                        <div class="text-sm space-y-1">
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Tanggal Transaksi</p>
                            <p class="font-black text-slate-700 dark:text-slate-200">{{ $sale->sale_date->format('d F Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Table Items --}}
                <div class="p-10 p-b-0">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-700">
                                <th class="pb-6">Item Produk</th>
                                <th class="pb-6 text-center">Qty</th>
                                <th class="pb-6 text-right">Harga Satuan</th>
                                <th class="pb-6 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/30">
                            @foreach($sale->items as $item)
                                <tr class="group">
                                    <td class="py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center border border-slate-100 dark:border-slate-700 text-xl overflow-hidden flex-shrink-0">
                                                @if($item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover">
                                                @else
                                                    📦
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-black text-slate-900 dark:text-white leading-tight truncate">{{ $item->product->name }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $item->product->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 text-center text-sm font-bold text-slate-600 dark:text-slate-400">{{ $item->qty }}</td>
                                    <td class="py-6 text-right text-sm font-bold text-slate-600 dark:text-slate-400">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-6 text-right text-sm font-black text-slate-900 dark:text-white">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Calculation --}}
                <div class="p-10 bg-slate-50/50 dark:bg-slate-900/30 flex justify-end">
                    <div class="w-full max-w-xs space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Subtotal Item</span>
                            <span class="text-slate-900 dark:text-white font-bold">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Pajak (11%)</span>
                            <span class="text-slate-900 dark:text-white font-bold">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($sale->discount_amount > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-rose-500 font-bold uppercase tracking-widest text-[10px]">Diskon</span>
                                <span class="text-rose-500 font-bold">- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="pt-4 mt-4 border-t-2 border-dashed border-slate-200 dark:border-slate-700 flex justify-between items-end">
                            <span class="text-slate-900 dark:text-white font-black text-sm uppercase tracking-widest">Total Bayar</span>
                            <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter shadow-indigo-500/10">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes Section --}}
            @if($sale->notes)
                <div class="bg-amber-50 dark:bg-amber-500/10 rounded-[2rem] p-8 border border-amber-100 dark:border-amber-700/30">
                    <h4 class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-3">Catatan Transaksi</h4>
                    <p class="text-sm text-amber-800 dark:text-amber-200 leading-relaxed italic">"{{ $sale->notes }}"</p>
                </div>
            @endif
        </div>

        {{-- Right: Side Info --}}
        <div class="space-y-8">
            {{-- Customer Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-8 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 px-1">Customer</h3>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-2xl border border-blue-100 dark:border-blue-700/30 shadow-sm shadow-blue-500/5">
                        👤
                    </div>
                    <div class="min-w-0">
                        <p class="text-base font-black text-slate-900 dark:text-white truncate">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                        <p class="text-xs text-slate-500 font-bold mt-0.5">{{ $sale->customer->phone ?? 'Tidak ada kontak' }}</p>
                    </div>
                </div>
                @if($sale->customer)
                    <div class="mt-6 pt-6 border-t border-slate-50 dark:border-slate-700/50 grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Grup</p>
                            <p class="text-xs font-black text-indigo-600">{{ $sale->customer->group->name ?? 'Regular' }}</p>
                        </div>
                        <div class="space-y-1 text-right">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ID</p>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">#{{ substr($sale->customer->id, 0, 8) }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Audit Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-8 shadow-sm space-y-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Audit Log</h3>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="mt-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-500/10"></div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-0.5">Dibuat Oleh</p>
                            <p class="text-sm font-bold text-slate-500">{{ $sale->creator->name }}</p>
                            <p class="text-[10px] text-slate-400 mt-1">{{ $sale->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="mt-1.5 w-2 h-2 rounded-full bg-indigo-500 ring-4 ring-indigo-500/10"></div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-0.5">Shift Kasir</p>
                            @if($sale->cashRegisterSession)
                                <p class="text-sm font-bold text-slate-500">Sesi #{{ substr($sale->cashRegisterSession->id, 0, 8) }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">Dibuka: {{ $sale->cashRegisterSession->opened_at->format('H:i') }}</p>
                            @else
                                <p class="text-sm font-bold text-slate-400 italic">Tanpa sesi aktif</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Card --}}
            <div class="bg-indigo-600/5 dark:bg-indigo-500/5 rounded-[2.5rem] border border-indigo-100 dark:border-indigo-500/20 p-8 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 px-1">Metode Pembayaran</h3>
                @foreach($sale->payments as $payment)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-lg shadow-indigo-600/20">
                            @if($payment->payment_method === 'cash')
                                💵
                            @elseif($payment->payment_method === 'bank_transfer')
                                🏦
                            @else
                                💳
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ $payment->payment_method }}</p>
                            <p class="text-xs font-bold text-indigo-600 mt-1">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
