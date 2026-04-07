<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">CRM Conversion Report</h1>
        <p class="text-gray-500 text-sm mt-1">Track lead conversion metrics and performance</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
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
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Source</label>
                <select wire:model.live="selected_source" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Sources</option>
                    @foreach($sources as $source)
                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Stage</label>
                <select wire:model.live="selected_stage" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Stages</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
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
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Leads</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($grandTotals->total_leads) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">New</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($grandTotals->new_count) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Converted</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($grandTotals->converted_count) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lost</div>
            <div class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($grandTotals->lost_count) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Conversion Rate</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $grandTotals->conversion_rate }}%</div>
        </div>
    </div>

    <!-- Source Conversion Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Conversion by Source</h2>
            <button wire:click="exportCsv" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                Export CSV
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-6 py-3">Source</th>
                        <th class="px-6 py-3 text-right">Total Leads</th>
                        <th class="px-6 py-3 text-right">Converted</th>
                        <th class="px-6 py-3 text-right">Lost</th>
                        <th class="px-6 py-3 text-right">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sourceConversion as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->source->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->total_leads) }}</td>
                            <td class="px-6 py-4 text-sm text-emerald-600 text-right font-medium">{{ number_format($item->converted_count) }}</td>
                            <td class="px-6 py-4 text-sm text-rose-600 text-right">{{ number_format($item->lost_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold">{{ $item->conversion_rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No lead data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Branch Performance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Branch Performance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-6 py-3">Branch</th>
                        <th class="px-6 py-3 text-right">Total Leads</th>
                        <th class="px-6 py-3 text-right">Converted</th>
                        <th class="px-6 py-3 text-right">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($branchPerformance as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->branch->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->total_leads) }}</td>
                            <td class="px-6 py-4 text-sm text-emerald-600 text-right font-medium">{{ number_format($item->converted_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold">{{ $item->conversion_rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No branch data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Conversions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Recent Conversions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-6 py-3">Lead</th>
                        <th class="px-6 py-3">Source</th>
                        <th class="px-6 py-3">Branch</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Converted At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentConversions as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $lead->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $lead->source->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $lead->branch->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-emerald-600">{{ $lead->convertedCustomer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $lead->converted_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No conversions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $recentConversions->links() }}
        </div>
    </div>
</div>