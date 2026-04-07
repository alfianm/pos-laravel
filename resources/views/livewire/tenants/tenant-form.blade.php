<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between px-4 sm:px-0 gap-4">
            <a href="{{ route('tenants.index') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                <div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm mr-3 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                Kembali ke Daftar
            </a>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $tenant ? 'Edit Tenant' : 'Tenant Baru' }}
            </h2>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700/50 p-8 sm:p-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Nama Bisnis / Perusahaan</label>
                        <input type="text" id="name" wire:model="name" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold placeholder-gray-400 border-2" placeholder="Masukkan nama resmi bisnis">
                        @error('name') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Kode Unik Sistem</label>
                        <input type="text" id="code" wire:model="code" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-mono font-bold placeholder-gray-400 border-2 uppercase" placeholder="TRIAL24">
                        @error('code') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Status Keanggotaan</label>
                        <div class="relative">
                            <select id="status" wire:model="status" class="w-full appearance-none bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('status') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Mata Utama</label>
                        <input type="text" id="currency" wire:model="currency" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2" placeholder="IDR">
                        @error('currency') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Zona Waktu</label>
                        <div class="relative">
                            <select id="timezone" wire:model="timezone" class="w-full appearance-none bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                                <option value="Asia/Jakarta">Indonesia Barat (WIB)</option>
                                <option value="Asia/Makassar">Indonesia Tengah (WITA)</option>
                                <option value="Asia/Jayapura">Indonesia Timur (WIT)</option>
                                <option value="UTC">Universal Time (UTC)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('timezone') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-16 flex flex-col sm:flex-row items-center justify-between gap-6 px-1">
                    <p class="text-xs text-gray-400 font-bold max-w-xs">* Pastikan Kode Unik tidak berubah jika sudah memiliki data operasional yang terkait.</p>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center px-12 py-6 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-[2rem] font-black text-sm text-white uppercase tracking-[0.1em] hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-2xl shadow-indigo-500/40 active:scale-95 group">
                        Simpan Data Tenant
                        <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

