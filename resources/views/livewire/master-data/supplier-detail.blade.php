<div>
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('master-data.suppliers') }}" wire:navigate class="p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none">{{ $supplier->name }}</h2>
                    <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-black tracking-widest uppercase border border-indigo-100 dark:border-indigo-800">{{ $supplier->code }}</span>
                </div>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm font-medium">Informasi mendalam dan riwayat transaksi pemasok.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <span class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest flex items-center gap-2 {{ $supplier->status === 'active' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 dark:bg-slate-700/50 dark:text-slate-400 border border-slate-200 dark:border-slate-600' }}">
                <div class="w-2 h-2 rounded-full {{ $supplier->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></div>
                {{ $supplier->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- Sidebar Info --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200 dark:border-slate-700/50 p-10 shadow-xl shadow-slate-200/40 dark:shadow-none relative overflow-hidden group transition-all duration-500 hover:shadow-2xl">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-600/5 dark:bg-indigo-600/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="relative flex flex-col items-center mb-10 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[2rem] flex items-center justify-center text-white text-4xl font-black shadow-lg shadow-indigo-500/30 mb-6 group-hover:scale-110 transition-transform duration-500">
                        {{ substr($supplier->name, 0, 1) }}
                    </div>
                    <div class="text-lg font-black text-slate-900 dark:text-white leading-tight uppercase tracking-widest mb-1">{{ $supplier->contact_person ?: 'No PIC Info' }}</div>
                    <div class="text-xs text-slate-400 font-bold tracking-[0.2em] uppercase italic">Contact Person</div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-5 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700 transition-all hover:bg-white dark:hover:bg-slate-800 hover:shadow-md">
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl text-indigo-500 border border-slate-100 dark:border-slate-700 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 whitespace-nowrap">Telepon / HP</p>
                            <p class="text-sm font-black text-slate-700 dark:text-white">{{ $supplier->phone ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700 transition-all hover:bg-white dark:hover:bg-slate-800 hover:shadow-md">
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl text-indigo-500 border border-slate-100 dark:border-slate-700 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 whitespace-nowrap">Email Adress</p>
                            <p class="text-sm font-black text-slate-700 dark:text-white truncate">{{ $supplier->email ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700 transition-all hover:bg-white dark:hover:bg-slate-800 hover:shadow-md">
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl text-indigo-500 border border-slate-100 dark:border-slate-700 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 whitespace-nowrap">Kota & Wilayah</p>
                            <p class="text-sm font-black text-slate-700 dark:text-white uppercase tracking-tight">{{ $supplier->city ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="p-6 bg-indigo-50/50 dark:bg-indigo-500/5 rounded-[2rem] border border-indigo-100/50 dark:border-indigo-500/10 shadow-inner">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 whitespace-nowrap">Alamat Lengkap</p>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300 leading-relaxed italic">{{ $supplier->address ?: 'Belum ada alamat terdaftar.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Tabs --}}
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white dark:bg-slate-800/90 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 overflow-hidden shadow-xl shadow-slate-200/40 dark:shadow-none min-h-[600px] flex flex-col">
                <div class="flex p-2 bg-slate-50 dark:bg-slate-900 mx-8 mt-8 rounded-2xl border border-slate-100 dark:border-slate-700">
                    <button wire:click="setTab('info')" class="flex-1 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'info' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-lg shadow-indigo-500/10' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">Overview</button>
                    <button wire:click="setTab('purchases')" class="flex-1 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'purchases' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-lg shadow-indigo-500/10' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">Riwayat Pembelian</button>
                    <button wire:click="setTab('ledgers')" class="flex-1 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'ledgers' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-lg shadow-indigo-500/10' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">Buku Besar</button>
                </div>

                <div class="p-8 flex-1">
                    @if($activeTab === 'info')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in">
                            <div class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-700 flex flex-col items-center justify-center text-center shadow-inner">
                                <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-indigo-500 shadow-sm border border-slate-100 dark:border-slate-700 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-1 leading-none italic">Total Pembelian</h4>
                                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">Rp {{ number_format($supplier->purchase_orders()->sum('grand_total'), 0, ',', '.') }}</p>
                            </div>
                            <div class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-700 flex flex-col items-center justify-center text-center shadow-inner">
                                <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-rose-500 shadow-sm border border-slate-100 dark:border-slate-700 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-1 leading-none italic">Hutang Outstanding</h4>
                                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">Rp {{ number_format($supplier->purchase_orders()->sum('due_amount'), 0, ',', '.') }}</p>
                            </div>
                            <div class="md:col-span-2 p-10 bg-indigo-600 rounded-[2.5rem] text-white relative overflow-hidden shadow-2xl">
                                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                                <div class="flex flex-col md:flex-row justify-between items-center gap-6 relative">
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-[0.3em] mb-3 opacity-60 italic leading-none">Status Supplier</h4>
                                        <p class="text-xl font-bold tracking-tight leading-relaxed max-w-sm">Pemasok ini terpantau <span class="px-2 py-0.5 bg-white text-indigo-600 rounded-md font-black italic">Sangat Sehat</span> dan kooperaktif dalam pengiriman barang.</p>
                                    </div>
                                    <button class="px-8 py-4 bg-white/20 backdrop-blur-md rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-white/30 transition-all border border-white/20 border-b-4 active:border-b-0 active:translate-y-1">Analisis Insight</button>
                                </div>
                            </div>
                        </div>
                    @elseif($activeTab === 'purchases')
                        <div class="animate-fade-in flex-1 flex flex-col">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 italic">No. Order</th>
                                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 italic">Tanggal</th>
                                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 italic text-right">Total</th>
                                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 italic text-right">Hutang</th>
                                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 italic text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                        @forelse($purchaseOrders as $order)
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/40 transition-all">
                                                <td class="px-6 py-5">
                                                    <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">{{ $order->purchase_number }}</span>
                                                </td>
                                                <td class="px-6 py-5 whitespace-nowrap">
                                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->order_date->format('d M Y') }}</span>
                                                </td>
                                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                                    <span class="text-sm font-black text-slate-900 dark:text-white leading-none tracking-tight">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                                </td>
                                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                                    <span class="text-sm font-black text-rose-500 leading-none tracking-tight">Rp {{ number_format($order->due_amount, 0, ',', '.') }}</span>
                                                </td>
                                                <td class="px-6 py-5 text-center">
                                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border {{ $order->status === 'paid' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">{{ $order->status }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-20 text-center">
                                                    <div class="flex flex-col items-center gap-2">
                                                        <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-full text-slate-300 shadow-inner">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                        </div>
                                                        <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase italic leading-none mt-2">Belum ada riwayat pembelian</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($purchaseOrders && $purchaseOrders->hasPages())
                                <div class="mt-auto px-6 py-6 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-50 dark:border-slate-700">
                                    {{ $purchaseOrders->links() }}
                                </div>
                            @endif
                        </div>
                    @elseif($activeTab === 'ledgers')
                        <div class="flex flex-col items-center justify-center p-20 text-center animate-fade-in h-64">
                            <div class="relative mb-6">
                                <div class="absolute -inset-4 bg-indigo-500/10 rounded-full blur-xl animate-pulse"></div>
                                <svg class="w-16 h-16 text-indigo-500 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-2 leading-none uppercase italic">Coming Soon</h4>
                            <p class="text-sm font-medium text-slate-400 max-w-xs leading-relaxed italic">Modul Buku Besar sedang dalam tahap finalisasi audit.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
