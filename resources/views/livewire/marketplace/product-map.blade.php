<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Product Mapping</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Hubungkan produk internal dengan listing di marketplace.</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="openImportModal" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-2xl font-bold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import CSV/Excel
                </button>
                <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Mapping
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 px-4 sm:px-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 px-4 sm:px-0 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="mb-6 px-4 sm:px-0">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SKU, nama produk, atau ID marketplace..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
                        </div>
                    </div>
                    <div class="w-full md:w-48">
                        <select wire:model.live="marketplace_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Platform</option>
                            <option value="shopee">Shopee</option>
                            <option value="tokopedia">Tokopedia</option>
                            <option value="lazada">Lazada</option>
                            <option value="bukalapak">Bukalapak</option>
                            <option value="blibli">Blibli</option>
                        </select>
                    </div>
                    <div class="w-full md:w-40">
                        <select wire:model.live="status_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mapping List --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Produk Internal</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Marketplace</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">ID Marketplace</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">SKU</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Status</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse ($mappings as $mapping)
                            <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-xs shadow-lg">
                                            {{ $mapping->product ? substr($mapping->product->name, 0, 2) : '??' }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">{{ $mapping->product->name ?? '-' }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono">{{ $mapping->product->sku ?? '-' }}</p>
                                            @if($mapping->variant)
                                                <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded">{{ $mapping->variant->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-{{ $this->getPlatformColor($mapping->marketplace) }}-100 text-{{ $this->getPlatformColor($mapping->marketplace) }}-700 uppercase">
                                        {{ $this->getPlatformLabel($mapping->marketplace) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <code class="text-xs bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded font-mono text-gray-600 dark:text-gray-400">{{ $mapping->external_product_id }}</code>
                                </td>
                                <td class="px-8 py-5">
                                    @if($mapping->external_sku)
                                        <code class="text-xs bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded font-mono text-gray-600 dark:text-gray-400">{{ $mapping->external_sku }}</code>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    @if($mapping->is_active)
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 uppercase">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-500 uppercase">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="toggleActive('{{ $mapping->id }}')" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="{{ $mapping->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($mapping->is_active)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            @endif
                                        </button>
                                        <button wire:click="openEditModal('{{ $mapping->id }}')" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="delete('{{ $mapping->id }}')" wire:confirm="Yakin ingin menghapus mapping ini?" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-3xl flex items-center justify-center text-gray-400 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Belum ada product mapping</p>
                                        <p class="text-gray-400 text-sm mt-1">Hubungkan produk Anda dengan marketplace.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($mappings->hasPages())
                <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $mappings->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:keydown.escape="closeModal">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $editId ? 'Edit Mapping' : 'Tambah Mapping Baru' }}</h3>
                            <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="space-y-5">
                            {{-- Marketplace --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Platform Marketplace</label>
                                <select wire:model.live="marketplace" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                    <option value="">Pilih Platform</option>
                                    <option value="shopee">Shopee</option>
                                    <option value="tokopedia">Tokopedia</option>
                                    <option value="lazada">Lazada</option>
                                    <option value="bukalapak">Bukalapak</option>
                                    <option value="blibli">Blibli</option>
                                </select>
                                @error('marketplace') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Product --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Produk Internal</label>
                                <select wire:model.live="product_id" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Variant (if product has variants) --}}
                            @if($product_id && count($variants) > 0)
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Varian (Opsional)</label>
                                    <select wire:model="product_variant_id" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                        <option value="">Pilih Varian</option>
                                        @foreach ($variants as $variant)
                                            <option value="{{ $variant->id }}">{{ $variant->name }} ({{ $variant->sku }})</option>
                                        @endforeach
                                    </select>
                                    @error('product_variant_id') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- External Product ID --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">ID Produk Marketplace</label>
                                <input type="text" wire:model="external_product_id" placeholder="ID dari marketplace (item_id)" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                <p class="text-[10px] text-gray-400 mt-1">Contoh: 12345678 (item_id dari URL atau API)</p>
                                @error('external_product_id') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            {{-- External SKU --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">SKU Marketplace <span class="text-gray-300 font-normal">(Opsional)</span></label>
                                <input type="text" wire:model="external_sku" placeholder="SKU dari marketplace" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('external_sku') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            {{-- External Name --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama di Marketplace <span class="text-gray-300 font-normal">(Opsional)</span></label>
                                <input type="text" wire:model="external_name" placeholder="Nama produk di marketplace" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('external_name') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" wire:click="closeModal" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                                    {{ $editId ? 'Simpan' : 'Tambah Mapping' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Import Modal --}}
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:keydown.escape="closeImportModal">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeImportModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-4xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white">Import Mapping dari CSV/Excel</h3>
                                <p class="text-sm text-gray-500 mt-1">Upload file untuk mapping produk secara bulk.</p>
                            </div>
                            <button wire:click="closeImportModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Step 1: Select Platform --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Platform Tujuan</label>
                            <select wire:model.live="import_marketplace" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                <option value="">Pilih Platform</option>
                                <option value="shopee">Shopee</option>
                                <option value="tokopedia">Tokopedia</option>
                                <option value="lazada">Lazada</option>
                                <option value="bukalapak">Bukalapak</option>
                                <option value="blibli">Blibli</option>
                            </select>
                        </div>

                        {{-- Step 2: Download Template --}}
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-blue-700 dark:text-blue-400">Format File CSV</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                        Kolom wajib: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">sku_internal</code>, <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">external_product_id</code><br>
                                        Kolom opsional: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">external_sku</code>, <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">external_name</code><br>
                                        Untuk varian gunakan format: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">SKU-PRODUK/SKU-VARIAN</code>
                                    </p>
                                    <button wire:click="downloadTemplate" class="mt-2 inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download Template CSV
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: Upload File --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Upload File</label>
                            <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-6 text-center hover:border-indigo-400 transition-all">
                                <input type="file" wire:model="import_file" accept=".csv,.txt,.xlsx,.xls" class="hidden" id="import-file">
                                <label for="import-file" class="cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    @if($import_file)
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $import_file->getClientOriginalName() }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Klik untuk ganti file</p>
                                    @else
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">Klik untuk upload file</p>
                                        <p class="text-xs text-gray-500 mt-1">Format: CSV, XLSX (max 10MB)</p>
                                    @endif
                                </label>
                            </div>
                            @error('import_file') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            @error('import_marketplace') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Errors --}}
                        @if(!empty($importErrors))
                            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl">
                                <p class="text-sm font-bold text-rose-700 dark:text-rose-400 mb-2">Error:</p>
                                @foreach($importErrors as $error)
                                    <p class="text-xs text-rose-600 dark:text-rose-300">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        {{-- Preview --}}
                        @if(!empty($importPreview))
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Preview Data ({{ count($importPreview) }} baris)</p>
                                    <p class="text-xs text-gray-500">{{ collect($importPreview)->where('status', 'valid')->count() }} valid, {{ collect($importPreview)->where('status', 'not_found')->count() }} tidak ditemukan</p>
                                </div>
                                <div class="overflow-x-auto max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-bold text-gray-500">SKU Internal</th>
                                                <th class="px-3 py-2 text-left font-bold text-gray-500">Produk</th>
                                                <th class="px-3 py-2 text-left font-bold text-gray-500">ID Marketplace</th>
                                                <th class="px-3 py-2 text-left font-bold text-gray-500">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            @foreach($importPreview as $item)
                                                <tr class="{{ $item['status'] === 'valid' ? 'bg-white dark:bg-gray-800' : 'bg-rose-50 dark:bg-rose-900/10' }}">
                                                    <td class="px-3 py-2 font-mono text-gray-900 dark:text-white">{{ $item['sku_internal'] }}</td>
                                                    <td class="px-3 py-2 text-gray-900 dark:text-white">
                                                        @if($item['product'])
                                                            {{ $item['product']->name }}
                                                            @if($item['variant'])
                                                                <span class="text-[10px] text-indigo-500">({{ $item['variant']->name }})</span>
                                                            @endif
                                                        @else
                                                            <span class="text-rose-500">Tidak ditemukan</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 font-mono text-gray-600 dark:text-gray-400">{{ $item['external_product_id'] }}</td>
                                                    <td class="px-3 py-2">
                                                        @if($item['status'] === 'valid')
                                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-emerald-100 text-emerald-700">OK</span>
                                                        @else
                                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-rose-100 text-rose-700">Error</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex gap-3">
                            <button type="button" wire:click="closeImportModal" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                Batal
                            </button>
                            <button type="button" wire:click="processImport" wire:disabled="{{ empty($importPreview) || collect($importPreview)->where('status', 'valid')->count() === 0 }}" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                Import {{ collect($importPreview)->where('status', 'valid')->count() }} Mapping
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>