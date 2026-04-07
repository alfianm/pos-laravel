<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('expenses.index') }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Catat Pengeluaran</h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Input pengeluaran kas operasional baru.</p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <x-form.input-group label="Tanggal Transaksi" error="date">
                        <input type="date" wire:model="date" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-bold">
                    </x-form.input-group>

                    <x-form.input-group label="Kategori Pengeluaran" error="expense_category_id">
                        <select wire:model="expense_category_id" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-bold">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </x-form.input-group>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Jumlah Pengeluaran (Rp)</label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-black">
                                Rp
                            </div>
                            <input type="number" wire:model="amount" class="block w-full pl-12 pr-4 py-5 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-3xl text-3xl font-black text-rose-500 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all shadow-inner">
                        </div>
                        @error('amount') <span class="text-rose-500 text-xs mt-2 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <x-form.input-group label="Metode Pembayaran" error="payment_method">
                        <select wire:model="payment_method" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-bold">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="bank_transfer">Transfer Bank</option>
                        </select>
                    </x-form.input-group>

                    <x-form.input-group label="Cabang Terkait" error="branch_id">
                        <select wire:model="branch_id" class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-bold text-slate-400" disabled>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </x-form.input-group>

                    <div class="md:col-span-2">
                        <x-form.input-group label="Catatan / Keterangan" error="notes">
                            <textarea wire:model="notes" rows="4" class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-3xl text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white font-medium" placeholder="Contoh: Bayar tagihan listrik Maret, Bensin kurir..."></textarea>
                        </x-form.input-group>
                    </div>
                </div>

                <div class="mt-12 flex justify-end gap-4 border-t border-slate-50 dark:border-slate-700 pt-8">
                    <a href="{{ route('expenses.index') }}" wire:navigate class="px-8 py-4 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-black rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all uppercase tracking-widest text-xs">Batal</a>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs">Simpan Pengeluaran</button>
                </div>
            </div>
        </form>
    </div>
</div>
