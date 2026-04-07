<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 px-4 sm:px-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('expenses.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Kategori Pengeluaran</h2>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Atur grup pengeluaran kas operasional.</p>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Input Form --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6">{{ $isEdit ? 'Ubah Kategori' : 'Tambah Kategori' }}</h3>
                    <form wire:submit.prevent="save" class="space-y-5">
                        <x-form.input-group label="Nama Kategori" error="name">
                            <input type="text" wire:model="name" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-bold" placeholder="Misal: Operasional Toko">
                        </x-form.input-group>

                        <x-form.input-group label="Deskripsi" error="description">
                            <textarea wire:model="description" rows="3" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-medium" placeholder="Opsional..."></textarea>
                        </x-form.input-group>

                        <div class="pt-4 flex gap-2">
                             @if($isEdit)
                                <button type="button" wire:click="$set('isEdit', false)" class="flex-1 px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-black rounded-xl hover:bg-slate-200 transition-all text-xs uppercase tracking-widest leading-none">Batal</button>
                            @endif
                            <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 text-xs uppercase tracking-widest leading-none">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: List --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="overflow-x-auto rounded-3xl border border-slate-100 dark:border-slate-700">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400 dark:text-slate-700 uppercase tracking-widest font-mono">Nama Kategori</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400 dark:text-slate-700 uppercase tracking-widest font-mono">Deskripsi</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($categories as $cat)
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/20 transition-all">
                                    <td class="px-6 py-5 font-bold text-slate-900 dark:text-white">{{ $cat->name }}</td>
                                    <td class="px-6 py-5 text-sm text-slate-500 dark:text-slate-400">{{ $cat->description ?: '-' }}</td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="edit('{{ $cat->id }}')" class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 rounded-lg transition-all" title="Ubah">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button wire:click="delete('{{ $cat->id }}')" wire:confirm="Hapus kategori ini?" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/50 rounded-lg transition-all" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-400 font-medium italic italic">Belum ada kategori pengeluaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
