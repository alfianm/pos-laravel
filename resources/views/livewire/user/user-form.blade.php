<div class="max-w-5xl mx-auto space-y-8 animate-fade-in-down">
    <div class="flex items-center gap-4">
        <a href="{{ route('settings.users.index') }}" wire:navigate 
           class="p-2.5 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 hover:shadow-sm transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight tracking-tight">{{ $isEdit ? 'Ubah Profil User' : 'Tambah User Baru' }}</h1>
            <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold text-[10px]">Atur kredensial, peran, dan akses cabang.</p>
        </div>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
        <!-- Main Form Data -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 p-8 space-y-6">
                <h3 class="text-xs font-black text-gray-900 tracking-widest uppercase mb-4">Informasi Akun</h3>
                
                <div class="space-y-2">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nama Lengkap</label>
                    <input type="text" wire:model.live="name" placeholder="Nama lengkap user..." 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('name') ring-2 ring-rose-500/20 @enderror">
                    @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Email</label>
                        <input type="email" wire:model.live="email" placeholder="email@domain.com" 
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('email') ring-2 ring-rose-500/20 @enderror">
                        @error('email') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Phone (Opsional)</label>
                        <input type="text" wire:model.live="phone" placeholder="08xxxxxxxx" 
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Password {{ $isEdit ? '(Biarkan kosong jika tidak diubah)' : '' }}</label>
                    <input type="password" wire:model.live="password" placeholder="Min. 8 karakter..." 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('password') ring-2 ring-rose-500/20 @enderror">
                    @error('password') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Branch Assignment -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 p-8 space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-black text-gray-900 tracking-widest uppercase">Akses Cabang</h3>
                    <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-tighter">{{ count($selected_branches) }} Terpilih</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($branches as $branch)
                        <label class="relative flex items-center p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-indigo-50/50 transition-all group overflow-hidden border-2 {{ in_array($branch->id, $selected_branches) ? 'border-indigo-600 bg-indigo-50' : 'border-transparent' }}">
                            <input type="checkbox" wire:model.live="selected_branches" value="{{ $branch->id }}" class="sr-only">
                            <div class="flex flex-col flex-1">
                                <span class="text-sm font-black text-gray-900 tracking-tight">{{ $branch->name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $branch->code }}</span>
                            </div>
                            @if(in_array($branch->id, $selected_branches))
                                <div class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center text-white scale-110 shadow-lg shadow-indigo-500/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @endif
                        </label>
                    @endforeach
                </div>
                @error('selected_branches') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 p-8 h-fit sticky top-6 space-y-8">
                <div>
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Role / Peran</label>
                    <select wire:model.live="role" 
                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all mt-2">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ strtoupper($r->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Cabang Aktif Saat Ini</label>
                    <select wire:model.live="active_branch_id" 
                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all mt-2 @error('active_branch_id') ring-2 ring-rose-500/20 @enderror">
                        <option value="">-- Pilih Cabang Login --</option>
                        @foreach($branches as $branch)
                            @if(in_array($branch->id, $selected_branches))
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('active_branch_id') <p class="text-[10px] font-bold text-rose-500 mt-2 ml-1 leading-tight">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 space-y-3">
                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-sm hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <span wire:loading.remove>{{ $isEdit ? 'SIMPAN PERUBAHAN' : 'BUAT SEKARANG' }}</span>
                        <span wire:loading>PROSES...</span>
                        <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    </button>

                    <a href="{{ route('settings.users.index') }}" wire:navigate
                       class="w-full py-4 bg-white border border-gray-100 text-gray-500 rounded-2xl font-black text-sm hover:bg-gray-50 transition-all flex items-center justify-center">
                        BATALKAN
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
