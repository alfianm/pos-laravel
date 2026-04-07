<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('crm.leads.index') }}" wire:navigate class="p-3 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $isEdit ? 'Ubah Data Lead' : 'Tambah Lead Baru' }}</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Lengkapi informasi prospek di bawah ini untuk memulai pelacakan.</p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-8">
            {{-- Personal Info Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50">
                <h2 class="text-xl font-black text-slate-900 dark:text-white mb-8 border-b border-slate-50 dark:border-slate-700 pb-4">Informasi Kontak</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="Masukkan nama...">
                        @error('name') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="email@example.com">
                        @error('email') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">No. HP / WhatsApp</label>
                        <input type="text" wire:model="phone" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="08xxxxxxxx">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Pilih Cabang <span class="text-rose-500">*</span></label>
                        <select wire:model="branch_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-8 space-y-2">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Alamat Domisili</label>
                    <textarea wire:model="address" rows="3" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="Tulis alamat jika ada..."></textarea>
                </div>
            </div>

            {{-- Source & Status Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50">
                <h2 class="text-xl font-black text-slate-900 dark:text-white mb-8 border-b border-slate-50 dark:border-slate-700 pb-4">Klasifikasi Lead</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Sumber Lead <span class="text-rose-500">*</span></label>
                        <select wire:model="lead_source_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold uppercase tracking-widest">
                            <option value="">Pilih Sumber</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                        @error('lead_source_id') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Pilih Tahapan <span class="text-rose-500">*</span></label>
                        <select wire:model="lead_stage_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold uppercase tracking-widest">
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                        @error('lead_stage_id') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Assigned To</label>
                        <select wire:model="assigned_to" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-8 space-y-2">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Catatan Internal</label>
                    <textarea wire:model="notes" rows="4" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="Tuliskan info tambahan mengenai lead ini..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-12 pb-20">
                <a href="{{ route('crm.leads.index') }}" wire:navigate class="px-8 py-4 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black rounded-2xl border border-slate-100 dark:border-slate-700 hover:bg-slate-50 transition-all active:scale-95 uppercase tracking-widest text-xs">Batalkan</a>
                <button type="submit" class="px-12 py-4 bg-indigo-600 text-white font-black rounded-[2rem] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs">Simpan Data Lead</button>
            </div>
        </form>
    </div>
</div>
