<div
    x-data="{ ready: false }"
    x-init="setTimeout(() => ready = true, 80)"
    :class="{ 'dashboard-ready': ready }"
    class="space-y-6 lg:space-y-8"
>
    <!-- Simple Modern Dashboard Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Sales Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1H9m3 1h3"></path></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Today Sales</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
            <p class="text-sm text-gray-500 mt-1">Total completed transactions today</p>
        </div>

        <!-- Transactions Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Transactions</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($todayTransactions) }}</div>
            <p class="text-sm text-gray-500 mt-1">Order volume processed today</p>
        </div>

        <!-- Average Sale Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Avg Transaction</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($avgSale, 0, ',', '.') }}</div>
            <p class="text-sm text-gray-500 mt-1">Average spending per customer</p>
        </div>

        <!-- API Rate Limit Card -->
        @if($rateLimitMax > 0 && !empty($quotaSummary))
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div @class([
                    'p-2 rounded-lg',
                    'bg-rose-50 text-rose-600' => $rateLimitRemaining < 10,
                    'bg-indigo-50 text-indigo-600' => $rateLimitRemaining >= 10,
                ])>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">API Rate Limit</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $rateLimitRemaining }}</div>
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-1 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div @class([
                        'h-full transition-all duration-500',
                        'bg-rose-500' => $rateLimitRemaining < 10,
                        'bg-indigo-500' => $rateLimitRemaining >= 10,
                    ]) style="width: {{ ($rateLimitRemaining / $rateLimitMax) * 100 }}%"></div>
                </div>
                <span class="text-xs text-gray-500">{{ $rateLimitMax }} max</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Service Quotas (Tenant Only) -->
    @if(!empty($quotaSummary))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach($quotaSummary as $type => $data)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ str_replace('_', ' ', $type) }}</span>
                    @if($data['is_exceeded'])
                        <span class="flex h-2 w-2 rounded-full bg-rose-500"></span>
                    @elseif($data['is_approaching_limit'])
                        <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    @else
                        <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    @endif
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-xl font-black text-gray-900 tracking-tight">
                            {{ $data['used'] }} <span class="text-gray-300 font-normal">/ {{ $data['is_unlimited'] ? '∞' : $data['limit'] }}</span>
                        </div>
                    </div>
                    <div @class([
                        'text-[10px] font-bold uppercase tracking-tighter',
                        'text-rose-600' => $data['is_exceeded'],
                        'text-amber-600' => $data['is_approaching_limit'],
                        'text-emerald-600' => !$data['is_exceeded'] && !$data['is_approaching_limit'],
                    ])>
                        {{ $data['is_unlimited'] ? 'Unlimited' : round($data['percentage']) . '%' }}
                    </div>
                </div>
                <div class="mt-3 w-full bg-gray-50 h-1 rounded-full overflow-hidden">
                    <div @class([
                        'h-full transition-all duration-700',
                        'bg-rose-500' => $data['is_exceeded'],
                        'bg-amber-500' => $data['is_approaching_limit'],
                        'bg-emerald-500' => !$data['is_exceeded'] && !$data['is_approaching_limit'],
                    ]) style="width: {{ min(100, $data['percentage']) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- User Context / Info Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-12">
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Recent Branches</h3>
            <div class="space-y-4">
                @forelse ($recentBranches as $branch)
                    <div class="flex items-center justify-between p-3 border border-gray-50 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                {{ substr($branch->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $branch->name }}</p>
                                <p class="text-xs text-gray-500">{{ $branch->code }} • {{ $branch->tenant->name ?? '' }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase rounded-md">{{ $branch->status }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No branches found.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">User Context</h3>
            <div class="space-y-5">
                @foreach ($accessRows as $row)
                    <div class="flex flex-col pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">{{ $row['label'] }}</p>
                        <p class="text-sm font-bold text-gray-900">{{ $row['value'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $row['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
