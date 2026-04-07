<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Pembelian</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Ringkasan pembelian per supplier dan status.</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <input type="date" wire:model.live="date_from" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                    <input type="date" wire:model.live="date_to" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Supplier</label>
                    <select wire:model.live="selected_supplier" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                    <select wire:model.live="selected_status" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="partial">Partial</option>
                        <option value="received">Received</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="resetFilters" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-sm">
                        Reset
                    </button>
                    <button wire:click="exportCsv" class="px-4 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total PO</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($this->grandTotals->total_orders ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Nilai</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format($this->grandTotals->total_amount ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1H9m3 1h3"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sudah Dibayar</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format($this->grandTotals->total_paid ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jatuh Tempo</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format($this->grandTotals->total_due ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avg Order</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format(($this->grandTotals->total_orders > 0 ? $this->grandTotals->total_amount / $this->grandTotals->total_orders : 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Status Pembelian</h3>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                @foreach(['draft', 'submitted', 'partial', 'received', 'completed', 'cancelled'] as $status)
                @php $summary = $this->statusSummary->get($status); @endphp
                <div class="text-center">
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $this->getStatusColor($status) }}">
                        {{ $this->getStatusLabel($status) }}
                    </span>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($summary->count ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Rp {{ number_format($summary->total ?? 0, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Supplier Summary Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Ringkasan per Supplier</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Total PO</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Total Nilai</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Dibayar</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->supplierSummary as $summary)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-all">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $summary->supplier->name ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $summary->supplier->code ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">{{ number_format($summary->total_orders) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">Rp {{ number_format($summary->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-blue-600">Rp {{ number_format($summary->total_paid, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-amber-600">Rp {{ number_format($summary->total_due, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $summary->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($summary->payment_status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $summary->payment_status === 'paid' ? 'Lunas' : ($summary->payment_status === 'partial' ? 'Sebagian' : 'Belum Bayar') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <p class="font-bold">Tidak ada data pembelian</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pesanan Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">No. PO</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->recentOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-all">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-bold text-gray-900 dark:text-white">{{ $order->po_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $order->supplier->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-300">{{ $order->branch->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $this->getStatusColor($order->status) }}">
                                    {{ $this->getStatusLabel($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-300">{{ $order->order_date->format('d M Y') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <p class="font-bold">Tidak ada pesanan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->recentOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $this->recentOrders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>