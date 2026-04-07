<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Domain Custom (White-label)</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola custom domain untuk tiap tenant.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Domain
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
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari domain atau tenant..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
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
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Domain</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Tenant</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">SSL</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Status</th>
                            <th class="px-6 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($domains as $item)
                        <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/40 dark:to-indigo-800/40 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black group-hover:scale-110 transition-transform border border-indigo-100 dark:border-indigo-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-extrabold text-gray-900 dark:text-white">{{ $item->domain }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($item->is_primary)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase tracking-tighter">Primary</span>
                                            @endif
                                            @if($item->is_verified)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-tighter">Verified</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 uppercase tracking-tighter">Unverified</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->tenant->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->tenant->code }}</div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
                                    $sslColor = match($item->ssl_status) {
                                        'active' => 'emerald',
                                        'pending' => 'amber',
                                        'failed' => 'rose',
                                        'expired' => 'gray',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sslColor }}-100 text-{{ $sslColor }}-800 dark:bg-{{ $sslColor }}-900/30 dark:text-{{ $sslColor }}-400 capitalize">
                                    {{ $item->ssl_status }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <button wire:click="toggleActive('{{ $item->id }}')" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ $item->is_active ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $item->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openEditModal('{{ $item->id }}')" class="p-2.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete('{{ $item->id }}')" wire:confirm="Yakin ingin menghapus domain ini?" class="p-2.5 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl transition-all" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-8 rounded-[2.5rem] mb-4">
                                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-bold text-lg">Belum ada custom domain.</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Mulai dengan menambahkan domain pertama untuk tenant.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($domains->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $domains->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0" x-cloak>
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" wire:click="closeModal"></div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $editId ? 'Edit Domain' : 'Tambah Domain Baru' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Konfigurasi custom domain untuk tenant.</p>
                    </div>
                    <button wire:click="closeModal" class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tenant <span class="text-rose-500">*</span></label>
                        <select wire:model="tenant_id" @class([
                            'block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white',
                            'border-rose-500' => $errors->has('tenant_id'),
                            'border-gray-200 dark:border-gray-700' => !$errors->has('tenant_id'),
                        ])>
                            <option value="">Pilih Tenant</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->code }})</option>
                            @endforeach
                        </select>
                        @error('tenant_id')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Domain <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="domain" placeholder="contoh: shop.example.com" @class([
                            'block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white',
                            'border-rose-500' => $errors->has('domain'),
                            'border-gray-200 dark:border-gray-700' => !$errors->has('domain'),
                        ])>
                        @error('domain')<p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">SSL Status <span class="text-rose-500">*</span></label>
                            <select wire:model="ssl_status" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="flex flex-col justify-center">
                            <label class="flex items-center space-x-2 cursor-pointer mt-4">
                                <input type="checkbox" wire:model="is_primary" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Primary Domain</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_verified" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Verified</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 px-6 py-4 bg-indigo-600 border border-transparent rounded-2xl font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">{{ $editId ? 'Update Domain' : 'Simpan Domain' }}</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
