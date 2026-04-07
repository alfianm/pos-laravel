<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Daftar Produk</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola inventori produk utama Anda.</p>
            </div>
            @can('manage products')
            <a href="{{ route('master-data.products.create') }}" wire:navigate class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Produk
            </a>
            @endcan
        </div>

        @if (session()->has('message'))
            <div class="mb-6 mx-4 sm:mx-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        {{-- Filter Card --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700/50 p-6 mb-8 mx-4 sm:mx-0">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SKU, Nama, Barcode..." class="block w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                </div>
                <div>
                    <select wire:model.live="category_filter" class="block w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="brand_filter" class="block w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <button wire:click="$set('search', ''); $set('category_filter', ''); $set('brand_filter', '');" class="text-sm font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">Reset Filter</button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Produk</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Kategori / Brand</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Harga Jual</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Status</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($products as $product)
                        <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="h-14 w-14 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-gray-400 font-black group-hover:scale-110 transition-transform overflow-hidden relative border border-gray-200 dark:border-gray-600">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                                        @else
                                            <svg class="w-8 h-8 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-extrabold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs font-mono font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded leading-none">{{ $product->sku }}</span>
                                            @if($product->barcode)
                                                <span class="text-xs text-gray-400 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                    {{ $product->barcode }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                    {{ $product->category->name ?? '—' }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    Brand: <span class="text-gray-500 dark:text-gray-400">{{ $product->brand->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-base font-black text-gray-900 dark:text-white tracking-tight">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-rose-400 font-bold mt-0.5">
                                    Modal: Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 uppercase tracking-widest">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 uppercase tracking-widest">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('master-data.products.show', $product) }}" wire:navigate class="p-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/50 rounded-xl transition-all" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('master-data.products.edit', $product) }}" wire:navigate class="p-2.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button wire:click="delete('{{ $product->id }}')" wire:confirm="Yakin ingin menghapus produk ini?" class="p-2.5 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl transition-all" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-10 rounded-[3rem] mb-6">
                                        <svg class="w-20 h-20 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-extrabold text-xl tracking-tight">Data Produk Kosong.</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1 max-w-xs mx-auto">Anda belum memiliki produk apapun di semua tenant dan branch.</p>
                                    <a href="{{ route('master-data.products.create') }}" wire:navigate class="mt-6 inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border-2 border-indigo-600 dark:border-indigo-500/50 rounded-2xl font-black text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all shadow-xl shadow-indigo-500/10">
                                        Buat Produk Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
