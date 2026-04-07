<div class="max-w-4xl mx-auto space-y-8 animate-fade-in-down">
    <div class="flex items-center gap-4">
        <a href="{{ route('membership.tiers.index') }}" wire:navigate 
           class="p-2.5 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 hover:shadow-sm transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $isEdit ? 'Ubah Tier' : 'Tambah Tier Baru' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi tingkatan reward dan multiplier poin.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 overflow-hidden">
                <div class="p-8">
                    <form wire:submit.prevent="save" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nama Tingkatan Tier</label>
                            <input type="text" wire:model.live="name" placeholder="Contoh: Gold, Platinum, VIP" 
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-blue-500/20 transition-all @error('name') ring-2 ring-rose-500/20 @enderror">
                            @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Minimal Pengeluaran (Rp)</label>
                                <input type="number" wire:model.live="min_spending" placeholder="0" 
                                       class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-blue-500/20 transition-all @error('min_spending') ring-2 ring-rose-500/20 @enderror">
                                @error('min_spending') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                <p class="text-[10px] text-gray-400 leading-tight ml-1">Minimal total belanja pelanggan untuk masuk ke tingkatan ini.</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Multiplier Poin</label>
                                <input type="number" step="0.1" wire:model.live="point_multiplier" placeholder="1.0" 
                                       class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-blue-500/20 transition-all @error('point_multiplier') ring-2 ring-rose-500/20 @enderror">
                                @error('point_multiplier') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                <p class="text-[10px] text-gray-400 leading-tight ml-1">Contoh: 1.5 berarti pelanggan mendapat 1.5x poin normal.</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-50 flex items-center justify-end gap-3">
                            <button type="submit" wire:loading.attr="disabled"
                                    class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-black text-sm hover:bg-blue-700 shadow-xl shadow-blue-500/30 transition-all active:scale-95 disabled:opacity-50">
                                <span wire:loading.remove>{{ $isEdit ? 'SIMPAN PERUBAHAN' : 'BUAT TIER SEKARANG' }}</span>
                                <span wire:loading>MENYIMPAN...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tier Preview Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 overflow-hidden h-fit sticky top-6">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-xs font-black text-gray-900 tracking-widest uppercase">Live Preview</h3>
                </div>
                <div class="p-8">
                    <div class="aspect-[4/3] bg-gradient-to-br from-blue-600 via-indigo-700 to-indigo-900 rounded-[2.5rem] p-8 flex flex-col justify-between text-white relative overflow-hidden shadow-2xl shadow-blue-500/20 group">
                        <!-- Abstract overlay -->
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150"></div>
                        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-400/20 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-[0.65rem] font-black text-blue-100 uppercase tracking-[0.2em] leading-none mb-2">Membership Tier</p>
                                <h4 class="text-3xl font-black tracking-tighter leading-none">{{ $name ?: 'NAMA TIER' }}</h4>
                            </div>
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.519-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-end justify-between">
                            <div>
                                <p class="text-[0.65rem] font-bold text-blue-100 uppercase tracking-widest leading-none mb-2">Benefit Reward</p>
                                <p class="text-2xl font-black tracking-tight leading-none">{{ (float)$point_multiplier }}x Poin</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.65rem] font-bold text-blue-100 uppercase tracking-widest leading-none mb-2">Min. Spend</p>
                                <p class="text-base font-black tracking-tight leading-none">Rp{{ number_format((float)$min_spending, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-amber-50 rounded-2xl border border-amber-100 border-dashed">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[10px] font-bold text-amber-700 leading-relaxed uppercase tracking-wide">PENTING: Tier bersifat otomatis. Pelanggan akan naik standar tier setelah total akumulasi belanjanya menyentuh "Minimal Pengeluaran".</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
