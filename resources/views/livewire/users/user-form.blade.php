<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between px-4 sm:px-0 gap-4">
            <a href="{{ route('users.index') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                <div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm mr-3 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                Kembali
            </a>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $user ? 'Edit Pengguna' : 'Pengguna Baru' }}
            </h2>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700/50 p-8 sm:p-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    @if(count($tenants) > 0)
                    <div class="md:col-span-2">
                        <label for="tenant_id" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Tenant Asal</label>
                        <select id="tenant_id" wire:model.live="tenant_id" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-5 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                            <option value="">-- SYSTEM / SUPER ADMIN --</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('tenant_id') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>
                    @endif

                    <!-- Basic Info -->
                    <div>
                        <label for="name" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Nama Lengkap</label>
                        <input type="text" id="name" wire:model="name" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-4 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                        @error('name') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">Email Login</label>
                        <input type="email" id="email" wire:model="email" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-4 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2">
                        @error('email') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="password" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-3 ml-1">
                            Password {!! $user ? '<span class="text-gray-400 normal-case font-medium ml-2">(Kosongkan jika tidak ingin diubah)</span>' : '' !!}
                        </label>
                        <input type="password" id="password" wire:model="password" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700 rounded-[1.5rem] py-4 px-8 text-lg text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold border-2 uppercase tracking-widest placeholder:normal-case placeholder:tracking-normal" placeholder="Min. 8 karakter">
                        @error('password') <span class="text-rose-500 text-xs font-bold mt-2 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-6 ml-1 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Hak Akses (Roles)
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($roles as $role)
                            <label class="relative flex items-center p-5 rounded-[1.5rem] border-2 cursor-pointer transition-all active:scale-95 {{ in_array($role->name, $selected_roles) ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20 shadow-lg shadow-indigo-500/10' : 'border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-800' }}">
                                <input type="checkbox" wire:model="selected_roles" value="{{ $role->name }}" class="hidden">
                                <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ str_replace('_', ' ', $role->name) }}</span>
                                @if(in_array($role->name, $selected_roles))
                                    <div class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full p-1 shadow-md">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        @error('selected_roles') <span class="text-rose-500 text-xs font-bold mt-4 ml-4 block italic">× {{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 mt-8">
                        <h3 class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-[0.2em] mb-6 ml-1 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            Penempatan Cabang
                        </h3>
                        @if($tenant_id || Auth::user()->hasRole('super_admin'))
                            @if(count($branches) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($branches as $branch)
                                <label class="relative flex items-center p-5 rounded-[1.5rem] border-2 cursor-pointer transition-all active:scale-95 {{ in_array($branch->id, $selected_branches) ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/20 shadow-lg shadow-emerald-500/10' : 'border-gray-100 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800' }}">
                                    <input type="checkbox" wire:model="selected_branches" value="{{ $branch->id }}" class="hidden">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ $branch->name }}</span>
                                        <span class="text-[0.6rem] text-gray-400 font-mono tracking-widest mt-0.5">{{ $branch->code }}</span>
                                    </div>
                                    @if(in_array($branch->id, $selected_branches))
                                        <div class="absolute -top-2 -right-2 bg-emerald-600 text-white rounded-full p-1 shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            @else
                            <div class="p-10 bg-gray-50/50 dark:bg-gray-900/30 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-gray-700/50 text-center">
                                <p class="text-sm text-gray-400 font-bold italic">Belum ada cabang terdaftar untuk tenant ini.</p>
                            </div>
                            @endif
                        @else
                        <div class="p-10 bg-gray-50/50 dark:bg-gray-900/30 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-gray-700/50 text-center">
                            <p class="text-sm text-gray-400 font-bold italic group-hover:text-indigo-500 transition-colors">Pilih tenant terlebih dahulu untuk melihat daftar cabang.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-20 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center px-12 py-6 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-[2.5rem] font-black text-sm text-white uppercase tracking-[0.1em] hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-2xl shadow-indigo-500/40 active:scale-95 overflow-hidden relative group">
                        <span class="relative z-10 flex items-center">
                            Simpan Data Pengguna
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                        <div class="absolute inset-0 bg-white/10 group-hover:translate-x-full transition-transform duration-500 -skew-x-12 -translate-x-full"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

