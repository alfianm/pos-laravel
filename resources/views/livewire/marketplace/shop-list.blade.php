<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Toko Marketplace</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola daftar toko dari setiap akun marketplace yang terhubung.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Toko
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 px-4 sm:px-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
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
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama toko atau ID..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
                        </div>
                    </div>
                    <div class="w-full md:w-64">
                        <select wire:model.live="account_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Akun</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ ucfirst($account->marketplace) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shop List --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Toko</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Akun Marketplace</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Shop ID</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Pesanan</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse ($shops as $shop)
                            <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-xs shadow-lg">
                                            {{ substr($shop->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">{{ $shop->name }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">ID: {{ $shop->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($shop->account)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-{{ $shop->account->platform_color }}-50 dark:bg-{{ $shop->account->platform_color }}-900/30 p-1 flex items-center justify-center">
                                                <img src="{{ $shop->account->platform_logo }}" alt="{{ $shop->account->platform_label }}" class="max-w-full max-h-full object-contain">
                                            </div>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $shop->account->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    <code class="text-xs bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded font-mono text-gray-600 dark:text-gray-400">{{ $shop->shop_id }}</code>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $shop->orders_count ?? $shop->orders()->count() }}</span>
                                    <span class="text-xs text-gray-400 ml-1">pesanan</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openEditModal('{{ $shop->id }}')" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="delete('{{ $shop->id }}')" wire:confirm="Yakin ingin menghapus toko ini?" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-3xl flex items-center justify-center text-gray-400 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Belum ada toko marketplace</p>
                                        <p class="text-gray-400 text-sm mt-1">Tambahkan toko untuk mulai sinkronisasi produk.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($shops->hasPages())
                <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $shops->links() }}
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
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $editId ? 'Edit Toko' : 'Tambah Toko Baru' }}</h3>
                            <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Akun Marketplace</label>
                                <select wire:model="marketplace_account_id" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ ucfirst($account->marketplace) }})</option>
                                    @endforeach
                                </select>
                                @error('marketplace_account_id') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Shop ID</label>
                                <input type="text" wire:model="shop_id" placeholder="ID dari marketplace" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('shop_id') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Toko</label>
                                <input type="text" wire:model="name" placeholder="Nama toko yang mudah dikenali" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('name') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" wire:click="closeModal" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                                    {{ $editId ? 'Simpan' : 'Tambah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>