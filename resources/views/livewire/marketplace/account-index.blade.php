<div class="h-full flex flex-col">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Koneksi Marketplace</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Hubungkan toko anda dengan Shopee, Tokopedia, dan lainnya.</p>
        </div>
        <a href="{{ route('omnichannel.accounts.create') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-4 bg-indigo-600 dark:bg-indigo-500 text-white rounded-2xl font-black hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-500/20 active:scale-95 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Akun Baru
        </a>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Total Koneksi</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $accounts->total() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Connection Grid/List --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden flex-1 flex flex-col">
        <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari akun marketplace..." class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
            </div>
        </div>

        <div class="flex-1 overflow-x-auto overflow-y-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-4">Marketplace</th>
                        <th class="px-6 py-4">Nama Akun</th>
                        <th class="px-6 py-4">Toko Terhubung</th>
                        <th class="px-6 py-4">Masa Berlaku</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($accounts as $account)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center p-2 bg-{{ $account->platform_color }}-50 dark:bg-{{ $account->platform_color }}-900/20">
                                        <img src="{{ $account->platform_logo }}" alt="{{ $account->platform_label }}" class="max-w-full max-h-full object-contain">
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $account->platform_label }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $account->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-500">
                                {{ $account->shops_count ?? 0 }} Toko
                            </td>
                            <td class="px-6 py-4">
                                @if($account->expires_at)
                                    <span class="text-xs font-bold {{ $account->expires_at->isPast() ? 'text-rose-500' : 'text-emerald-500' }}">
                                        {{ $account->expires_at->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-gray-400">Permanen / Manual</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('omnichannel.accounts.edit', $account->id) }}" wire:navigate 
                                       class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button wire:click="delete('{{ $account->id }}')" 
                                            wire:confirm="Yakin ingin memutuskan koneksi ini?"
                                            class="p-2 text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-3xl flex items-center justify-center text-gray-400 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Belum ada koneksi marketplace</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($accounts->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 dark:border-gray-700">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>
