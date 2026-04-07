<div>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex mb-4 text-xs font-bold text-slate-400 uppercase tracking-widest gap-2">
                        <a href="{{ route('master-data.products') }}" wire:navigate class="hover:text-indigo-500 transition-colors">Produk</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ $isEdit ? 'Edit Mode' : 'Entry Baru' }}</span>
                    </nav>
                    <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-none">
                        {{ $isEdit ? 'Edit Produk' : 'Buat Produk Baru' }}
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm font-medium">Lengkapi informasi produk secara detail untuk manajemen stok yang lebih baik.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('master-data.products') }}" wire:navigate class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-500 dark:text-slate-400 font-bold text-xs uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                        Batalkan
                    </a>
                    <button type="submit" form="productForm" class="px-8 py-3 bg-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                        {{ $isEdit ? 'Update Produk' : 'Simpan Produk' }}
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="save" id="productForm" class="space-y-8">
                {{-- Row 1: Identity & Classification --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {{-- Informasi Utama --}}
                    <div class="lg:col-span-8 bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-10 shadow-xl shadow-slate-200/40 dark:shadow-none">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl font-black">1</div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Identitas Produk</h3>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Informasi dasar & kode unik</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input label="Kode Produk Internal" placeholder="PRD-XXXXXXXX" model="code" :error="$errors->first('code')" />
                                <x-form.input label="Nomor SKU (Stock Keeping Unit)" placeholder="SKU-XXXXXXXX" model="sku" :error="$errors->first('sku')" />
                            </div>

                            <x-form.input label="Nama Lengkap Produk" placeholder="Masukan nama lengkap produk sesuai merk & tipe" model="name" :error="$errors->first('name')" />

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2">
                                    <x-form.input label="Barcode / GTIN / EAN" placeholder="Scan barcode produk jika ada" model="barcode" :error="$errors->first('barcode')" />
                                </div>
                                <div class="md:col-span-1">
                                    <x-form.select label="Tipe Produk" model="type" wire:model.live="type" :error="$errors->first('type')">
                                        <option value="single">Single</option>
                                        <option value="variable">Variable</option>
                                        <option value="service">Jasa</option>
                                    </x-form.select>
                                </div>
                            </div>
                            <x-form.textarea label="Keterangan / Deskripsi Produk" placeholder="Tuliskan spesifikasi teknis atau detail produk di sini..." model="description" :error="$errors->first('description')" rows="3" />
                        </div>
                    </div>

                    {{-- Sidebar Style: Media & Meta --}}
                    <div class="lg:col-span-4 space-y-8">
                        {{-- Photo --}}
                        <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-8 shadow-xl shadow-slate-200/40 dark:shadow-none">
                            <div class="relative aspect-square w-full bg-slate-50 dark:bg-slate-900 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden group">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($old_image_url)
                                    <img src="{{ $old_image_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm text-slate-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lampirkan Foto</span>
                                    </div>
                                @endif
                                <label class="absolute inset-0 bg-indigo-600/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center cursor-pointer">
                                    <span class="px-6 py-2 bg-white text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg">Upload</span>
                                    <input type="file" wire:model="image" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>

                        {{-- Classification Mini --}}
                        <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-8 shadow-xl shadow-slate-200/40 dark:shadow-none">
                            <x-form.toggle label="Produk Aktif" model="is_active" />
                            <div class="mt-6 space-y-4">
                                <x-form.select label="Kategori" model="category_id">
                                    <option value="">Tanpa Kategori</option>
                                    @foreach($categories as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                                </x-form.select>
                                <x-form.select label="Brand" model="brand_id">
                                    <option value="">Tanpa Brand</option>
                                    @foreach($brands as $brand) <option value="{{ $brand->id }}">{{ $brand->name }}</option> @endforeach
                                </x-form.select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Full Width Pricing Section --}}
                <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 overflow-hidden shadow-xl shadow-slate-200/40 dark:shadow-none">
                    <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/30 dark:bg-slate-900/40">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl font-black">2</div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Konfigurasi Harga & Varian</h3>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Atur penomoran harga {{ $type === 'variable' ? 'per variasi' : 'satuan' }}</p>
                                </div>
                            </div>
                            @if($type === 'variable')
                                <button type="button" wire:click="addVariant" class="px-8 py-3 bg-indigo-600 text-white text-[10px] font-black rounded-xl hover:bg-indigo-700 transition-all uppercase tracking-widest shadow-lg shadow-indigo-500/20 active:scale-95">
                                    + Tambah Varian
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="p-10">
                        @if($type === 'variable')
                            <div class="space-y-6">
                                <div class="hidden md:grid grid-cols-12 gap-6 px-6 pb-6 border-b border-slate-50 dark:border-slate-700 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <div class="col-span-3">Label Varian</div>
                                    <div class="col-span-2">SKU Spesifik</div>
                                    <div class="col-span-3">Harga Jual</div>
                                    <div class="col-span-3">Harga Pokok</div>
                                    <div class="col-span-1 text-center">Aksi</div>
                                </div>

                                <div class="space-y-4">
                                    @foreach($variants as $index => $variant)
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 p-6 bg-slate-50/50 dark:bg-slate-900/40 rounded-3xl md:items-center hover:bg-slate-50 dark:hover:bg-slate-900 transition-all group animate-fade-in-down border border-transparent hover:border-slate-100 dark:hover:border-slate-800">
                                            <div class="md:col-span-3">
                                                <input type="text" wire:model="variants.{{ $index }}.name" placeholder="Ukuran/Warna..." class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl text-base font-bold px-5 py-4 focus:ring-4 focus:ring-indigo-500/10 dark:text-white transition-all shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <input type="text" wire:model="variants.{{ $index }}.sku" placeholder="SKU-VAR..." class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-black px-5 py-4 focus:ring-4 focus:ring-indigo-500/10 dark:text-white font-mono uppercase transition-all shadow-sm">
                                            </div>
                                            <div class="md:col-span-3">
                                                <div x-data="{ display: $wire.get('variants.{{ $index }}.price') ? window.formatRupiah($wire.get('variants.{{ $index }}.price')) : '' }" class="relative">
                                                    <input type="text" x-model="display" x-on:input="let numeric = window.parseRupiah($event.target.value); $wire.set('variants.{{ $index }}.price', numeric); display = window.formatRupiah(numeric);" placeholder="Rp 0" class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl text-base font-black text-emerald-600 dark:text-emerald-400 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                                                </div>
                                            </div>
                                            <div class="md:col-span-3">
                                                <div x-data="{ display: $wire.get('variants.{{ $index }}.cost') ? window.formatRupiah($wire.get('variants.{{ $index }}.cost')) : '' }" class="relative">
                                                    <input type="text" x-model="display" x-on:input="let numeric = window.parseRupiah($event.target.value); $wire.set('variants.{{ $index }}.cost', numeric); display = window.formatRupiah(numeric);" placeholder="Rp 0" class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl text-base font-black text-slate-500 focus:ring-4 focus:ring-slate-500/10 transition-all shadow-sm">
                                                </div>
                                            </div>
                                            <div class="md:col-span-1 flex items-center justify-center">
                                                <button type="button" wire:click="removeVariant({{ $index }})" class="p-4 text-rose-400 hover:text-rose-600 hover:bg-white dark:hover:bg-slate-800 rounded-2xl transition-all shadow-sm group-hover:shadow-md">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if(empty($variants))
                                        <div class="py-20 text-center border-2 border-dashed border-slate-100 dark:border-slate-700 rounded-[3rem]">
                                            <p class="text-base font-bold text-slate-400 uppercase tracking-widest leading-loose">Pilih "Variable" untuk membuat banyak variasi harga.<br>Klik tombol di atas untuk memulai.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                                <x-form.input label="Harga Modal (HPP)" placeholder="Rp 0" model="cost_price" isCurrency="true" :error="$errors->first('cost_price')" />
                                <x-form.input label="Harga Beli" placeholder="Rp 0" model="purchase_price" isCurrency="true" :error="$errors->first('purchase_price')" />
                                <x-form.input label="Harga Jual" placeholder="Rp 0" model="selling_price" isCurrency="true" :error="$errors->first('selling_price')" />
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Additional Options --}}
                <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 p-10 shadow-xl shadow-slate-200/40 dark:shadow-none">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-10 pb-4 border-b border-slate-50 dark:border-slate-700/50">3. Kontrol & Inventaris</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <x-form.toggle 
                            label="Lacak Stok" 
                            model="track_stock" 
                            note="Otomatis kurangi stok saat transaksi & cegah penjualan saat habis."
                        />
                        <x-form.toggle 
                            label="Input Desimal" 
                            model="allow_decimal" 
                            note="Memungkinkan pengisian angka pecahan (contoh: 0.5 kg, 1.2 L)."
                        />
                        <x-form.toggle 
                            label="Gunakan Expired" 
                            model="has_expiry" 
                            note="Aktifkan pencatatan tanggal kadaluarsa untuk produk ini."
                        />
                        <x-form.select label="Satuan" model="unit_id">
                            <option value="">Pilih Satuan...</option>
                            @foreach($units as $unit) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endforeach
                        </x-form.select>
                    </div>
                </div>

                {{-- Summary / Save Action --}}
                <div class="pt-10 flex justify-end gap-4">
                    <button type="submit" class="px-16 py-6 bg-indigo-600 text-white font-black text-sm uppercase tracking-[0.2em] rounded-[2rem] hover:bg-slate-900 transition-all shadow-2xl shadow-indigo-500/30 active:scale-[0.98]">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Finalisasi & Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        input:focus { border-color: #6366f1 !important; }
    </style>
</div>
