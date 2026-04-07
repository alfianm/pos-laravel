<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Pergerakan Stok</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Riwayat masuk dan keluar stok per produk dan cabang.</p>
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Cabang</label>
                    <select wire:model.live="selected_branch" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe</label>
                    <select wire:model.live="selected_type" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Tipe</option>
                        <option value="opening">Stok Awal</option>
                        <option value="purchase">Pembelian</option>
                        <option value="sale">Penjualan</option>
                        <option value="adjustment">Penyesuaian</option>
                        <option value="transfer_in">Transfer Masuk</option>
                        <option value="transfer_out">Transfer Keluar</option>
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
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
            @foreach($this->movementTypes as $type => $label)
            @php $summary = $this->movementSummary->get($type); @endphp
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 p-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $label }}</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($summary->total_movements ?? 0) }}</p>
                <p class="text-xs text-gray-500">
                    @if($type === 'sale' || $type === 'transfer_out')
                        <span class="text-rose-500">-{{ number_format(abs($summary->total_qty ?? 0)) }} unit</span>
                    @else
                        <span class="text-emerald-500">+{{ number_format($summary->total_qty ?? 0) }} unit</span>
                    @endif
                </p>
            </div>
            @endforeach
        </div>

        {{-- Movements Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Pergerakan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Referensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->movements as $movement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $movement->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $movement->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $movement->product->name ?? '-' }}</div>
                                @if($movement->variant)
                                    <div class="text-xs text-gray-400">{{ $movement->variant->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-300">{{ $movement->branch->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeColors = [
                                        'opening' => 'bg-blue-100 text-blue-700',
                                        'purchase' => 'bg-emerald-100 text-emerald-700',
                                        'sale' => 'bg-rose-100 text-rose-700',
                                        'adjustment' => 'bg-amber-100 text-amber-700',
                                        'transfer_in' => 'bg-cyan-100 text-cyan-700',
                                        'transfer_out' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $typeLabels = [
                                        'opening' => 'Stok Awal',
                                        'purchase' => 'Pembelian',
                                        'sale' => 'Penjualan',
                                        'adjustment' => 'Penyesuaian',
                                        'transfer_in' => 'Transfer In',
                                        'transfer_out' => 'Transfer Out',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $typeLabels[$movement->movement_type] ?? $movement->movement_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($movement->qty > 0)
                                    <span class="font-bold text-emerald-600">+{{ $movement->qty }}</span>
                                @else
                                    <span class="font-bold text-rose-600">{{ $movement->qty }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $movement->reference_no ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $movement->performedBy->name ?? '-' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                    <p class="font-bold">Tidak ada pergerakan stok</p>
                                    <p class="text-sm">Coba ubah filter</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->movements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $this->movements->links() }}
            </div>
            @endif
        </div>
    </div>
</div>