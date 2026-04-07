<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Paket Langganan</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola paket langganan untuk tenant (Free, Starter, Pro, Enterprise).</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Paket
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 mx-4 sm:mx-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 mx-4 sm:mx-0 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Search Card --}}
        <div class="mb-6 px-4 sm:px-0">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="relative flex-1 max-w-md">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari paket..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
                    </div>
                    @if($search)
                        <button wire:click="$set('search', '')" class="px-3 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            Reset
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Paket</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Harga Bulanan</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Cabang</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Produk</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">User</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Status</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($plans as $plan)
                        <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/40 dark:to-indigo-800/40 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black group-hover:scale-110 transition-transform border border-indigo-100 dark:border-indigo-800">
                                        {{ substr($plan->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-extrabold text-gray-900 dark:text-white">{{ $plan->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $plan->price_monthly == 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' }}">
                                    {{ $this->formatPrice($plan->price_monthly) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center text-sm text-gray-600 dark:text-gray-300">
                                {{ $this->formatLimit($plan->features['max_branches'] ?? 1) }}
                            </td>
                            <td class="px-6 py-5 text-center text-sm text-gray-600 dark:text-gray-300">
                                {{ $this->formatLimit($plan->features['max_products'] ?? 100) }}
                            </td>
                            <td class="px-6 py-5 text-center text-sm text-gray-600 dark:text-gray-300">
                                {{ $this->formatLimit($plan->features['max_users'] ?? 2) }}
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($plan->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">Nonaktif</span>
                                @endif
                                @if($plan->is_public)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 mt-1">Publik</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openEditModal('{{ $plan->id }}')" class="p-2.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete('{{ $plan->id }}')" wire:confirm="Yakin ingin menghapus paket ini?" class="p-2.5 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl transition-all" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-8 rounded-[2.5rem] mb-4">
                                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-2.332 9-7.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-bold text-lg">Belum ada paket langganan.</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Mulai dengan menambahkan paket pertama Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($plans->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $plans->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0" x-cloak>
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" wire:click="closeModal"></div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto">
            <div class="p-6 max-h-[85vh] overflow-y-auto">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $editId ? 'Edit Paket Langganan' : 'Tambah Paket Baru' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Konfigurasi paket dan limit fitur.</p>
                    </div>
                    <button wire:click="closeModal" class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode Paket <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="code" placeholder="contoh: starter" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('code') border-rose-500 @enderror">
                            @error('code')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Paket <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="name" placeholder="contoh: Starter" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('name') border-rose-500 @enderror">
                            @error('name')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                        <textarea wire:model="description" rows="2" placeholder="Deskripsi singkat paket..." class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('description') border-rose-500 @enderror"></textarea>
                        @error('description')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Pricing --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Siklus Billing <span class="text-rose-500">*</span></label>
                            <select wire:model="billing_cycle" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                                <option value="monthly">Bulanan</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Harga Bulanan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="price_monthly" placeholder="299000" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('price_monthly') border-rose-500 @enderror">
                            @error('price_monthly')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Harga Tahunan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="price_yearly" placeholder="2990000" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('price_yearly') border-rose-500 @enderror">
                            @error('price_yearly')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Quotas --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Limit Quota</h4>
                        <p class="text-xs text-gray-500 mb-3">Gunakan -1 untuk unlimited</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Max Cabang</label>
                                <input type="number" wire:model="max_branches" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Max Produk</label>
                                <input type="number" wire:model="max_products" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Max User</label>
                                <input type="number" wire:model="max_users" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Max Transaksi/Bulan</label>
                                <input type="number" wire:model="max_monthly_transactions" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Storage (MB)</label>
                                <input type="number" wire:model="storage_mb" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Support Level</label>
                                <select wire:model="support" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                                    <option value="email">Email</option>
                                    <option value="priority_email">Priority Email</option>
                                    <option value="priority_chat">Priority Chat</option>
                                    <option value="dedicated_account_manager">Dedicated Manager</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Fitur & Modul</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Modul Aktif (pisahkan dengan koma)</label>
                            <input type="text" wire:model="modules" placeholder="pos, inventory, crm, loyalty" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="api_access" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">API Access</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="custom_domain" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Custom Domain</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="white_label" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">White Label</span>
                            </label>
                        </div>
                    </div>

                    {{-- Status & Order --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Pengaturan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_public" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Tampil Publik</span>
                            </label>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Urutan</label>
                                <input type="number" wire:model="sort_order" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 px-6 py-4 bg-indigo-600 border border-transparent rounded-2xl font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">{{ $editId ? 'Update Paket' : 'Simpan Paket' }}</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
