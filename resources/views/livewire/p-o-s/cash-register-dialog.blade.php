<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('isOpen', false)"></div>

        <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-700 animate-in zoom-in-95 duration-200">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl text-indigo-600 dark:text-indigo-400">
                            @if($mode === 'open')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3 3L22 4m-2 6v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ $mode === 'open' ? 'Buka Shift Kasir' : 'Tutup Shift Kasir' }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ $mode === 'open' ? 'Masukan saldo awal laci kasir.' : 'Verifikasi rekapitulasi penjualan.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="{{ $mode === 'open' ? 'openRegister' : 'closeRegister' }}" class="space-y-6">
                    @if($mode === 'open')
                        <x-form.input 
                            label="Saldo Awal (Modal Tunai)"
                            placeholder="Rp 0"
                            model="opening_balance"
                            isCurrency="true"
                            :error="$errors->first('opening_balance')"
                        />
                    @else
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 space-y-4">
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-black">Saldo Awal</span>
                                <span class="text-slate-900 dark:text-white">Rp {{ number_format($activeSession->opening_balance ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-black">Total Penjualan Tunai</span>
                                <span class="text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($activeSession->total_cash_sales ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if($activeSession->total_cash_in ?? 0 > 0)
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-black">Uang Masuk Tambahan</span>
                                <span class="text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($activeSession->total_cash_in, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($activeSession->total_cash_out ?? 0 > 0)
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-black">Uang Keluar</span>
                                <span class="text-rose-600 dark:text-rose-400">- Rp {{ number_format($activeSession->total_cash_out, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="h-px bg-slate-200 dark:bg-slate-700 mx-2"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-900 dark:text-white text-xs font-black uppercase tracking-widest">Ekspektasi Kas</span>
                                <span class="text-indigo-600 dark:text-indigo-400 font-black text-lg">Rp {{ number_format($activeSession->expected_cash ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <x-form.input 
                            label="Kas yang Diserahkan (Aktual)"
                            placeholder="Rp 0"
                            model="closing_cash_submitted"
                            isCurrency="true"
                            :error="$errors->first('closing_cash_submitted')"
                        />

                        <x-form.textarea 
                            label="Catatan Shift"
                            placeholder="Tuliskan catatan jika ada selisih..."
                            model="notes"
                            rows="2"
                        />
                    @endif

                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-[0.98]">
                            {{ $mode === 'open' ? 'BUKA SEKARANG' : 'TUTUP & SELESAI' }}
                        </button>
                        @if($mode === 'close')
                            <button type="button" wire:click="$set('isOpen', false)" class="w-full py-4 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 font-bold rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all active:scale-[0.98]">
                                BATAL
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($showAdjustmentModal)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showAdjustmentModal', false)"></div>

        <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-700">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">
                        {{ $adjustmentType === 'cash_in' ? 'Uang Masuk' : 'Uang Keluar' }}
                    </h3>
                    <button wire:click="$set('showAdjustmentModal', false)" class="p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="processAdjustment" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah</label>
                        <input type="text" wire:model="adjustmentAmount" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Rp 0">
                        @error('adjustmentAmount') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alasan</label>
                        <select wire:model="adjustmentReason" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Pilih alasan...</option>
                            @if($adjustmentType === 'cash_in')
                                <option value="petty_cash">Tambah Modal Kas Kecil</option>
                                <option value="refund_void">Refund/Pembatalan</option>
                                <option value="other_in">Lainnya</option>
                            @else
                                <option value="petty_expense">Pengeluaran Kas Kecil</option>
                                <option value="bank_deposit">Setor ke Bank</option>
                                <option value="cash_withdrawal">Tarik Tunai</option>
                                <option value="other_out">Lainnya</option>
                            @endif
                        </select>
                        @error('adjustmentReason') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                        <textarea wire:model="adjustmentNotes" rows="2" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Detail tambahan..."></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" wire:click="$set('showAdjustmentModal', false)" class="flex-1 py-3 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 font-bold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            BATAL
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all">
                            SIMPAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>