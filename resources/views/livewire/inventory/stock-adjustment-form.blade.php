<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4 py-2">
        <a href="{{ route('inventory.adjustments.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Buat Penyesuaian Stok</h1>
            <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">#{{ $adjustment_no }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 shadow-sm overflow-hidden p-8">
                <div class="space-y-6">
                    {{-- Search Section --}}
                    <div class="relative" x-data="{ open: @entangle('searchResults').live.count() > 0 }">
                        <x-form.input 
                            label="Pilih Produk"
                            placeholder="Cari Nama Produk atau SKU..."
                            model="search"
                            autocomplete="off"
                        />
                        
                        @if(!empty($searchResults))
                        <div class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                            @foreach($searchResults as $product)
                            <button wire:click="addItem('{{ $product->id }}')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left border-b border-slate-50 dark:border-slate-700/50 last:border-0 grow">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-xl">📦</div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ $product->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $product->sku }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Stok Saat Ini</p>
                                    <p class="text-sm font-bold text-indigo-600">{{ $product->inventories()->where('branch_id', auth()->user()->active_branch_id)->first()->qty_available ?? 0 }}</p>
                                </div>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Items List --}}
                    <div class="border border-slate-100 dark:border-slate-700/50 rounded-3xl overflow-hidden mt-8">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Item Produk</th>
                                    <th class="px-6 py-4 text-center">Stok Awal</th>
                                    <th class="px-6 py-4 text-center">Penyesuaian (+/-)</th>
                                    <th class="px-6 py-4 text-center">Stok Akhir</th>
                                    <th class="px-6 py-4 text-right line-clamp-1"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/30">
                                @forelse($items as $index => $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-black text-slate-900 dark:text-white">{{ $item['name'] }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $item['sku'] }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-bold text-slate-600 dark:text-slate-400">{{ $item['before_qty'] }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" step="any" wire:model.live="items.{{ $index }}.adjusted_qty" class="w-24 mx-auto block text-center py-2 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-black focus:border-indigo-500 focus:outline-none transition-all">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm font-black {{ $item['after_qty'] < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                {{ $item['after_qty'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button wire:click="removeItem({{ $index }})" class="p-2 text-slate-300 hover:text-rose-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm italic font-medium">Belum ada produk yang dipilih.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side Actions --}}
        <div class="space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-8 shadow-sm space-y-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] px-1">Informasi Utama</h3>
                
                <x-form.select 
                    label="Alasan Penyesuaian"
                    model="reason"
                    :options="[
                        'Audit' => 'Audit Rutin',
                        'Damage' => 'Barang Rusak',
                        'Expired' => 'Kadaluarsa',
                        'Correction' => 'Koreksi Data',
                        'Lost' => 'Barang Hilang'
                    ]"
                />

                <x-form.textarea 
                    label="Keterangan Opsional"
                    placeholder="Masukan catatan tambahan..."
                    model="notes"
                    rows="3"
                />

                <div class="pt-4">
                    <button wire:click="save" class="w-full py-4 bg-indigo-600 text-white font-black rounded-[2rem] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-[0.98] flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        SIMPAN ADJUSTMENT
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
