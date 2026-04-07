<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Sales Returns</h1>
            <p class="text-slate-500">Kelola pengembalian barang dan dana pelanggan.</p>
        </div>
        <div>
            <a href="{{ route('sales.returns.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Return
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100 items-end">
        <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-500 uppercase">Cari Nomor Return/Invoice</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       class="block w-full pl-10 pr-3 py-2 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out" 
                       placeholder="RET/2026/04/01/00001...">
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-500 uppercase">Status</label>
            <select wire:model.live="status" class="block w-full px-3 py-2 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out">
                <option value="">Semua Status</option>
                @foreach(\App\Constants\ReturnStatus::STATUSES as $s)
                    <option value="{{ $s }}">{{ strtoupper($s) }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-500 uppercase">Dari Tanggal</label>
            <input wire:model.live="date_from" type="date" 
                   class="block w-full px-3 py-2 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out">
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-500 uppercase">Sampai Tanggal</label>
            <input wire:model.live="date_to" type="date" 
                   class="block w-full px-3 py-2 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">No. Return</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl / Invoice</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($returns as $return)
                        <tr class="hover:bg-slate-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900">{{ $return->return_number }}</div>
                                <div class="text-xs text-slate-400">Oleh: {{ $return->user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $return->return_date->format('d M Y') }}</div>
                                <div class="text-xs font-medium text-indigo-600">{{ $return->sale?->sale_no }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $return->customer?->name ?? 'Walk-in Customer' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-900">Rp {{ number_format($return->total_amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusColor = match($return->status) {
                                        'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'cancelled' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full border {{ $statusColor }} uppercase tracking-wider">
                                    {{ $return->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end space-x-2">
                                    <button class="p-1 text-slate-400 hover:text-indigo-600 transition" title="Print Receipt">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 group">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center group-hover:scale-110 transition duration-300">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <p class="text-slate-400">Belum ada data return penjualan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($returns->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
