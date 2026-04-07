<div>
    @if($isOpen && $sale)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity"></div>

        <!-- Modal Content -->
        <div class="relative bg-white dark:bg-slate-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
            {{-- Header (Non-Printable) --}}
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="font-black text-slate-900 dark:text-white tracking-tight">Transaksi Berhasil</h3>
                </div>
                <button wire:click="close" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Receipt Area (Scrollable & Printable) --}}
            <div id="receipt-print-area" class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-slate-50/50 dark:bg-slate-900/30">
                <div class="bg-white dark:bg-slate-800 p-8 shadow-sm rounded-[2rem] border border-slate-100 dark:border-slate-700 mx-auto w-full max-w-xs sm:max-w-sm font-mono text-xs text-slate-600 dark:text-slate-400">
                    
                    {{-- Store Info --}}
                    <div class="text-center mb-8">
                        <h4 class="text-xl font-black text-slate-900 dark:text-white mb-1 uppercase tracking-tighter">{{ $sale->branch->name ?? 'ChainPOS' }}</h4>
                        <p class="leading-relaxed opacity-70">{{ $sale->branch->address ?? 'Alamat Outlet Belum Diatur' }}</p>
                        <p class="mt-1 opacity-70">Telp: {{ $sale->branch->phone ?? '-' }}</p>
                    </div>

                    {{-- Transaction Meta --}}
                    <div class="border-y border-dashed border-slate-200 dark:border-slate-700 py-4 my-6 space-y-1.5">
                        <div class="flex justify-between">
                            <span>No. Faktur</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $sale->sale_no }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tanggal</span>
                            <span>{{ $sale->sale_date->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kasir</span>
                            <span>{{ $sale->cashier->name ?? 'Kasir' }}</span>
                        </div>
                        @if($sale->customer)
                        <div class="flex justify-between">
                            <span>Pelanggan</span>
                            <span>{{ $sale->customer->name }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Items --}}
                    <div class="space-y-4 mb-8">
                        @foreach($sale->items as $item)
                        <div class="space-y-1">
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-slate-800 dark:text-slate-200 font-bold uppercase">{{ $item->product_name }}</span>
                                <span class="whitespace-nowrap">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex gap-2 opacity-70">
                                <span>{{ (float)$item->qty }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="border-t border-dashed border-slate-200 dark:border-slate-700 pt-6 space-y-2.5">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rp{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pajak (11%)</span>
                            <span>Rp{{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($sale->discount_amount > 0)
                        <div class="flex justify-between text-rose-500">
                            <span>Diskon</span>
                            <span>-Rp{{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-black text-slate-900 dark:text-white pt-2">
                            <span>TOTAL</span>
                            <span>Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Payment Status --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 text-center italic opacity-60">
                        <p>Pembayaran: {{ strtoupper($sale->payments->first()->method ?? 'Tunai') }}</p>
                        <p class="mt-6 font-bold uppercase tracking-widest text-[10px]">Terima Kasih</p>
                    </div>
                </div>
            </div>

            {{-- Footer Aksi --}}
            <div class="p-8 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button onclick="window.printReceipt()" class="flex-1 py-4 bg-indigo-600 text-white font-black rounded-2xl flex items-center justify-center gap-3 hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        CETAK STRUK
                    </button>
                    <button wire:click="close" class="flex-1 py-4 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 font-bold rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        LANJUT TRANSAKSI
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-print-area, #receipt-print-area * {
                visibility: visible;
            }
            #receipt-print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #receipt-print-area > div {
                box-shadow: none !important;
                border: none !important;
                max-width: none !important;
                width: 100% !important;
                color: black !important;
            }
            .dark {
                color-scheme: light;
            }
        }
    </style>

    <script>
        window.printReceipt = function() {
            window.print();
        }
    </script>
    @endif
</div>
