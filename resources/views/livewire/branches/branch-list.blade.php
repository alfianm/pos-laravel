<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 px-4 sm:px-0 gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Cabang & Outlet</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola titik operasional bisnis Anda.</p>
            </div>
            <a href="{{ route('branches.create') }}" wire:navigate class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-[1.5rem] font-black text-sm text-white hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-500/30 active:scale-95 group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Cabang
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Cabang</th>
                            <th class="px-8 py-5 text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Kode</th>
                            <th class="px-8 py-5 text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Tenant</th>
                            <th class="px-8 py-5 text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($branches as $branch)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 bg-indigo-100 dark:bg-indigo-900/40 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black shadow-inner">
                                        {{ substr($branch->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-extrabold text-gray-900 dark:text-white flex items-center">
                                            {{ $branch->name }}
                                            @if($branch->is_main_warehouse)
                                                <span class="ml-2 px-2 py-0.5 text-[0.6rem] font-black bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-md uppercase tracking-tighter">Gudang Utama</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 font-medium tracking-tight">{{ Str::limit($branch->address, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-black tracking-widest bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-800/50 uppercase">
                                    {{ $branch->code }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ $branch->tenant->name }}</div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('branches.edit', $branch) }}" wire:navigate class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 rounded-xl transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-100 dark:bg-gray-700/50 p-6 rounded-[2rem] mb-4 text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-bold">Belum ada cabang terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($branches->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50">
                    {{ $branches->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

