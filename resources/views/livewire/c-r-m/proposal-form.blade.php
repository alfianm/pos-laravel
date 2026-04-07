<div class="py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('crm.proposals.index') }}" wire:navigate class="p-3 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Buat Penawaran Harga</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium italic">Draft penawaran profesional untuk prospek terpilih.</p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-8 pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Detail Main --}}
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl border border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center justify-between mb-8 border-b border-slate-50 dark:border-slate-700 pb-6">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">Daftar Produk / Layanan</h3>
                            <button type="button" wire:click="addItem" class="text-indigo-600 dark:text-indigo-400 text-xs font-black uppercase tracking-widest flex items-center gap-2 hover:translate-x-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Item
                            </button>
                        </div>

                        <div class="space-y-4">
                            @foreach($items as $index => $item)
                            <div class="grid grid-cols-12 gap-4 items-end bg-slate-50 dark:bg-slate-900/30 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 group relative">
                                <div class="col-span-12 md:col-span-6 space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Produk</label>
                                    <select wire:model.live="items.{{ $index }}.product_id" class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-bold focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white">
                                        <option value="">Pilih Produk...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (Rp {{ number_format($product->price, 0) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4 md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Qty</label>
                                    <input type="number" step="0.1" wire:model.live="items.{{ $index }}.quantity" class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-bold focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white text-center">
                                </div>
                                <div class="col-span-6 md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Price (Rp)</label>
                                    <input type="number" wire:model.live="items.{{ $index }}.unit_price" class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-bold focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white">
                                </div>
                                <div class="col-span-2 md:col-span-1 pb-1 text-right">
                                    <button type="button" wire:click="removeItem({{ $index }})" class="p-3 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-700 space-y-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Terms & Conditions</label>
                                <textarea wire:model="terms_conditions" rows="3" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-medium" placeholder="E.g. Valid for 14 days, Cash on delivery..."></textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Internal Notes</label>
                                <textarea wire:model="notes" rows="2" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-medium italic" placeholder="Only visible for your team..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar: Total & Config --}}
                <div class="space-y-8">
                    <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-600/30">
                        <label class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] mb-4 block leading-none">Total Penawaran</label>
                        <div class="text-4xl font-black tracking-tight mb-8">Rp {{ number_format($total_amount, 0, ',', '.') }}</div>
                        
                        <div class="space-y-4 pt-4 border-t border-white/10">
                            <div class="flex justify-between text-xs font-bold text-indigo-100 uppercase tracking-widest">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-bold text-indigo-100 uppercase tracking-widest">
                                <span>Discount</span>
                                <input type="number" wire:model.live="discount_amount" class="w-24 px-3 py-1 bg-white/10 border border-white/20 rounded-lg text-white font-black text-right focus:outline-none focus:bg-white/20">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl border border-slate-100 dark:border-slate-700/50 space-y-6">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-4 border-b border-slate-50 dark:border-slate-700 pb-4">Pengaturan Proposal</h4>
                        
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Proposal</label>
                                <input type="text" wire:model="proposal_no" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-black dark:text-white focus:outline-none">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Proposal</label>
                                <input type="date" wire:model="proposal_date" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-bold dark:text-white focus:outline-none">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Berlaku Hingga</label>
                                <input type="date" wire:model="valid_until" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-bold dark:text-white focus:outline-none">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kirim Ke Prospek</label>
                                <select wire:model="lead_id" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-xs font-bold dark:text-white focus:outline-none">
                                    <option value="">Pilih Lead...</option>
                                    @foreach($leads as $lead)
                                        <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-[2rem] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs flex justify-center items-center gap-3">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                 Simpan Proposal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
