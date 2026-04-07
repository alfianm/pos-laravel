<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 px-4 sm:px-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('purchasing.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $purchaseOrder->po_no }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Order Date: <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 font-mono text-sm ml-1">{{ $purchaseOrder->order_date->format('d/m/Y') }}</span></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($purchaseOrder->status === 'pending')
                    <button wire:click="submitOrder" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95">
                        Kirim Order ke Supplier
                    </button>
                    <a href="{{ route('purchasing.edit', $purchaseOrder->id) }}" wire:navigate class="px-6 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95">
                        Ubah PO
                    </a>
                @endif

                @if($purchaseOrder->status === 'ordered')
                    <button wire:click="receiveGoods" wire:confirm="Terima semua barang dan tambahkan ke stok sekarang?" class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
                        Konfirmasi Terima Barang (GRN)
                    </button>
                @endif
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Items Table --}}
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6">Daftar Barang Dipesan</h3>
                    <div class="overflow-hidden rounded-3xl border border-slate-100 dark:border-slate-700">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-6 py-5 text-xs font-black text-slate-400 dark:text-slate-700 uppercase tracking-widest font-mono">Barang</th>
                                    <th class="px-6 py-5 text-xs font-black text-slate-400 dark:text-slate-700 uppercase tracking-widest font-mono text-center">Order Qty</th>
                                    <th class="px-6 py-5 text-xs font-black text-slate-400 dark:text-slate-700 uppercase tracking-widest font-mono text-center">Receive Qty</th>
                                    <th class="px-6 py-5 text-xs font-black text-slate-400 dark:text-slate-700 uppercase tracking-widest font-mono text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($purchaseOrder->items as $item)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-all">
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-slate-900 dark:text-white leading-tight">{{ $item->product->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">COST: Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center font-bold text-slate-700 dark:text-slate-300">{{ (float)$item->qty }}</td>
                                    <td class="px-6 py-5 text-center font-bold {{ $item->received_qty >= $item->qty ? 'text-emerald-500' : 'text-slate-400' }}">
                                        {{ (float)$item->received_qty }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-black text-slate-900 dark:text-white tracking-tight">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Below Table --}}
                    <div class="mt-8 pt-8 border-t-2 border-dashed border-slate-100 dark:border-slate-700 flex flex-col items-end gap-3 px-6">
                        <div class="flex items-center gap-12">
                            <span class="text-slate-400 font-black uppercase text-xs tracking-widest font-mono">Total Net</span>
                            <span class="text-slate-900 dark:text-white font-black text-2xl">Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center gap-12 p-6 bg-slate-50/80 dark:bg-slate-900/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 mt-4 shadow-inner">
                            <span class="text-indigo-600 dark:text-indigo-400 font-black uppercase text-sm tracking-widest font-mono">Grand Total</span>
                            <span class="text-slate-900 dark:text-white font-black text-4xl">Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($purchaseOrder->notes)
                <div class="bg-white dark:bg-slate-800 rounded-[2rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-4">Catatan PO</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">{{ $purchaseOrder->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Right Sidebar: Metadata & Actions --}}
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-sm font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Status Pesanan</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-2xl text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">PROGRES</p>
                                <p class="text-lg font-black {{ $purchaseOrder->status === 'received' ? 'text-emerald-600' : 'text-indigo-600' }} tracking-tight">{{ strtoupper($purchaseOrder->status) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-2xl text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">PEMBAYARAN</p>
                                <p class="text-lg font-black {{ $purchaseOrder->payment_status === 'paid' ? 'text-emerald-600' : 'text-rose-600' }} tracking-tight">{{ strtoupper($purchaseOrder->payment_status) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-sm font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Informasi Vendor</h3>
                    <div class="space-y-4">
                        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mb-1">{{ $purchaseOrder->supplier->name }}</p>
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $purchaseOrder->supplier->phone }}
                            </p>
                            @if($purchaseOrder->expected_date)
                            <p class="text-xs font-medium text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Est. Kedatangan: {{ $purchaseOrder->expected_date->format('d/m/Y') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-sm font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Tujuan Pengiriman</h3>
                    <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mb-1">{{ $purchaseOrder->branch->name }}</p>
                    <p class="text-xs font-medium text-slate-500 leading-relaxed">{{ $purchaseOrder->branch->address }}</p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900/40 rounded-[2.5rem] p-8 border border-slate-100 dark:border-slate-700/50">
                    <h3 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Dibuat Oleh</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center font-black">
                            {{ substr($purchaseOrder->user->name ?? 'A', 0, 1) }}
                        </div>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $purchaseOrder->user->name ?? 'Sistem' }}</p>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-4">TANGGAL INPUT: {{ $purchaseOrder->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
