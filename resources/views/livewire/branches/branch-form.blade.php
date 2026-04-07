<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between px-4 sm:px-0 gap-4">
            <a href="{{ route('branches.index') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                <div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm mr-3 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                Kembali
            </a>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $branch ? 'Edit Cabang' : 'Cabang Baru' }}
            </h2>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700/50 p-8 sm:p-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    @if(count($tenants) > 0)
                    <div class="md:col-span-2">
                        <label for="tenant_id" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Pilih Tenant</label>
                        <select id="tenant_id" wire:model.live="tenant_id" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                            <option value="">-- Pilih Tenant --</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('tenant_id') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Nama Cabang / Outlet</label>
                        <input type="text" id="name" wire:model="name" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold placeholder-gray-400 border-2" placeholder="Contoh: Cabang Jakarta Pusat">
                        @error('name') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Kode Cabang</label>
                        <input type="text" id="code" wire:model="code" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-mono font-bold placeholder-gray-400 border-2 uppercase" placeholder="JKT-01">
                        @error('code') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Telepon</label>
                        <input type="text" id="phone" wire:model="phone" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2" placeholder="021-xxxxxx">
                        @error('phone') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Alamat Lengkap</label>
                        <textarea id="address" wire:model="address" rows="3" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2" placeholder="Jl. Raya No. 123..."></textarea>
                        @error('address') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center space-x-3 ml-2 mt-4">
                        <input type="checkbox" id="is_main_warehouse" wire:model="is_main_warehouse" class="w-6 h-6 text-indigo-600 border-gray-300 rounded-lg focus:ring-indigo-500 transition-all cursor-pointer">
                        <label for="is_main_warehouse" class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest cursor-pointer mt-1">Gudang Utama</label>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Status</label>
                        <select id="status" wire:model="status" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-16 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center px-12 py-6 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-[2rem] font-black text-sm text-white uppercase tracking-[0.1em] hover:from-indigo-700 hover:to-indigo-800 focus:outline-none transition-all shadow-2xl shadow-indigo-500/40 active:scale-95 group">
                        Simpan Data Cabang
                        <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

