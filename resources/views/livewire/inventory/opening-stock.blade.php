<div class="max-w-4xl mx-auto space-y-6 pb-20">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Stok Awal</h1>
            <p class="text-slate-500 mt-1 font-medium italic">Inisialisasi persediaan gudang/toko</p>
        </div>
        <a href="{{ route('inventory.index') }}" wire:navigate class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-600 font-bold text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8 lg:p-10">
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Catatan</label>
                    <textarea wire:model="notes" 
                        class="w-full bg-slate-50 border-slate-100 rounded-3xl py-4 px-6 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2 placeholder:text-slate-400/60"
                        rows="2"></textarea>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar Produk & Stok</label>
                        <button type="button" wire:click="addItem" class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.1em] hover:text-indigo-700">
                            + Tambah Baris
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($items as $index => $item)
                        <div class="grid grid-cols-12 gap-4 p-4 bg-slate-50 rounded-3xl border border-slate-100 items-start">
                            <div class="col-span-12 lg:col-span-6">
                                <select wire:model="items.{{ $index }}.product_id" class="w-full bg-white border-slate-100 rounded-2xl py-3 px-5 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-5 lg:col-span-2">
                                <input type="number" wire:model="items.{{ $index }}.qty" step="0.0001" placeholder="Jumlah" class="w-full bg-white border-slate-100 rounded-2xl py-3 px-5 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2">
                            </div>
                            <div class="col-span-5 lg:col-span-3">
                                <input type="number" wire:model="items.{{ $index }}.unit_cost" step="0.01" placeholder="Harga Pokok" class="w-full bg-white border-slate-100 rounded-2xl py-3 px-5 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2">
                            </div>
                            <div class="col-span-2 lg:col-span-1 flex justify-center pt-2">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-rose-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-end">
                <button type="button" wire:click="save" class="px-10 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] transition-all shadow-2xl shadow-indigo-500/40 active:scale-95">
                    Simpan Stok Awal
                </button>
            </div>
        </div>
    </div>
</div>
