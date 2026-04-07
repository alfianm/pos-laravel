<div>
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none">Grup Pelanggan</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm font-medium">Kelola klasifikasi pelanggan untuk diskon dan loyalitas.</p>
        </div>
        <button wire:click="openCreateModal" class="px-8 py-3 bg-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
            + Tambah Grup
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center gap-3 animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-bold">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl flex items-center gap-3 animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 overflow-hidden shadow-xl shadow-slate-200/40 dark:shadow-none">
        <div class="p-8 border-b border-slate-50 dark:border-slate-700/50">
            <div class="max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari nama grup..." class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Nama Grup</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Diskone (%)</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Deskripsi</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                    @forelse($groups as $group)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/40 transition-all">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-900 dark:text-white leading-none tracking-tight">{{ $group->name }}</span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-black">{{ $group->discount_percentage }}%</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ $group->description ?? '-' }}</span>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="openEditModal('{{ $group->id }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete('{{ $group->id }}')" wire:confirm="Yakin ingin menghapus grup ini?" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-full text-slate-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-400 tracking-widest uppercase">Tidak ada grup pelanggan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($groups->hasPages())
            <div class="p-8 border-t border-slate-50 dark:border-slate-700/50 bg-slate-50/30 dark:bg-slate-900/40">
                {{ $groups->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in" x-on:keydown.escape.window="showModal = false">
        <div x-show="showModal" x-transition class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-lg overflow-hidden animate-slide-up">
            <div class="p-10">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-none">{{ $editId ? 'Edit Grup' : 'Tambah Grup Baru' }}</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Lengkapi informasi grup pelanggan</p>
                    </div>
                    <button wire:click="closeModal" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Nama Grup <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="Contoh: Member VIP" class="block w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-base font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white @error('name') border-rose-500 @enderror">
                        @error('name') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Diskon (%) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" wire:model="discount_percentage" step="0.01" class="block w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-base font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white @error('discount_percentage') border-rose-500 @enderror">
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-300">%</span>
                        </div>
                        @error('discount_percentage') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Deskripsi</label>
                        <textarea wire:model="description" placeholder="Keterangan singkat grup ini..." rows="3" class="block w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-base font-medium focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white resize-none"></textarea>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="button" wire:click="closeModal" class="flex-1 px-8 py-5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-all">Batal</button>
                        <button type="submit" class="flex-1 px-8 py-5 bg-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">Simpan Grup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
