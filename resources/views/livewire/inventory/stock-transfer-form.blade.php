<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8 px-4 sm:px-0">
            <a href="{{ route('inventory.transfers.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Buat Transfer Stok</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Mutasi barang antar cabang #<span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-widest">{{ $transfer_no }}</span></p>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Form: Item Selection --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">Pilih Produk</h3>
                    
                    <div class="relative mb-8">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SKU atau Nama Produk..." class="block w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-[1.5rem] text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white shadow-inner">
                        
                        @if(!empty($searchResults))
                            <div class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-[1.5rem] shadow-2xl overflow-hidden ring-1 ring-slate-200/50 dark:ring-slate-700/50">
                                @foreach($searchResults as $p)
                                    @if($p->variants->count() > 0)
                                        @foreach($p->variants as $v)
                                            <button type="button" wire:click="addItem('{{ $p->id }}', '{{ $v->id }}')" class="w-full px-6 py-4 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center justify-between border-b border-slate-50 dark:border-slate-700 last:border-0 transition-colors group">
                                                <div>
                                                    <p class="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $p->name }} ({{ $v->name }})</p>
                                                    <p class="text-xs font-mono font-bold text-slate-400 tracking-widest uppercase mt-0.5">SKU: {{ $v->sku ?: $p->sku }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">STOK SOURCE</p>
                                                    <p class="font-black text-slate-600 dark:text-slate-200">{{ (float)($p->inventories->where('product_variant_id', $v->id)->first()->qty_on_hand ?? 0) }}</p>
                                                </div>
                                            </button>
                                        @endforeach
                                    @else
                                        <button type="button" wire:click="addItem('{{ $p->id }}')" class="w-full px-6 py-4 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center justify-between border-b border-slate-50 dark:border-slate-700 last:border-0 transition-colors group">
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $p->name }}</p>
                                                <p class="text-xs font-mono font-bold text-slate-400 tracking-widest uppercase mt-0.5">SKU: {{ $p->sku }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">STOK SOURCE</p>
                                                <p class="font-black text-slate-600 dark:text-slate-200">{{ (float)($p->inventories->where('product_variant_id', null)->first()->qty_on_hand ?? 0) }}</p>
                                            </div>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto rounded-[1.5rem] border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/80 dark:bg-slate-900/40">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Item Mutasi</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono text-center">Tersedia</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono text-center w-32">Qty Mutasi</th>
                                    <th class="px-6 py-4 w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($items as $index => $item)
                                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-all">
                                        <td class="px-6 py-5">
                                            <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $item['product_name'] }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">ID: {{ substr($item['product_id'], 0, 8) }}</p>
                                        </td>
                                        <td class="px-6 py-5 text-center font-bold text-slate-600 dark:text-slate-400">
                                            {{ $item['available_qty'] }}
                                        </td>
                                        <td class="px-6 py-5">
                                            <input type="number" wire:model.live="items.{{ $index }}.qty" class="w-full bg-slate-50 dark:bg-slate-900/50 border-0 rounded-xl text-center font-black text-indigo-600 dark:text-indigo-400 py-2 focus:ring-0">
                                        </td>
                                        <td class="px-6 py-5">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-full mb-3">
                                                    <svg class="w-8 h-8 text-slate-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </div>
                                                <p class="text-slate-400 text-sm font-medium">Belum ada item yang dipilih untuk ditransfer.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Logistics info --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 space-y-5">
                    <h3 class="text-sm font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 px-1">Logistik Transfer</h3>
                    
                    <x-form.input-group label="Asal Cabang (Source)" error="from_branch_id">
                        <div class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-black text-slate-400">
                             {{ auth()->user()->activeBranch->name }}
                        </div>
                    </x-form.input-group>

                    <x-form.input-group label="Tujuan Cabang (Destination)" error="to_branch_id">
                        <select wire:model.live="to_branch_id" class="block w-full px-4 py-3 bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-black text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option value="">Pilih Cabang Tujuan</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </x-form.input-group>

                    <x-form.input-group label="Tanggal Mutasi" error="transfer_date">
                        <input type="date" wire:model="transfer_date" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                    </x-form.input-group>

                    <x-form.input-group label="Catatan / Referensi" error="notes">
                        <textarea wire:model="notes" rows="4" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white" placeholder="Opsional..."></textarea>
                    </x-form.input-group>

                    <button type="button" wire:click="save" class="w-full py-4 bg-indigo-600 text-white font-black rounded-[1.5rem] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs">
                        Proses Mutasi Stok
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
