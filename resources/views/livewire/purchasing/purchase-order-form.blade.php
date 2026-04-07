<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('purchasing.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $isEdit ? 'Ubah PO' : 'Buat PO Baru' }}</h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Lengkapi rincian pesanan pembelian Anda.</p>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                {{-- Left: Header Info --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 space-y-4">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Metadata PO</h3>
                        
                        <x-form.input-group label="Vendor / Supplier" error="supplier_id">
                            <select wire:model="supplier_id" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </x-form.input-group>

                        <x-form.input-group label="Target Cabang" error="branch_id">
                            <select wire:model="branch_id" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </x-form.input-group>

                        <x-form.input-group label="Tanggal Order" error="order_date">
                            <input type="date" wire:model="order_date" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                        </x-form.input-group>

                        <x-form.input-group label="Estimasi Sampai" error="expected_date">
                            <input type="date" wire:model="expected_date" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                        </x-form.input-group>

                        <x-form.input-group label="Catatan Tambahan" error="notes">
                            <textarea wire:model="notes" rows="4" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white" placeholder="Opsional..."></textarea>
                        </x-form.input-group>
                    </div>
                </div>

                {{-- Right: Items --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6">Pilih Produk</h3>
                        
                        <div class="relative mb-8">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search_product" placeholder="Cari SKU atau Nama Produk..." class="block w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-3xl text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white shadow-inner">
                            
                            @if($search_results)
                                <div class="absolute z-20 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl shadow-2xl overflow-hidden ring-1 ring-slate-200/50 dark:ring-slate-700/50">
                                    @foreach($search_results as $p)
                                        <button type="button" wire:click="addProduct('{{ $p->id }}')" class="w-full px-6 py-4 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center justify-between border-b border-slate-50 dark:border-slate-700 last:border-0 transition-colors group">
                                            <div>
                                                <p class="font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $p->name }}</p>
                                                <p class="text-xs font-mono font-bold text-slate-400 dark:text-slate-500 mt-0.5 tracking-widest uppercase">SKU: {{ $p->sku }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">STOK OPS</p>
                                                <p class="font-black text-slate-600 dark:text-slate-200">{{ (float)$p->inventories->where('branch_id', $branch_id)->first()->qty_on_hand ?? 0 }} {{ $p->unit->name ?? 'pcs' }}</p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="overflow-x-auto rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/80 dark:bg-slate-900/40">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Item</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono w-24">Qty</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono w-40">Unit Cost</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono text-right w-40">Subtotal</th>
                                        <th class="px-6 py-4 w-12 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @forelse($items as $index => $item)
                                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-all">
                                            <td class="px-6 py-5">
                                                <p class="font-black text-slate-900 dark:text-white leading-tight mb-0.5">{{ $item['product_name'] }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">PROD ID: {{ substr($item['product_id'], 0, 8) }}</p>
                                            </td>
                                            <td class="px-6 py-5">
                                                <input type="number" wire:model.live="items.{{ $index }}.qty" class="w-full bg-transparent border-0 font-bold text-slate-900 dark:text-white p-0 focus:ring-0 leading-none">
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-1.5 font-bold text-slate-600 dark:text-slate-400">
                                                    <span>Rp</span>
                                                    <input type="number" wire:model.live="items.{{ $index }}.unit_cost" class="w-full bg-transparent border-0 font-bold text-slate-900 dark:text-white p-0 focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-right font-black text-slate-900 dark:text-white">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-5">
                                                <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-full mb-3">
                                                        <svg class="w-8 h-8 text-slate-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                    </div>
                                                    <p class="text-slate-400 text-sm font-medium">Belum ada item yang ditambahkan.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Total Area --}}
                        <div class="mt-8 pt-8 border-t-2 border-dashed border-slate-100 dark:border-slate-700 flex flex-col items-end gap-3 px-6">
                            <div class="flex items-center gap-12">
                                <span class="text-slate-400 font-black uppercase text-xs tracking-widest font-mono">Subtotal</span>
                                <span class="text-slate-900 dark:text-white font-black text-xl">Rp {{ number_format($this->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-12">
                                <span class="text-slate-400 font-black uppercase text-xs tracking-widest font-mono">Tax (11%)</span>
                                <span class="text-slate-900 dark:text-white font-black text-xl">Rp 0</span>
                            </div>
                            <div class="flex items-center gap-12 mt-4 p-6 bg-slate-50/80 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-700">
                                <span class="text-indigo-600 dark:text-indigo-400 font-black uppercase text-sm tracking-widest font-mono">Grand Total</span>
                                <span class="text-slate-900 dark:text-white font-black text-3xl">Rp {{ number_format($this->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-4">
                            <a href="{{ route('purchasing.index') }}" wire:navigate class="px-8 py-4 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-black rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all uppercase tracking-widest text-xs">Batal</a>
                            <button type="submit" class="px-12 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs">Simpan Pesanan (PO)</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
