<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Pembelian (PO)</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola pesanan pembelian barang ke supplier.</p>
            </div>
            <a href="{{ route('purchasing.create') }}" wire:navigate class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Buat Pesanan Baru
            </a>
        </div>

        {{-- Filter --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700/50 p-6 mb-8 mx-4 sm:mx-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. PO..." class="block w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                </div>
                <div>
                    <select wire:model.live="status_filter" class="block w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="ordered">Ordered</option>
                        <option value="partial">Partial</option>
                        <option value="received">Received</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">No. PO & Tanggal</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Supplier & Cabang</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Grand Total</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Status</th>
                            <th class="px-8 py-5 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($purchaseOrders as $po)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all">
                            <td class="px-8 py-6">
                                <div class="text-base font-black text-gray-900 dark:text-white tracking-tight">{{ $po->po_no }}</div>
                                <div class="text-xs text-gray-400 mt-1 font-medium">{{ $po->order_date->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-800 dark:text-slate-200">{{ $po->supplier->name }}</div>
                                <div class="text-xs text-gray-400 mt-1 font-medium">{{ $po->branch->name }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-base font-black text-slate-900 dark:text-white tracking-tight">Rp{{ number_format($po->grand_total, 0, ',', '.') }}</div>
                                <div class="text-xs {{ $po->payment_status === 'paid' ? 'text-emerald-500' : 'text-rose-500' }} font-bold mt-1 uppercase tracking-widest">{{ $po->payment_status }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-widest
                                    {{ $po->status === 'received' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 
                                       ($po->status === 'pending' ? 'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-400' : 
                                       'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400') }}">
                                    {{ $po->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('purchasing.show', $po->id) }}" wire:navigate class="p-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/50 rounded-xl transition-all" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-10 rounded-[3rem] mb-6 shadow-inner">
                                        <svg class="w-20 h-20 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-extrabold text-xl tracking-tight">Belum Ada Pesanan Pembelian.</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1 max-w-xs mx-auto font-medium">Buat PO pertama Anda untuk mengisi stok inventori.</p>
                                    <a href="{{ route('purchasing.create') }}" wire:navigate class="mt-8 inline-flex items-center px-8 py-3 bg-white dark:bg-gray-800 border-2 border-indigo-600 dark:border-indigo-500/50 rounded-2xl font-black text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all shadow-xl shadow-indigo-500/10">
                                        Buat PO Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchaseOrders->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $purchaseOrders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
