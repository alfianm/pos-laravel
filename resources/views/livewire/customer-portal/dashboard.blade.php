<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Welcome Header --}}
        <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Selamat Datang, {{ $customer->name }}!</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium italic mt-1">Status: <span class="text-emerald-500 font-black uppercase tracking-widest text-xs px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg ml-1">Akun Terverifikasi</span></p>
            </div>
            <div class="p-4 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg> 
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block leading-none mb-1">Loyalty Points</label>
                    <span class="text-lg font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($customer->loyalty_points ?? 0) }} Pts</span>
                </div>
            </div>
        </div>

        {{-- Statistics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-600/30 group">
                <label class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-4 block leading-none">Total Belanja</label>
                <div class="text-4xl font-black tracking-tight mb-2 group-hover:scale-105 transition-transform duration-500 origin-left">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</div>
                <p class="text-indigo-200 text-xs font-medium opacity-70 italic tracking-wide">Semua transaksi sukses.</p>
            </div>
            
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl border border-slate-100 dark:border-slate-700/50 flex items-center justify-between group">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block leading-none">Frekuensi Order</label>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $stats['total_orders'] }} Kali</div>
                </div>
                <div class="p-5 bg-slate-50 dark:bg-slate-900/50 rounded-3xl text-slate-400 group-hover:text-indigo-600 transition-colors duration-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 118 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl border border-slate-100 dark:border-slate-700/50 flex items-center justify-between group">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block leading-none">Terakhir Belanja</label>
                    <div class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $stats['last_purchase']?->created_at->format('d M Y') ?: 'Belum ada' }}</div>
                </div>
                <div class="p-5 bg-slate-50 dark:bg-slate-900/50 rounded-3xl text-slate-400 group-hover:text-emerald-500 transition-colors duration-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> 
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white dark:bg-slate-800 shadow-2xl rounded-[3rem] border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 flex justify-between items-center">
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Transaksi Terakhir</h3>
                <a href="{{ route('customer.orders') }}" wire:navigate class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] border-b-4 border-indigo-600/10 hover:border-indigo-600 transition-all pb-1">Lihat Semua</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/30">
                             <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Invoice No.</th>
                             <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono text-center">Tanggal</th>
                             <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Status</th>
                             <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono text-right">Total Transaksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($recentOrders as $order)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all">
                             <td class="px-10 py-7">
                                 <div class="text-sm font-black text-slate-900 dark:text-white tracking-tight">{{ $order->invoice_no }}</div>
                                 <div class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Lunas / Terbayar</div>
                             </td>
                             <td class="px-10 py-7 text-center">
                                 <div class="text-xs font-bold text-slate-700 dark:text-slate-300 italic">{{ $order->created_at->format('d/m/Y, H:i') }}</div>
                             </td>
                             <td class="px-10 py-7">
                                 <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30">
                                     Success
                                 </span>
                             </td>
                             <td class="px-10 py-7 text-right">
                                 <div class="text-base font-black text-slate-900 dark:text-white tracking-tight">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                             </td>
                        </tr>
                        @empty
                        <tr>
                             <td colspan="4" class="px-10 py-20 text-center">
                                 <p class="text-slate-400 font-medium italic">Belum ada transaksi tercatat.</p>
                             </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
