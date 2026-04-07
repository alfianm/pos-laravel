<div class="flex flex-col lg:flex-row min-h-screen bg-gray-50 dark:bg-gray-900 overflow-hidden font-sans">
    <!-- Left Column: Products -->
    <div class="flex-1 flex flex-col min-w-0 p-4 md:p-6 overflow-hidden transition-all duration-500 {{ count($cart) > 0 ? 'lg:mr-0' : 'max-w-7xl mx-auto' }}">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Cari & Pilih Produk</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pilih produk atau scan barcode untuk menambahkan ke pesanan.</p>
            </div>
            <div class="relative group w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="w-full pl-11 pr-6 py-3.5 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-3xl text-sm focus:outline-none focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-xl shadow-gray-200/40 dark:shadow-none transition-all dark:text-white">
            </div>
            
            {{-- Shift Status --}}
            <div class="flex items-center gap-3">
                @if($activeSession)
                    <div class="flex items-center gap-2">
                        <button wire:click="$dispatch('openCashRegister', {mode: 'cash_in'})" class="px-3 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-xs rounded-xl border border-emerald-200 dark:border-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span class="hidden sm:inline">Uang Masuk</span>
                        </button>
                        <button wire:click="$dispatch('openCashRegister', {mode: 'cash_out'})" class="px-3 py-2 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-xl border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            <span class="hidden sm:inline">Uang Keluar</span>
                        </button>
                    </div>
                    <div class="px-4 py-2 sm:px-5 sm:py-2.5 bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-200/50 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3 shadow-sm group/shift">
                        <div class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-[9px] font-black text-emerald-600/70 dark:text-emerald-400/60 uppercase tracking-widest leading-none">Shift Aktif</p>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ $activeSession->opened_at->format('H:i') }}</p>
                        </div>
                        <div class="sm:hidden border-l border-emerald-200/50 dark:border-emerald-500/20 pl-3">
                            <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 leading-none">{{ $activeSession->opened_at->format('H:i') }}</p>
                        </div>
                        <button wire:click="closeSession" class="ml-2 p-1.5 sm:p-2 bg-white dark:bg-slate-800 text-rose-500 border border-slate-100 dark:border-slate-700 rounded-xl hover:text-white hover:bg-rose-500 hover:border-rose-500 transition-all active:scale-90 shadow-sm" title="Tutup Shift">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </div>
                @else
                    <button wire:click="openSession" class="px-5 py-2.5 sm:px-7 sm:py-3.5 bg-indigo-600 text-white font-black text-[11px] sm:text-xs rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 flex items-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span class="tracking-widest uppercase">Buka Shift</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Categories Scroll --}}
        <div class="flex items-center gap-3 mb-8 overflow-x-auto pb-4 scrollbar-hide py-2 max-w-full">
            <button wire:click="$set('selected_category', 'all')" class="px-6 py-3 rounded-[1.5rem] flex items-center gap-2 whitespace-nowrap transition-all font-bold text-sm {{ $selected_category == 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/10' : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-100 dark:border-gray-700' }}">
                🏷️ Semua
            </button>
            @foreach($categories as $category)
                <button wire:click="$set('selected_category', '{{ $category->id }}')" class="px-6 py-3 rounded-[1.5rem] flex items-center gap-2 whitespace-nowrap transition-all font-bold text-sm {{ $selected_category == $category->id ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/10' : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-100 dark:border-gray-700' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto pr-2 pb-10 custom-scrollbar relative">
            @if(!$activeSession)
                <div class="absolute inset-0 z-20 bg-gray-50/60 dark:bg-gray-900/60 backdrop-blur-[2px] flex items-center justify-center p-6">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 text-center max-w-sm animate-fade-in-down">
                        <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-950/30 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Shift Belum Dibuka</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-8">Silakan buka shift kasir terlebih dahulu untuk mulai melayani pelanggan dan memilih produk.</p>
                        <button wire:click="openSession" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-95">
                            BUKA SHIFT SEKARANG
                        </button>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 {{ count($cart) > 0 ? 'lg:grid-cols-3' : 'lg:grid-cols-4 xl:grid-cols-5' }} gap-6 transition-all duration-500">
                @forelse($products as $product)
                    <div wire:click="addToCart('{{ $product->id }}')" 
                        class="bg-white dark:bg-gray-800 rounded-[2rem] border-2 border-transparent hover:border-indigo-500/50 dark:hover:border-indigo-400/50 shadow-xl shadow-gray-200/20 dark:shadow-none overflow-hidden group cursor-pointer active:scale-95 transition-all flex flex-col relative h-full">
                        
                        {{-- Image & Badge Section --}}
                        <div class="aspect-square bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center relative overflow-hidden flex-shrink-0">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:rotate-2 group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-50 dark:from-indigo-950/20 dark:to-slate-900/20">
                                    <span class="text-3xl sm:text-4xl filter grayscale group-hover:grayscale-0 transition-all duration-500">📦</span>
                                </div>
                            @endif

                            {{-- Stock Info Badge --}}
                            @php 
                                $stock = $product->inventories()->where('branch_id', auth()->user()->active_branch_id)->first();
                                $qty = $stock ? $stock->qty_available : 0;
                            @endphp
                            <div class="absolute top-3 left-3 z-10">
                                @if($qty > 10)
                                    <span class="px-3 py-1 bg-emerald-500/90 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">In Stock</span>
                                @elseif($qty > 0)
                                    <span class="px-3 py-1 bg-amber-500/90 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">{{ (int)$qty }} Left</span>
                                @else
                                    <span class="px-3 py-1 bg-rose-500/90 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg font-sans">Out</span>
                                @endif
                            </div>

                            {{-- Floating Action Icon --}}
                            <div class="absolute inset-0 bg-indigo-900/0 group-hover:bg-indigo-900/5 transition-all duration-500 flex items-center justify-center">
                                <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center shadow-2xl opacity-0 scale-50 group-hover:opacity-100 group-hover:scale-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Details Section --}}
                        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between bg-white dark:bg-gray-800">
                            <div>
                                <div class="flex items-center gap-2 mb-1.5 overflow-hidden">
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-white/5 text-[9px] font-black text-slate-500 dark:text-slate-400 rounded-lg uppercase tracking-wider truncate">
                                        {{ $product->category->name ?? 'General' }}
                                    </span>
                                </div>
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm leading-snug line-clamp-2 h-10 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $product->name }}
                                </h3>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-50 dark:border-white/5 flex items-end justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-gray-500 uppercase tracking-tighter mb-0.5">Price @if($product->activeBranchPrice) <span class="text-indigo-400 font-black tracking-widest">(BRANCH)</span> @endif</span>
                                    <span class="text-sm sm:text-base font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                                        @php 
                                            $displayPrice = $product->activeBranchPrice ? $product->activeBranchPrice->retail_price : $product->selling_price;
                                        @endphp
                                        Rp{{ number_format($displayPrice, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="text-[10px] font-bold text-slate-300 dark:text-gray-600 mt-auto ml-2 truncate">
                                    {{ $product->sku }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-24 h-24 bg-slate-50 dark:bg-gray-800 rounded-[3rem] flex items-center justify-center mx-auto mb-6">
                            <span class="text-5xl opacity-40">🔍</span>
                        </div>
                        <p class="text-gray-900 dark:text-white font-black text-lg tracking-tight">Produk tidak tersedia</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1 mx-auto max-w-xs">Gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Cart (Narrower) -->
    @if(count($cart) > 0)
        <aside 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-full lg:w-[28rem] bg-white dark:bg-gray-800 shadow-[-10px_0_30px_rgba(0,0,0,0.02)] flex flex-col lg:h-screen sticky top-0 border-t lg:border-t-0 lg:border-l border-gray-100 dark:border-gray-700 z-30 transition-all flex-shrink-0 lg:overflow-hidden">
            {{-- Order Header & Customer Selection --}}
            <div class="p-6 border-b border-gray-50 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-3 tracking-tight">
                        <span class="bg-indigo-50 dark:bg-indigo-900/40 p-2 rounded-xl text-indigo-600 dark:text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </span>
                        Pesanan
                    </h2>
                    <button wire:click="resetOrder" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-600 transition-colors">Hapus Semua</button>
                </div>

                {{-- Customer Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="text" 
                                wire:model.live.debounce.300ms="customer_search"
                                @focus="open = true"
                                placeholder="Cari Pelanggan..." 
                                class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                        </div>
                        @if($customer_id)
                            <button wire:click="$set('customer_id', '')" class="p-2.5 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition-colors tooltip" title="Lepas Pelanggan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>

                    {{-- Customer Results Dropdown --}}
                    <div x-show="open && $wire.customer_search.length > 0" 
                        @click.away="open = false"
                        class="absolute z-50 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-2 max-h-60 overflow-y-auto custom-scrollbar">
                        @forelse($customers as $customer)
                            <button type="button" 
                                wire:click="$set('customer_id', '{{ $customer->id }}'); $set('customer_search', ''); open = false"
                                class="w-full px-4 py-3 flex items-center justify-between hover:bg-indigo-50 dark:hover:bg-indigo-900/40 transition-colors text-left group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-gray-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $customer->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium">{{ $customer->phone ?? 'No Phone' }}</p>
                                    </div>
                                </div>
                                @if($customer->loyaltyAccount)
                                    <div class="text-right">
                                        <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-2 py-0.5 rounded-full uppercase tracking-widest">{{ $customer->loyaltyAccount->membershipTier->name ?? 'Member' }}</span>
                                    </div>
                                @endif
                            </button>
                        @empty
                            <div class="px-4 py-3 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">Tidak ditemukan</div>
                        @endforelse
                    </div>

                    {{-- Selected Customer Info --}}
                    @if($customer_id)
                        @php $selectedCustomer = App\Models\Customer::find($customer_id); @endphp
                        @if($selectedCustomer)
                            <div class="mt-4 p-3 bg-indigo-50 dark:bg-indigo-500/5 rounded-2xl border-2 border-indigo-100/50 dark:border-indigo-500/20 animate-fade-in-down">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm font-black text-xs ring-1 ring-indigo-100 dark:ring-indigo-900">
                                            {{ substr($selectedCustomer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-indigo-900 dark:text-indigo-100 leading-tight">{{ $selectedCustomer->name }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest">{{ $selectedCustomer->loyaltyAccount->membershipTier->name ?? 'Bronze' }}</span>
                                                <span class="w-0.5 h-0.5 rounded-full bg-indigo-200"></span>
                                                <span class="text-[8px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">{{ number_format($selectedCustomer->points_balance, 0) }} pts</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button wire:click="$set('customer_id', '')" class="text-indigo-300 hover:text-rose-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 custom-scrollbar max-h-[65vh] lg:max-h-none">
                <div class="space-y-6">
                    @foreach($cart as $item)
                        <div class="flex gap-4 group animate-fade-in-down">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex-shrink-0 overflow-hidden border border-gray-100 dark:border-gray-600">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl opacity-40">📦</div>
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-xs sm:text-sm font-extrabold text-gray-900 dark:text-white leading-tight line-clamp-2 pr-4">{{ $item['name'] }}</h4>
                                        <button wire:click="removeFromCart('{{ $item['id'] }}')" class="text-gray-300 hover:text-rose-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="text-xs sm:text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-1.5">Rp{{ number_format($item['price'], 0, ',', '.') }}</div>
                                </div>
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-xl p-0.5 sm:p-1 border border-gray-100 dark:border-gray-700">
                                        <button wire:click="decrementQty('{{ $item['id'] }}')" class="w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-all font-bold group-active:scale-90">-</button>
                                        <span class="w-8 text-center text-xs font-black text-gray-900 dark:text-white">{{ $item['qty'] }}</span>
                                        <button wire:click="incrementQty('{{ $item['id'] }}')" class="w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-all font-bold group-active:scale-90">+</button>
                                    </div>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 dark:text-white">Rp{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                <div class="space-y-3 mb-6">
                    @if (session()->has('success'))
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-xl border border-emerald-100 dark:border-emerald-800 flex items-center gap-2 mb-4 animate-fade-in-down">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="p-3 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-xl border border-rose-100 dark:border-rose-800 flex items-center gap-2 mb-4 animate-fade-in-down">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Voucher Section --}}
                    <div class="py-3 border-t border-gray-50 dark:border-gray-700/50">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <input type="text" 
                                    wire:model="voucher_code"
                                    placeholder="Kode Voucher..." 
                                    class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white uppercase placeholder:normal-case">
                            </div>
                            <button wire:click="applyVoucher" class="px-4 py-2 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-colors">
                                Cek
                            </button>
                        </div>
                        @if($applied_voucher)
                            <div class="mt-2 flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1.5 rounded-xl border border-emerald-100 dark:border-emerald-900/50 animate-fade-in-down">
                                <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">{{ $applied_voucher->code }}</span>
                                <button wire:click="$set('applied_voucher', null); $set('discount', 0); $set('voucher_code', '')" class="text-emerald-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Points Redemption --}}
                    @if($customer_id)
                        <div class="py-3 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </span>
                                    <input type="number" 
                                        wire:model.live="points_to_redeem"
                                        placeholder="Poin" 
                                        class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-[10px] font-black focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                                </div>
                                <button wire:click="redeemPoints" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all active:scale-95">
                                    Pakai
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-[10px]">Subtotal</span>
                        <span class="text-gray-900 dark:text-white font-extrabold tracking-tight">Rp{{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-[10px]">Pajak (11%)</span>
                        <span class="text-gray-900 dark:text-white font-extrabold tracking-tight">Rp{{ number_format($this->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($discount > 0)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-rose-500 font-bold uppercase tracking-widest text-[10px]">Diskon</span>
                            <span class="text-rose-500 font-extrabold tracking-tight text-right">- Rp{{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="pt-4 mt-4 border-t-2 border-dashed border-gray-200 dark:border-gray-700 flex justify-between items-end">
                        <span class="text-gray-900 dark:text-white font-black text-sm uppercase tracking-widest">Total</span>
                        <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter">Rp{{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button wire:click="checkout" wire:loading.attr="disabled"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-300 disabled:cursor-not-allowed text-white font-black text-base rounded-[2rem] py-4 transition-all shadow-2xl shadow-indigo-500/40 active:scale-[0.98] focus:outline-none flex items-center justify-center gap-3">
                    <span wire:loading.remove>Bayar Sekarang</span>
                    <span wire:loading>Memproses...</span>
                    <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </aside>
    @endif

    {{-- Modals --}}
    <livewire:p-o-s.cash-register-dialog />
    @livewire('p-o-s.receipt-modal')

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.4); }
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-5px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fade-in-down 0.3s ease-out; }
    </style>
</div>
