<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Marketplace Sync Report</h1>
        <p class="text-gray-500 text-sm mt-1">Track marketplace synchronization status and performance</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">From</label>
                <input type="date" wire:model.live="date_from" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">To</label>
                <input type="date" wire:model.live="date_to" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Branch</label>
                <select wire:model.live="selected_branch" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Marketplace</label>
                <select wire:model.live="selected_marketplace" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Marketplaces</option>
                    @foreach($marketplaceOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <select wire:model.live="selected_status" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Statuses</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Type</label>
                <select wire:model.live="selected_sync_type" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Types</option>
                    @foreach($syncTypeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Syncs</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($grandTotals->total_syncs) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Success</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($grandTotals->success_count) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Failed</div>
            <div class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($grandTotals->failed_count) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Success Rate</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">
                {{ $grandTotals->total_syncs > 0 ? round(($grandTotals->success_count / $grandTotals->total_syncs) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Marketplace Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Sync by Marketplace</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-6 py-3">Marketplace</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Success</th>
                        <th class="px-6 py-3 text-right">Failed</th>
                        <th class="px-6 py-3 text-right">Success Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($marketplaceSummary as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getMarketplaceColor($item->marketplace) }}">
                                    {{ $marketplaceOptions[$item->marketplace] ?? ucfirst($item->marketplace) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->total) }}</td>
                            <td class="px-6 py-4 text-sm text-emerald-600 text-right font-medium">{{ number_format($item->success_count) }}</td>
                            <td class="px-6 py-4 text-sm text-rose-600 text-right">{{ number_format($item->failed_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold">{{ $item->success_rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No marketplace data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sync Type Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Sync by Type</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Success</th>
                        <th class="px-6 py-3 text-right">Success Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($syncTypeSummary as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $syncTypeOptions[$item->sync_type] ?? ucfirst($item->sync_type) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->total) }}</td>
                            <td class="px-6 py-4 text-sm text-emerald-600 text-right font-medium">{{ number_format($item->success_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold">{{ $item->success_rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No sync type data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Sync Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Recent Sync Logs</h2>
            <button wire:click="exportCsv" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                Export CSV
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-6 py-3">Date/Time</th>
                        <th class="px-6 py-3">Marketplace</th>
                        <th class="px-6 py-3">Shop</th>
                        <th class="px-6 py-3">Branch</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getMarketplaceColor($log->marketplace) }}">
                                    {{ $marketplaceOptions[$log->marketplace] ?? ucfirst($log->marketplace) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $log->shop->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $log->branch->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $syncTypeOptions[$log->sync_type] ?? ucfirst($log->sync_type) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusColor($log->status) }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $log->error_message ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No sync logs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $recentLogs->links() }}
        </div>
    </div>
</div>