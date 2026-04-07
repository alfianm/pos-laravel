<div class="space-y-8 pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Persediaan</h1>
            <p class="text-slate-500 font-medium italic">Manajemen stok produk di cabang aktif</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('inventory.opening-stock') }}" wire:navigate class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-indigo-200 flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Input Stok Awal
            </a>
            <button class="px-6 py-3.5 bg-white border-2 border-slate-100 hover:bg-slate-50 text-slate-700 rounded-2xl font-black text-sm uppercase tracking-widest transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </button>
        </div>
    </div>

    <!-- Filters & Stats Card -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-3 bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-6 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px] relative">
                <input type="text" wire:model.live="search" 
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                    placeholder="Cari SKU atau Nama Produk...">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            
            <select wire:model.live="category_id" class="bg-slate-50 border-2 border-slate-50 rounded-2xl py-3.5 px-6 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="stock_status" class="bg-slate-50 border-2 border-slate-50 rounded-2xl py-3.5 px-6 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer">
                <option value="">Semua Status Stok</option>
                <option value="low">Stok Rendah</option>
                <option value="out">Habis</option>
            </select>
        </div>

        <div class="bg-indigo-600 rounded-[2.5rem] p-6 flex items-center justify-between text-white overflow-hidden relative shadow-xl shadow-indigo-200">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-1">Total Nilai Stok</p>
                <h3 class="text-2xl font-black tracking-tight" wire:loading.class="opacity-50">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-indigo-500/30 p-3 rounded-2xl relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Produk</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kategori</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tersedia</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Dicadangkan</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Fisik</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($inventories as $inventory)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                @if($inventory->product->image_url)
                                    <img src="{{ $inventory->product->image_url }}" class="w-12 h-12 rounded-2xl object-cover bg-slate-100 border border-slate-100">
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center border border-indigo-100">
                                        <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $inventory->product->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 tracking-wider">SKU: {{ $inventory->product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                {{ $inventory->product->category?->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-black {{ $inventory->qty_available <= $inventory->reorder_level ? 'text-rose-500' : 'text-slate-900' }}">
                                    {{ number_format($inventory->qty_available, 0) }}
                                </span>
                                @if($inventory->qty_available <= $inventory->reorder_level && $inventory->qty_available > 0)
                                    <span class="text-[9px] font-black text-rose-400 uppercase tracking-tighter">Low Stock</span>
                                @elseif($inventory->qty_available <= 0)
                                    <span class="text-[9px] font-black text-rose-500 uppercase tracking-tighter">Out of Stock</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-bold text-slate-400">{{ number_format($inventory->qty_reserved, 0) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-black text-slate-900">{{ number_format($inventory->qty_on_hand, 0) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0">
                                <a href="{{ route('inventory.adjustments.create', ['product_id' => $inventory->product_id]) }}" wire:navigate class="p-2.5 bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition-all shadow-sm" title="Sesuaikan Stok">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </a>
                                <a href="{{ route('inventory.transfers.create', ['product_id' => $inventory->product_id]) }}" wire:navigate class="p-2.5 bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition-all shadow-sm" title="Transfer Stok">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="max-w-xs mx-auto space-y-4">
                                <div class="w-16 h-16 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-black">Belum Ada Data</p>
                                    <p class="text-slate-400 text-xs font-medium">Data persediaan akan muncul setelah Anda melakukan input stok awal atau transaksi.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inventories->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
            {{ $inventories->links() }}
        </div>
        @endif
    </div>
</div>
