<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Sync Logs</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Riwayat sinkronisasi marketplace.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6 px-4 sm:px-0">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
                        </div>
                    </div>
                    <div>
                        <select wire:model.live="marketplace_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Platform</option>
                            <option value="shopee">Shopee</option>
                            <option value="tokopedia">Tokopedia</option>
                            <option value="lazada">Lazada</option>
                            <option value="bukalapak">Bukalapak</option>
                            <option value="blibli">Blibli</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="type_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Tipe</option>
                            <option value="order_import">Import Order</option>
                            <option value="stock_sync">Sinkron Stok</option>
                            <option value="price_sync">Sinkron Harga</option>
                            <option value="product_sync">Sinkron Produk</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="status_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Status</option>
                            <option value="success">Sukses</option>
                            <option value="failed">Gagal</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <button wire:click="clearFilters" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Reset
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Dari Tanggal</label>
                        <input type="date" wire:model.live="date_from" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model.live="date_to" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        {{-- Logs List --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Waktu</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Platform</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Tipe</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Status</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Pesan</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse ($logs as $log)
                            <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                                <td class="px-8 py-5">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $log->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-{{ $this->getPlatformColor($log->marketplace) }}-100 text-{{ $this->getPlatformColor($log->marketplace) }}-700 uppercase">
                                        {{ $this->getPlatformLabel($log->marketplace) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $this->getTypeLabel($log->sync_type) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    @if($log->status === 'success')
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 uppercase">Sukses</span>
                                    @elseif($log->status === 'failed')
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-rose-100 text-rose-700 uppercase">Gagal</span>
                                    @else
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700 uppercase">Pending</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    <div class="max-w-xs truncate text-sm text-gray-900 dark:text-white">
                                        {{ $log->error_message ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($log->payload)
                                        <button type="button" wire:click="$dispatch('show-payload', { id: '{{ $log->id }}' })" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                            Lihat Detail
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-3xl flex items-center justify-center text-gray-400 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M-.01 12h.01"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Belum ada log sinkronisasi</p>
                                        <p class="text-gray-400 text-sm mt-1">Log akan muncul setelah proses sync dilakukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>