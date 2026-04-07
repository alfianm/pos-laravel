<div class="py-12 bg-gray-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div class="flex items-center gap-4">
                <a href="{{ route('master-data.products') }}" wire:navigate class="group p-3 bg-white dark:bg-slate-900 rounded-2xl text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm border border-slate-200 dark:border-slate-800">
                    <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <nav class="flex mb-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] gap-2">
                        <a href="{{ route('master-data.products') }}" wire:navigate class="hover:text-indigo-500">Inventory</a>
                        <span>/</span>
                        <span class="text-indigo-600 dark:text-indigo-400">Detail Produk</span>
                    </nav>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none">{{ $product->name }}</h2>
                        <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-widest {{ $product->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' }}">
                            {{ $product->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="{{ route('master-data.products.prices', $product->id) }}" wire:navigate class="flex-1 md:flex-none px-6 py-4 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all active:scale-95 shadow-sm text-center">
                    Set Harga Cabang
                </a>
                <a href="{{ route('master-data.products.edit', $product->id) }}" wire:navigate class="flex-1 md:flex-none px-8 py-4 bg-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 text-center">
                    Ubah Produk
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Panel: Stats & Main Info -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Summary Widgets -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                            <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Harga Jual Utama</p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-1">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</h4>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-100 dark:border-emerald-500/20">+12% vs last month</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                            <svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Stok On-Hand</p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ (float)$product->inventories->sum('qty_on_hand') }} {{ $product->unit->name ?? 'pcs' }}</h4>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-400">Tersebar di {{ $product->inventories->count() }} cabang</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                            <svg class="w-16 h-16 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Harga Modal (HPP)</p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-1">Rp{{ number_format($product->cost_price, 0, ',', '.') }}</h4>
                        <div class="mt-4 flex items-center gap-2">
                            @php
                                $margin = $product->selling_price > 0 ? (($product->selling_price - $product->cost_price) / $product->selling_price) * 100 : 0;
                            @endphp
                            <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-lg border border-indigo-100 dark:border-indigo-500/20">Margin {{ round($margin, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Tabs & Content -->
                <div class="bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="flex items-center px-10 pt-8 border-b border-slate-50 dark:border-slate-800">
                        <button wire:click="setTab('info')" class="pb-6 px-4 text-xs font-black uppercase tracking-widest transition-all relative {{ $activeTab === 'info' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">
                            Informasi Dasar
                            @if($activeTab === 'info') <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-t-full"></div> @endif
                        </button>
                        <button wire:click="setTab('variants')" class="pb-6 px-4 text-xs font-black uppercase tracking-widest transition-all relative {{ $activeTab === 'variants' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">
                            Varian Produk ({{ $product->variants->count() }})
                            @if($activeTab === 'variants') <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-t-full"></div> @endif
                        </button>
                        <button wire:click="setTab('stock')" class="pb-6 px-4 text-xs font-black uppercase tracking-widest transition-all relative {{ $activeTab === 'stock' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">
                            Log Histori Stok
                            @if($activeTab === 'stock') <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-t-full"></div> @endif
                        </button>
                    </div>

                    <div class="p-10">
                        @if($activeTab === 'info')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                <div class="space-y-8">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Klasifikasi Utama</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kategori</p>
                                                <p class="text-slate-900 dark:text-white font-black">{{ $product->category->name ?? '-' }}</p>
                                            </div>
                                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Brand</p>
                                                <p class="text-slate-900 dark:text-white font-black">{{ $product->brand->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Identitas Unik</label>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                                <span class="text-xs font-bold text-slate-500">Nomor SKU</span>
                                                <span class="text-xs font-mono font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1 rounded-lg border border-indigo-100 dark:border-indigo-500/20">{{ $product->sku }}</span>
                                            </div>
                                            <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                                <span class="text-xs font-bold text-slate-500">Barcode / GTIN</span>
                                                <span class="text-xs font-mono font-black text-slate-600 dark:text-slate-400">{{ $product->barcode ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-8">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Deskripsi Produk</label>
                                        <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-700 min-h-[160px]">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                                                {{ $product->description ?: 'Tidak ada deskripsi tersedia untuk produk ini.' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-1 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Satuan</p>
                                            <p class="text-slate-900 dark:text-white font-black">{{ $product->unit->name ?? 'pcs' }} ({{ $product->unit->short_name ?? 'pcs' }})</p>
                                        </div>
                                        <div class="flex-1 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tipe Produk</p>
                                            <p class="text-slate-900 dark:text-white font-black uppercase tracking-tight">{{ $product->type ?: 'Single' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($activeTab === 'variants')
                            <div class="overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                                        <tr>
                                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Nama Varian</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">SKU Varian</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono text-right">Harga Jual</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @forelse($product->variants as $variant)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/40 transition-all">
                                            <td class="px-8 py-6 font-black text-slate-900 dark:text-white">{{ $variant->name }}</td>
                                            <td class="px-8 py-6">
                                                <span class="bg-white dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 font-mono text-xs text-slate-600 dark:text-slate-400">{{ $variant->sku }}</span>
                                            </td>
                                            <td class="px-8 py-6 font-black text-emerald-600 dark:text-emerald-400 text-right">Rp{{ number_format($variant->selling_price, 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-8 py-12 text-center text-slate-400 font-bold italic">Produk ini tidak memiliki variasi.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @elseif($activeTab === 'stock')
                            <div class="py-12 text-center bg-slate-50 dark:bg-slate-800/30 rounded-[2rem] border border-dashed border-slate-200 dark:border-slate-700">
                                <div class="p-4 bg-white dark:bg-slate-900 rounded-full inline-block mb-3 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-slate-400 font-black text-[10px] uppercase tracking-widest">Detail histori stok akan segera hadir melalui modul Mutasi.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Panel: visual & Distribution -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Visual Card -->
                <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-4 shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group">
                    <div class="aspect-square w-full rounded-[2rem] overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>
                    @if($product->image_url === $product->fallback_image_url)
                        <div class="absolute inset-0 flex items-center justify-center p-12 pointer-events-none opacity-10">
                            <svg class="w-full h-full text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                </div>

                <!-- Stock Distribution -->
                <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-10 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest leading-none">Sebaran Stok</h3>
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($product->inventories as $inventory)
                        <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 hover:border-indigo-500/30 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-black text-slate-900 dark:text-white text-sm leading-tight">{{ $inventory->branch->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $inventory->branch->city }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter">{{ (float)$inventory->qty_on_hand }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $product->unit->name ?? 'pcs' }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-10 text-center">
                            <svg class="w-12 h-12 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Belum ada stok digital terdaftar</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Cabang</span>
                        <span class="text-sm font-black text-slate-900 dark:text-white">{{ $product->inventories->count() }}</span>
                    </div>
                </div>

                <!-- Last Update Metadata -->
                <div class="px-8 py-4 bg-indigo-50/30 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/10 rounded-2xl flex items-center justify-between">
                    <span class="text-[8px] font-black text-indigo-400 dark:text-indigo-500 uppercase tracking-[0.3em]">Terakhir Diupdate</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $product->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
