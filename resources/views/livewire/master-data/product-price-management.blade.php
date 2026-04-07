<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8 px-4 sm:px-0">
            <a href="{{ route('master-data.products.show', $product->id) }}" wire:navigate class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Set Harga Cabang</h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Produk: <span class="text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-widest">{{ $product->name }}</span></p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto rounded-3xl border border-slate-100 dark:border-slate-700">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Nama Cabang</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Harga Retail (Rp)</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Grosir (Rp)</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Member (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($branches as $branch)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="font-black text-slate-900 dark:text-white leading-tight mb-0.5">{{ $branch->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $branch->city }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                                            <input type="number" wire:model.live="prices.{{ $branch->id }}.retail_price" class="block w-full pl-8 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-black focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                                            <input type="number" wire:model.live="prices.{{ $branch->id }}.wholesale_price" class="block w-full pl-8 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-black focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                                            <input type="number" wire:model.live="prices.{{ $branch->id }}.member_price" class="block w-full pl-8 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-black focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('master-data.products.show', $product->id) }}" wire:navigate class="px-8 py-4 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-black rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all uppercase tracking-widest text-xs">Kembali</a>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs">Simpan Perubahan Harga</button>
                </div>
            </div>
        </form>
    </div>
</div>
