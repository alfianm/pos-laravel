<div class="max-w-4xl mx-auto space-y-8 animate-fade-in-down">
    <div class="flex items-center gap-4">
        <a href="{{ route('membership.vouchers.index') }}" wire:navigate 
           class="p-2.5 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 hover:shadow-sm transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $isEdit ? 'Ubah Voucher' : 'Buat Voucher Baru' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi diskon, periode berlaku, dan batasan penggunaan.</p>
        </div>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
        <!-- Main Form Data -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 p-8 space-y-6">
                <!-- Voucher Code -->
                <div class="space-y-2">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Kode Voucher</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model.live="code" placeholder="CONTOH: PROMOAwalBulan" 
                               class="flex-1 px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all uppercase @error('code') ring-2 ring-rose-500/20 @enderror">
                        <button type="button" wire:click="generateCode" 
                                class="px-5 bg-indigo-50 text-indigo-600 rounded-2xl font-bold text-xs hover:bg-indigo-100 transition-colors uppercase tracking-widest">
                            Acak
                        </button>
                    </div>
                    @error('code') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Type & Value -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Jenis Diskon</label>
                        <select wire:model.live="type" 
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            <option value="fixed">Nominal Tetap (Rp)</option>
                            <option value="percentage">Persentase (%)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nilai Diskon</label>
                        <div class="relative">
                            <input type="number" wire:model.live="value" placeholder="0" 
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('value') ring-2 ring-rose-500/20 @enderror">
                            <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none font-black text-gray-400">
                                {{ $type === 'fixed' ? 'Rp' : '%' }}
                            </div>
                        </div>
                        @error('value') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Min Order -->
                <div class="space-y-2">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Minimal Belanja (Rp)</label>
                    <input type="number" wire:model.live="min_order_amount" placeholder="0" 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <p class="text-xs text-gray-400 ml-1">Voucher hanya aktif jika total belanja mencapai nilai ini.</p>
                </div>
            </div>

            <!-- Validity & Limits -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 p-8 space-y-6">
                <h3 class="text-xs font-black text-gray-900 tracking-widest uppercase mb-4">Masa Berlaku & Batas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Mulai Berlaku</label>
                        <input type="datetime-local" wire:model.live="starts_at" 
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Berakhir Pada (Opsional)</label>
                        <input type="datetime-local" wire:model.live="ends_at" 
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Batas Penggunaan Total</label>
                    <input type="number" wire:model.live="usage_limit" placeholder="Kosongkan jika tidak terbatas" 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-300 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <p class="text-xs text-gray-400 ml-1">Jumlah maksimal berapa kali voucher ini bisa digunakan secara keseluruhan.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 p-8 h-fit sticky top-6">
                <div class="aspect-square bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-900 rounded-[2rem] p-8 flex flex-col justify-center items-center text-white relative overflow-hidden shadow-2xl mb-8 group">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                    
                    <div class="text-[0.6rem] font-black text-indigo-300 uppercase tracking-[0.4em] mb-4">Voucher Preview</div>
                    <div class="text-3xl font-black tracking-tight mb-2 uppercase break-all text-center">{{ $code ?: 'KODE' }}</div>
                    <div class="px-6 py-2 bg-white/20 backdrop-blur-md rounded-2xl font-black text-sm uppercase tracking-widest ring-1 ring-white/30">
                        {{ $type === 'fixed' ? 'Rp' . number_format((float)$value, 0, ',', '.') : $value . '%' }} OFF
                    </div>
                    <div class="mt-6 text-[0.6rem] font-bold text-indigo-200 uppercase tracking-widest text-center opacity-70">
                        Min. Spend Rp{{ number_format((float)$min_order_amount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="space-y-3">
                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-sm hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <span wire:loading.remove>{{ $isEdit ? 'SIMPAN PERUBAHAN' : 'TERBITKAN VOUCHER' }}</span>
                        <span wire:loading>PROSES...</span>
                        <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    </button>

                    <a href="{{ route('membership.vouchers.index') }}" wire:navigate
                       class="w-full py-4 bg-white border border-gray-100 text-gray-500 rounded-2xl font-black text-sm hover:bg-gray-50 transition-all flex items-center justify-center">
                        BATALKAN
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
