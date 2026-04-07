<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Pengeluaran Kas</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola biaya operasional dan pengeluaran harian.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('expenses.categories') }}" wire:navigate class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl font-bold text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                    Kategori Pengeluaran
                </a>
                <a href="{{ route('expenses.create') }}" wire:navigate class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Catat Pengeluaran
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 mx-4 sm:mx-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700/50 p-6 mb-8 mx-4 sm:mx-0">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Expense atau Catatan..." class="block w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                </div>
                <div>
                    <select wire:model.live="category_id" class="block w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date" wire:model.live="date_from" class="block w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                </div>
                <div>
                    <input type="date" wire:model.live="date_to" class="block w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Tanggal & No. No.</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Kategori & Cabang</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Metode & Catatan</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Jumlah</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($expenses as $ex)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all">
                            <td class="px-8 py-6">
                                <div class="text-base font-black text-gray-900 dark:text-white tracking-tight">{{ $ex->expense_no }}</div>
                                <div class="text-xs text-gray-400 mt-1 font-medium">{{ $ex->date->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $ex->category->name }}</div>
                                <div class="text-xs text-gray-400 mt-1 font-medium">{{ $ex->branch->name }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm text-slate-600 dark:text-slate-400 italic">"{{ \Illuminate\Support\Str::limit($ex->notes, 30) ?: 'Tanpa catatan' }}"</div>
                                <div class="text-[10px] font-black uppercase text-indigo-500 dark:text-indigo-400 mt-1 tracking-[0.1em]">{{ $ex->payment_method }}</div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="text-xl font-black text-rose-500 tracking-tight">Rp{{ number_format($ex->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="delete('{{ $ex->id }}')" wire:confirm="Hapus data pengeluaran ini?" class="p-2.5 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl transition-all" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-10 rounded-[3rem] mb-6 shadow-inner">
                                        <svg class="w-20 h-20 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-extrabold text-xl tracking-tight">Data Pengeluaran Kosong.</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1 max-w-xs mx-auto font-medium">Belum ada pengeluaran kas yang dicatat untuk periode ini.</p>
                                    <a href="{{ route('expenses.create') }}" wire:navigate class="mt-8 inline-flex items-center px-8 py-3 bg-white dark:bg-gray-800 border-2 border-indigo-600 dark:border-indigo-500/50 rounded-2xl font-black text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all shadow-xl shadow-indigo-500/10">
                                        Catat Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
