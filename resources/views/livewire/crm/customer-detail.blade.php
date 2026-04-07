<div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50/50 min-h-screen">
    {{-- Header Section: Profile & Dashboard --}}
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Profile Card --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 p-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide uppercase bg-emerald-50 text-emerald-600 border border-emerald-100 ring-2 ring-emerald-50/50">
                            {{ $customer->status ?? 'Active' }}
                        </span>
                    </div>

                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 p-1 ring-4 ring-indigo-50 transition-transform duration-500 group-hover:scale-105">
                                <div class="w-full h-full rounded-2xl bg-white flex items-center justify-center overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=6366f1&color=fff&size=200" alt="{{ $customer->name }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-white rounded-xl shadow-lg p-2 border border-gray-100">
                                <span class="bg-indigo-600 text-white rounded-lg p-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </span>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $customer->name }}</h2>
                            <p class="text-gray-500 font-medium">{{ $customer->customer_code ?? 'CUST-'.str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <div class="w-full grid grid-cols-2 gap-4 pt-4">
                            <div class="bg-gray-50 rounded-2xl p-4 transition-colors hover:bg-indigo-50/50">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Spent</p>
                                <p class="text-lg font-bold text-gray-900 line-clamp-1">Rp{{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4 transition-colors hover:bg-emerald-50/50">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Points</p>
                                <p class="text-lg font-bold text-gray-900 line-clamp-1">{{ number_format($loyaltyAccount?->points_balance ?? 0, 0) }}</p>
                            </div>
                        </div>

                        <div class="w-full space-y-3 pt-4 border-t border-gray-100">
                            <div class="flex items-center text-gray-600 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3 transition-colors group-hover:bg-indigo-100">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-sm font-medium">{{ $customer->email ?? 'no-email@example.com' }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3 transition-colors group-hover:bg-indigo-100">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <span class="text-sm font-medium">{{ $customer->phone ?? 'No Phone' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Membership Card --}}
                @if($loyaltyAccount && $loyaltyAccount->membershipTier)
                <div class="relative overflow-hidden rounded-3xl p-8 bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow-xl group">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-8">
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-3 ring-1 ring-white/30">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                            </div>
                            <div class="text-right">
                                <p class="text-indigo-100 text-xs font-bold uppercase tracking-[0.2em]">Membership Tier</p>
                                <h3 class="text-3xl font-black italic tracking-tight">{{ $loyaltyAccount->membershipTier->name }}</h3>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between text-sm font-bold">
                                <span>Multipliers</span>
                                <span class="bg-white/20 px-3 py-1 rounded-lg">{{ $loyaltyAccount->membershipTier->point_multiplier }}x Points</span>
                            </div>
                            <div class="w-full bg-white/20 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full shadow-[0_0_10px_rgba(255,255,255,0.5)]" style="width: 75%"></div>
                            </div>
                            <p class="text-xs text-indigo-100 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Spend Rp2.000.000 more to unlock Platinum
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Center & Right Content (RFM & Tabs) --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- RFM Health & Intelligence --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Health & Behavioral IQ</h3>
                            <p class="text-gray-500 text-sm font-medium">Smart segmentation based on recent, frequent, and monetary metrics.</p>
                        </div>
                        <div class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold border border-indigo-100">
                            {{ $customer->rfm_segment ?? 'New / Uncategorized' }}
                        </div>
                    </div>
                    
                    <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($rfmSummary as $stat)
                        <div class="space-y-4">
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">{{ $stat['name'] }}</span>
                                <span class="text-2xl font-black text-{{ $stat['color'] }}-600">{{ $stat['score'] }}/5</span>
                            </div>
                            <div class="relative h-4 w-full bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                                <div class="absolute inset-y-0 left-0 bg-{{ $stat['color'] }}-500 rounded-full shadow-lg transition-all duration-1000" style="width: {{ ($stat['score'] / 5) * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">
                                @if($stat['name'] == 'Recency')
                                    Last seen {{ $customer->last_purchase_date ? $customer->last_purchase_date->diffForHumans() : 'never' }}
                                @elseif($stat['name'] == 'Frequency')
                                    Processed {{ $customer->total_orders ?? count($salesHistory) }} completed orders
                                @else
                                    Yielded Rp{{ number_format($customer->total_spent, 0, ',', '.') }} lifetime
                                @endif
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Activity Tabs --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 min-h-[500px]">
                    <div class="flex border-b border-gray-100 p-2 space-x-2">
                        <button 
                            wire:click="$set('activeTab', 'transactions')" 
                            class="flex-1 py-4 px-6 rounded-2xl text-sm font-bold tracking-tight transition-all duration-200 {{ $activeTab === 'transactions' ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Purchases
                            </div>
                        </button>
                        <button 
                            wire:click="$set('activeTab', 'loyalty')" 
                            class="flex-1 py-4 px-6 rounded-2xl text-sm font-bold tracking-tight transition-all duration-200 {{ $activeTab === 'loyalty' ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"></path></svg>
                                Points Log
                            </div>
                        </button>
                        <button 
                            wire:click="$set('activeTab', 'timeline')" 
                            class="flex-1 py-4 px-6 rounded-2xl text-sm font-bold tracking-tight transition-all duration-200 {{ $activeTab === 'timeline' ? 'bg-amber-500 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Timeline
                            </div>
                        </button>
                    </div>

                    <div class="p-8">
                        @if($activeTab === 'transactions')
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left py-4 border-b border-gray-50 font-bold uppercase text-xs tracking-widest text-gray-400">
                                        <th class="pb-4">Invoice #</th>
                                        <th class="pb-4 text-center">Items</th>
                                        <th class="pb-4">Total Amount</th>
                                        <th class="pb-4">Status</th>
                                        <th class="pb-4">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($salesHistory as $sale)
                                    <tr class="group hover:bg-gray-50 transition-colors">
                                        <td class="py-4 font-bold text-gray-900">{{ $sale->invoice_no }}</td>
                                        <td class="py-4 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-xs font-bold text-gray-600 border border-gray-200">
                                                {{ $sale->items_count ?? '0' }}
                                            </span>
                                        </td>
                                        <td class="py-4 font-black">Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                        <td class="py-4">
                                            <span class="inline-flex px-3 py-1 rounded-lg text-xs font-bold tracking-wide uppercase {{ $sale->status == 'completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                                {{ $sale->status }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-gray-500 text-sm font-medium">{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <p class="text-gray-500 font-bold">No transactions found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-6">
                                {{ $salesHistory->links() }}
                            </div>
                        </div>
                        @elseif($activeTab === 'loyalty')
                        <div class="space-y-6">
                            @forelse($loyaltyTransactions as $lt)
                            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-2xl border border-gray-100 transition-all hover:bg-white hover:shadow-md">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 transition-transform hover:rotate-12 {{ $lt->type == 'earn' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                        @if($lt->type == 'earn')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $lt->reference_type }} - {{ $lt->type == 'earn' ? 'Points Earned' : 'Points Redeemed' }}</h4>
                                        <p class="text-xs text-gray-500 font-medium">{{ $lt->created_at->format('d M Y, H:i') }} • Ref: #{{ $lt->reference_id }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-black {{ $lt->type == 'earn' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $lt->type == 'earn' ? '+' : '-' }}{{ number_format($lt->points, 0) }}
                                    </span>
                                    @if($lt->expires_at)
                                        <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mt-1">Exp: {{ $lt->expires_at->format('M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="py-12 text-center text-gray-500 font-bold">No loyalty history found.</div>
                            @endforelse
                            <div class="mt-6">
                                {{ $loyaltyTransactions->links() }}
                            </div>
                        </div>
                        @else
                        {{-- Timeline --}}
                        <div class="relative pl-8 space-y-12 before:absolute before:inset-y-0 before:left-3 before:w-1 before:bg-gradient-to-b before:from-indigo-500 before:via-purple-500 before:to-transparent before:rounded-full">
                            <div class="relative">
                                <div class="absolute -left-[2.15rem] top-1 w-6 h-6 bg-white border-4 border-indigo-500 rounded-full shadow-md z-10"></div>
                                <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-lg hover:-translate-x-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs font-black uppercase">Initial Onboarding</span>
                                        <span class="text-xs text-gray-400 font-bold">{{ $customer->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <p class="text-gray-700 font-medium text-sm">Customer account created via POS terminal.</p>
                                </div>
                            </div>
                            {{-- Add more dynamic timeline items here --}}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
