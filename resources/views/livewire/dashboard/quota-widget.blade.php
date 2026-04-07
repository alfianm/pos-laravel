<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Penggunaan Kuota</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor penggunaan limit berdasarkan paket langganan</p>
        </div>
        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
    </div>

    @if(count($alerts) > 0)
        <div class="mb-4 space-y-2">
            @foreach($alerts as $alert)
                <div class="p-3 rounded-xl {{ $alert['type'] === 'error' ? 'bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800' : 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $alert['type'] === 'error' ? 'text-red-500' : 'text-yellow-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="text-sm font-medium {{ $alert['type'] === 'error' ? 'text-red-700 dark:text-red-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                            {{ $alert['message'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="space-y-4">
        @foreach($quotas as $type => $quota)
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                            {{ match($type) {
                                'branches' => 'Cabang',
                                'products' => 'Produk',
                                'users' => 'Pengguna',
                                'transactions' => 'Transaksi/Bulan',
                                'storage' => 'Storage (MB)',
                                default => $type
                            } }}
                        </span>
                        @if($quota['is_unlimited'])
                            <span class="px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">Unlimited</span>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($quota['is_unlimited'])
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($quota['used']) }} / ∞</span>
                        @else
                            <span class="text-sm font-bold {{ $quota['is_exceeded'] ? 'text-red-600' : ($quota['is_approaching_limit'] ? 'text-yellow-600' : 'text-gray-900 dark:text-white') }}">
                                {{ number_format($quota['used']) }} / {{ number_format($quota['limit']) }}
                            </span>
                        @endif
                    </div>
                </div>

                @if(!$quota['is_unlimited'])
                    @php
                        $percentage = min($quota['percentage'], 100);
                        $barClass = $quota['is_exceeded'] ? 'bg-red-500' : ($quota['percentage'] >= 90 ? 'bg-red-400' : ($quota['percentage'] >= 80 ? 'bg-yellow-400' : 'bg-emerald-500'));
                    @endphp
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $barClass }} rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $quota['percentage'] }}% digunakan</span>
                        @if($quota['is_exceeded'])
                            <span class="text-xs font-medium text-red-600">Kuota habis!</span>
                        @elseif($quota['is_approaching_limit'])
                            <span class="text-xs font-medium text-yellow-600">Hampir habis</span>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($quota['remaining']) }} tersisa</span>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
