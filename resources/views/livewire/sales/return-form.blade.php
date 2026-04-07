<div class="p-6 space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Buat Return Baru</h1>
            <p class="text-slate-500">Pilih invoice dan barang yang ingin dikembalikan.</p>
        </div>
        <a href="{{ route('sales.returns.index') }}" class="text-slate-500 hover:text-indigo-600 font-medium flex items-center transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    @if($errors->has('general'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-600 rounded-xl flex items-center animate-pulse">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $errors->first('general') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Form Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Sale Search -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <span class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mr-3">1</span>
                    Cari Invoice Asal
                </h3>
                
                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Invoice Penjualan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input wire:model.live.debounce.300ms="search_sale" type="text" 
                               class="block w-full pl-10 pr-3 py-3 text-sm text-slate-700 border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" 
                               placeholder="Contoh: INV/2024/03/...">
                        
                        @if($saleSearchResults)
                            <div class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden py-1">
                                @foreach($saleSearchResults as $sale)
                                    <button wire:click="selectSale('{{ $sale->id }}')" 
                                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 transition flex items-center justify-between group">
                                        <div class="space-y-0.5">
                                            <div class="font-bold text-slate-800 group-hover:text-indigo-600">{{ $sale->sale_no }}</div>
                                            <div class="text-xs text-slate-400">{{ $sale->sale_date->format('d M Y') }} • Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="text-xs text-slate-400 font-medium group-hover:text-indigo-500">Pilih →</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @error('sale_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if($selectedSale)
                    <div class="mt-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-1">DATA TRANSAKSI</h4>
                                <div class="text-2xl font-black text-indigo-700">{{ $selectedSale->sale_no }}</div>
                                <div class="text-sm text-indigo-500 font-medium">{{ $selectedSale->sale_date->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="text-right">
                                <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1">PENERIMA</h4>
                                <div class="text-sm font-bold text-slate-900">{{ $selectedSale->customer?->name ?? 'Walk-in Customer' }}</div>
                                <div class="text-xs text-slate-500">{{ $selectedSale->customer?->phone ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Items Selection -->
            @if($selectedSale)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mr-3">2</span>
                        Pilih Item untuk Direturn
                    </h3>

                    <div class="space-y-4">
                        @foreach($selectedSale->items as $item)
                        <div class="p-4 rounded-xl border @if(($returnedItems[$item->id]['qty'] ?? 0) > 0) bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200 @else bg-slate-50/50 border-slate-100 @endif transition duration-300">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                                <div class="md:col-span-2 space-y-1">
                                    <div class="font-bold text-slate-800">{{ $item->product?->name ?? 'Prd #'.$item->product_id }}</div>
                                    <div class="text-xs font-semibold text-slate-500 italic">Dibeli: {{ $item->qty }} unit • Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                                    
                                    <div class="flex items-center space-x-2 mt-2">
                                        <select wire:model.defer="returnedItems.{{ $item->id }}.reason_id" 
                                                class="text-xs h-7 py-0 pl-2 pr-6 border-slate-200 rounded bg-white text-slate-600 focus:ring-indigo-500 transition shadow-sm">
                                            <option value="">Alasan Return (Pilih)</option>
                                            @foreach($return_reasons as $reason)
                                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                            @endforeach
                                        </select>
                                        <input wire:model.defer="returnedItems.{{ $item->id }}.notes" type="text" 
                                               class="text-xs h-7 py-0 px-2 border-slate-200 rounded bg-white text-slate-600 focus:ring-indigo-500 transition placeholder:italic shadow-sm flex-1" 
                                               placeholder="Catatan tambahan (opsional)">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block px-1">Jumlah Jml Return</label>
                                    <div class="relative flex items-center">
                                        <button type="button" 
                                                onclick="let input = this.parentNode.querySelector('input'); input.stepDown(); input.dispatchEvent(new Event('input'));"
                                                class="w-8 h-8 rounded-l font-bold text-indigo-600 bg-white border border-r-0 border-slate-200 hover:bg-indigo-50 transition active:scale-95 shadow-sm">
                                            -
                                        </button>
                                        <input type="number" wire:model.live="returnedItems.{{ $item->id }}.qty" min="0" max="{{ $item->qty }}" step="1"
                                               class="w-16 h-8 text-center text-sm font-bold border-slate-200 focus:ring-indigo-500 py-0 shadow-sm">
                                        <button type="button" 
                                                onclick="let input = this.parentNode.querySelector('input'); input.stepUp(); input.dispatchEvent(new Event('input'));"
                                                class="w-8 h-8 rounded-r font-bold text-indigo-600 bg-white border border-l-0 border-slate-200 hover:bg-indigo-50 transition active:scale-95 shadow-sm">
                                            +
                                        </button>
                                    </div>
                                    @error("returnedItems.{$item->id}.qty") <span class="text-[10px] text-rose-500 italic block mt-1 line-clamp-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="text-right pr-2">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block pr-1 mb-1">Subtotal</div>
                                    <div class="text-lg font-black text-slate-900 tabular-nums">
                                        Rp {{ number_format(($returnedItems[$item->id]['qty'] ?? 0) * $item->unit_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Summary & Action -->
        <div class="space-y-6">
            <div class="bg-indigo-900 p-8 rounded-3xl shadow-xl text-white relative overflow-hidden group">
                <!-- Background decoration -->
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition duration-500"></div>
                <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition duration-500"></div>
                
                <h3 class="text-sm font-bold opacity-60 uppercase tracking-widest mb-8 flex items-center">
                    <span class="w-1 h-3 bg-indigo-400 mr-2 rounded-full"></span>
                    Ringkasan Return
                </h3>
                
                <div class="space-y-6 relative z-10">
                    <div class="flex justify-between items-center pb-6 border-b border-white/10 group/item">
                        <span class="text-sm font-medium opacity-60 group-hover/item:opacity-100 transition">Jumlah Item</span>
                        @php
                            $totalItemsReturn = collect($returnedItems)->sum('qty');
                        @endphp
                        <span class="text-xl font-bold">{{ $totalItemsReturn }} unit</span>
                    </div>
                    <div class="pt-2">
                        <div class="text-xs font-bold opacity-40 uppercase tracking-widest mb-2">Total Nilai Return</div>
                        @php
                            $totalValueReturn = collect($returnedItems)->sum(fn($i) => ($i['qty'] ?? 0) * ($i['price'] ?? 0));
                        @endphp
                        <div class="text-4xl font-black tabular-nums tracking-tighter">
                            Rp {{ number_format($totalValueReturn, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tgl/Waktu Waktu Return</label>
                    <input wire:model="return_date" type="date" 
                           class="block w-full px-3 py-2 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Alasan Alasan / Catatan Umum</label>
                    <textarea wire:model="notes" rows="3" 
                              class="block w-full px-3 py-2 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" 
                              placeholder="Keterangan tambahan untuk return ini..."></textarea>
                </div>

                <div class="pt-4 space-y-3">
                    <button wire:click="save" 
                            wire:loading.attr="disabled"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 active:scale-95 transition-all text-lg shadow-lg shadow-indigo-100 flex items-center justify-center group">
                        <span wire:loading.remove>
                            <span class="flex items-center">
                                Proses Return
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </span>
                        </span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                    <div class="text-center">
                        <p class="text-[10px] text-slate-400 font-medium">Stok akan otomatis bertambah kembali setelah diproses.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
