<div class="py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Customer 360° View</h2>
                <p class="mt-1 text-sm text-gray-500">Profil lengkap dan analisis perilaku pelanggan</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customers.list') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                    Kembali
                </a>
                <a href="{{ route('customers.edit', $customer) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Edit Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Profile Card -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-2xl font-bold text-indigo-600">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $customer->email ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $customer->phone ?? '-' }}</p>
                        <div class="mt-2 flex items-center space-x-2">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $segmentInfo['color'] }}-100 text-{{ $segmentInfo['color'] }}-800">
                                {{ $rfmData['segment'] ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Customer ID</p>
                    <p class="font-mono text-lg">{{ $customer->code ?? $customer->id }}</p>
                </div>
            </div>

            @if(isset($segmentInfo['description']))
                <div class="mt-4 p-3 bg-{{ $segmentInfo['color'] }}-50 rounded-md">
                    <p class="text-sm text-{{ $segmentInfo['color'] }}-700">
                        <span class="font-semibold">{{ $segmentInfo['description'] }}</span>
                        @if(isset($segmentInfo['action']))
                            <br><span class="text-xs">Rekomendasi: {{ $segmentInfo['action'] }}</span>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- RFM Scores -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Recency -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase">Recency</h4>
                    <span
                        class="text-2xl font-bold text-{{ $rfmData['r_score'] >= 4 ? 'green' : ($rfmData['r_score'] >= 3 ? 'yellow' : 'red') }}-600">
                        {{ $rfmData['r_score'] ?? 0 }}/5
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $rfmData['recency_days'] ?? 0 }}</p>
                <p class="text-sm text-gray-500">hari sejak pembelian terakhir</p>
                <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $rfmData['r_score'] >= 4 ? 'green' : ($rfmData['r_score'] >= 3 ? 'yellow' : 'red') }}-500"
                        style="width: {{ ($rfmData['r_score'] ?? 0) * 20 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Frequency -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase">Frequency</h4>
                    <span
                        class="text-2xl font-bold text-{{ $rfmData['f_score'] >= 4 ? 'green' : ($rfmData['f_score'] >= 3 ? 'yellow' : 'red') }}-600">
                        {{ $rfmData['f_score'] ?? 0 }}/5
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $rfmData['frequency'] ?? 0 }}</p>
                <p class="text-sm text-gray-500">total pesanan dalam 1 tahun</p>
                <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $rfmData['f_score'] >= 4 ? 'green' : ($rfmData['f_score'] >= 3 ? 'yellow' : 'red') }}-500"
                        style="width: {{ ($rfmData['f_score'] ?? 0) * 20 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Monetary -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase">Monetary</h4>
                    <span
                        class="text-2xl font-bold text-{{ $rfmData['m_score'] >= 4 ? 'green' : ($rfmData['m_score'] >= 3 ? 'yellow' : 'red') }}-600">
                        {{ $rfmData['m_score'] ?? 0 }}/5
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">Rp
                    {{ number_format($rfmData['monetary'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500">total nilai pembelian</p>
                <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $rfmData['m_score'] >= 4 ? 'green' : ($rfmData['m_score'] >= 3 ? 'yellow' : 'red') }}-500"
                        style="width: {{ ($rfmData['m_score'] ?? 0) * 20 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Stats & Loyalty -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Purchase Statistics -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistik Pembelian</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Total Pesanan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $purchaseStats['total_orders'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Avg Order Value</p>
                        <p class="text-xl font-bold text-gray-900">Rp
                            {{ number_format($purchaseStats['avg_order_value'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Pembelian Pertama</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{ $purchaseStats['first_purchase'] ? $purchaseStats['first_purchase']->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Pembelian Terakhir</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{ $purchaseStats['last_purchase'] ? $purchaseStats['last_purchase']->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>

                @if(!empty($purchaseStats['favorite_products']))
                    <div class="mt-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Produk Favorit</h5>
                        <div class="space-y-2">
                            @foreach($purchaseStats['favorite_products'] as $product)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm text-gray-900">{{ $product['product']->name ?? 'Unknown' }}</span>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">{{ $product['quantity'] }}x</span>
                                        <span class="text-xs font-semibold text-gray-700">Rp
                                            {{ number_format($product['revenue'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Loyalty Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Program Loyalty</h4>
                @if($loyaltyData['has_account'])
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Tier Status</p>
                            <span class="px-2 py-1 text-sm font-semibold rounded bg-indigo-100 text-indigo-800">
                                {{ $loyaltyData['tier'] }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Points Balance</p>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ number_format($loyaltyData['points_balance'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-xs text-green-600">Total Earned</p>
                            <p class="text-lg font-bold text-green-800">
                                +{{ number_format($loyaltyData['lifetime_earned'], 0, ',', '.') }}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg">
                            <p class="text-xs text-red-600">Total Redeemed</p>
                            <p class="text-lg font-bold text-red-800">
                                -{{ number_format($loyaltyData['lifetime_redeemed'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @if(!empty($loyaltyData['transactions']))
                        <div>
                            <h5 class="text-sm font-semibold text-gray-700 mb-2">Transaksi Terakhir</h5>
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($loyaltyData['transactions'] as $transaction)
                                    <div
                                        class="flex items-center justify-between text-sm p-2 {{ $transaction->type === 'earned' ? 'bg-green-50' : 'bg-red-50' }} rounded">
                                        <span class="text-gray-600">{{ $transaction->created_at->format('d M') }}</span>
                                        <span
                                            class="{{ $transaction->type === 'earned' ? 'text-green-700' : 'text-red-700' }} font-semibold">
                                            {{ $transaction->type === 'earned' ? '+' : '' }}{{ number_format($transaction->points, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Pelanggan belum terdaftar di program loyalty</p>
                        <button wire:click="enrollLoyalty"
                            class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Daftarkan Sekarang
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Customer Timeline</h4>
            @if($timeline->isEmpty())
                <p class="text-gray-500 text-center py-8">Belum ada aktivitas</p>
            @else
                <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                    @foreach($timeline as $event)
                                <div class="ml-6 relative">
                                    <span class="absolute -left-9 top-1 flex h-4 w-4 items-center justify-center rounded-full
                                                {{ match ($event['type']) {
                            'sale' => 'bg-green-500',
                            'return' => 'bg-red-500',
                            'payment' => 'bg-blue-500',
                            'loyalty' => 'bg-purple-500',
                            default => 'bg-gray-500'
                        } }}">
                                    </span>
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $event['title'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $event['description'] }}</p>
                                            @if(!empty($event['metadata']))
                                                <div class="mt-1 text-xs text-gray-400">
                                                    @foreach($event['metadata'] as $key => $value)
                                                        <span class="mr-2">{{ $key }}: {{ $value }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $event['date']->diffForHumans() }}</span>
                                    </div>
                                </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>