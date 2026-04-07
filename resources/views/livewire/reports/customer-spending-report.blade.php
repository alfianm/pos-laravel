<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Spending Customer</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Analisis pengeluaran dan perilaku belanja customer.</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <input type="date" wire:model.live="date_from" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                    <input type="date" wire:model.live="date_to" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Customer</label>
                    <select wire:model.live="selected_customer" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Customer</option>
                        @foreach($this->customerList as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="resetFilters" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-sm">
                        Reset
                    </button>
                    <button wire:click="exportCsv" class="px-4 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Transaksi</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($this->allTransactions->total_transactions ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Revenue</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format($this->allTransactions->total_spent ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1H9m3 1h3"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Top Customer</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white mt-1 truncate max-w-[150px]">{{ $this->topCustomers->first()?->customer?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avg Transaction</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format(($this->allTransactions->total_transactions > 0 ? $this->allTransactions->total_spent / $this->allTransactions->total_transactions : 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Top 10 Customer</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Transaksi</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Total Belanja</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->topCustomers as $index => $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-all">
                            <td class="px-6 py-4">
                                @if($index === 0)
                                    <span class="w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold">🥇</span>
                                @elseif($index === 1)
                                    <span class="w-8 h-8 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center font-bold">🥈</span>
                                @elseif($index === 2)
                                    <span class="w-8 h-8 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center font-bold">🥉</span>
                                @else
                                    <span class="w-8 h-8 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center font-bold">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $customer->customer->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-400">{{ $customer->customer->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">{{ number_format($customer->total_transactions) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-600 dark:text-gray-300">Rp {{ number_format($customer->avg_transaction, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <p class="font-bold">Tidak ada data customer</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Purchases --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Transaksi Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">No. Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->recentPurchases as $sale)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-all">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-bold text-gray-900 dark:text-white">{{ $sale->sale_no }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $sale->customer->name ?? 'Walk-in' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-300">{{ $sale->branch->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-300">{{ $sale->sale_date->format('d M Y H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <p class="font-bold">Tidak ada transaksi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->recentPurchases->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $this->recentPurchases->links() }}
            </div>
            @endif
        </div>
    </div>
</div>