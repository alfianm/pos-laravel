<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Supplier & Vendor</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola daftar pemasok barang dagangan Anda.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Supplier
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 px-4 sm:px-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
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
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Kode, Nama, CP..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
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
                            <th class="px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Vendor</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Kontak Person</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Kontak</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Status</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($suppliers as $supplier)
                        <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-gray-500 font-extrabold shadow-sm border border-gray-200 dark:border-gray-600">
                                        {{ substr($supplier->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('master-data.suppliers.show', $supplier->id) }}" wire:navigate class="text-base font-extrabold text-gray-900 dark:text-white hover:text-indigo-600 transition-colors uppercase tracking-tight">{{ $supplier->name }}</a>
                                        <div class="text-xs text-indigo-500 font-mono font-bold">{{ $supplier->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300">
                                    {{ $supplier->contact_person ?: '-' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-xs font-bold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $supplier->phone ?: '-' }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight">
                                    {{ $supplier->city ?: 'No City Info' }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($supplier->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 uppercase tracking-widest">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 uppercase tracking-widest">
                                        {{ $supplier->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('master-data.suppliers.show', $supplier->id) }}" wire:navigate class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 rounded-xl transition-all" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <button wire:click="openEditModal('{{ $supplier->id }}')" class="p-2.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete('{{ $supplier->id }}')" wire:confirm="Yakin ingin menghapus supplier ini?" class="p-2.5 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl transition-all" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-10 rounded-[3rem] mb-6">
                                        <svg class="w-20 h-20 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-extrabold text-xl tracking-tight">Data Supplier Kosong.</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Anda belum mencatat supplier pasokan satupun.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppliers->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0" x-data @keydown.escape.window="$wire.closeModal()">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" wire:click="closeModal"></div>
        
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $editId ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lengkapi informasi supplier di bawah ini.</p>
                    </div>
                    <button wire:click="closeModal" class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Perusahaan / Supplier <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="name" placeholder="Masukan nama perusahaan" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('name') border-rose-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode Vendor <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="code" placeholder="Auto-generated" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('code') border-rose-500 @enderror">
                            @error('code')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                            <input type="text" wire:model="contact_person" placeholder="Nama PIC" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('contact_person') border-rose-500 @enderror">
                            @error('contact_person')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email Perusahaan</label>
                            <input type="email" wire:model="email" placeholder="supplier@perusahaan.com" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('email') border-rose-500 @enderror">
                            @error('email')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Telepon / HP</label>
                            <input type="text" wire:model="phone" placeholder="021-123456 / 0812..." class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('phone') border-rose-500 @enderror">
                            @error('phone')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Alamat kantor</label>
                            <textarea wire:model="address" placeholder="Jalan, Komplek, RT/RW, Kota..." rows="2" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white resize-none @error('address') border-rose-500 @enderror"></textarea>
                            @error('address')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kota</label>
                            <input type="text" wire:model="city" placeholder="Nama kota" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('city') border-rose-500 @enderror">
                            @error('city')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status Kerjasama <span class="text-rose-500">*</span></label>
                            <select wire:model="status" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white @error('status') border-rose-500 @enderror">
                                <option value="active">Active (Aktif)</option>
                                <option value="inactive">Inactive (Non-Aktif)</option>
                                <option value="suspended">Suspended (Ditangguhkan)</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="closeModal" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-indigo-600 border border-transparent rounded-2xl font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30 active:scale-[0.98]">
                            {{ $editId ? 'Update Supplier' : 'Simpan Supplier' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>