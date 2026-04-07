<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Accounts Receivable</h1>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-500 dark:text-gray-400">Current</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['current']['amount'], 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $summary['current']['count'] }} invoices</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-yellow-400">
            <div class="text-sm text-gray-500 dark:text-gray-400">1-30 Days</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['1-30']['amount'], 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $summary['1-30']['count'] }} invoices</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-orange-400">
            <div class="text-sm text-gray-500 dark:text-gray-400">31-60 Days</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['31-60']['amount'], 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $summary['31-60']['count'] }} invoices</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-orange-600">
            <div class="text-sm text-gray-500 dark:text-gray-400">61-90 Days</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['61-90']['amount'], 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $summary['61-90']['count'] }} invoices</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="text-sm text-gray-500 dark:text-gray-400">90+ Days</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['90+']['amount'], 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $summary['90+']['count'] }} invoices</div>
        </div>
    </div>

    {{-- Total Outstanding --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg shadow p-4 mb-6 border border-blue-200 dark:border-blue-800">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Outstanding</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['total']['amount'], 2) }}</div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Invoices</div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['total']['count'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search reference, customer..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                <select wire:model.live="branchId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Branches</option>
                    @foreach($branches as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model.live="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aging</label>
                <select wire:model.live="agingBucket"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All</option>
                    @foreach($agingBuckets as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- AR Records Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trans. Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paid</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Overdue</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $record->reference_number }}</a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $record->entity?->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $record->transaction_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $record->due_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">
                                {{ number_format($record->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                                {{ number_format($record->paid_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">
                                {{ number_format($record->balance_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @php
                                    $statusColors = [
                                        'outstanding' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'partial' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statuses[$record->status] ?? $record->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                @if($record->days_overdue > 0)
                                    <span class="text-red-600 dark:text-red-400 font-medium">{{ $record->days_overdue }} days</span>
                                @else
                                    <span class="text-green-600 dark:text-green-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No accounts receivable records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            {{ $records->links() }}
        </div>
    </div>
</div>