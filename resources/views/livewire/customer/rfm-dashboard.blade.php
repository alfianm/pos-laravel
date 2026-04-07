<div class="py-10 px-4 sm:px-6 lg:px-12 bg-gray-50/50 min-h-screen">
    {{-- Header Content --}}
    <div class="max-w-7xl mx-auto mb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase bg-indigo-50 text-indigo-700 border border-indigo-100 mb-2">
                    Customer Analytics
                </div>
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">RFM Behavior IQ</h2>
                <p class="text-lg text-gray-500 font-medium max-w-2xl">Segmentasi pelanggan tingkat lanjut berdasarkan frekuensi belanja otomatis melalui algoritma kuantil.</p>
            </div>
            <div class="flex gap-4">
                <button wire:click="loadStats" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all flex items-center group">
                    <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh IQ
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards & Visualization Placeholder --}}
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-50 rounded-full opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2 relative z-10">Database Health</p>
            <div class="relative z-10 flex items-end gap-3">
                <h3 class="text-4xl font-black text-gray-900">{{ number_format($distribution['total_customers'], 0, ',', '.') }}</h3>
                <span class="mb-1 text-xs font-bold px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-lg">Customers tracked</span>
            </div>
        </div>
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-50 rounded-full opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2 relative z-10">Avg IQ Score</p>
            <div class="relative z-10 flex items-end gap-3">
                <h3 class="text-4xl font-black text-indigo-600">{{ $distribution['avg_rfm_score'] }}</h3>
                <span class="mb-1 text-xs font-bold px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-lg">out of 15.0</span>
            </div>
        </div>
        {{-- Selected Filter Insight --}}
         <div class="bg-indigo-600 rounded-[2rem] p-8 shadow-xl text-white col-span-1 md:col-span-2">
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <p class="text-sm font-bold text-indigo-200 uppercase tracking-[0.2em] mb-2">Active Segment Filter</p>
                    <h3 class="text-3xl font-black italic">{{ $selectedSegment === 'all' ? 'Holistic View (All Segments)' : $selectedSegment }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-indigo-100 font-medium">{{ $selectedSegment === 'all' ? 'Viewing behavior across the entire ecosystem. High-level trends selected.' : $segmentStats[$selectedSegment]['action'] }}</p>
        </div>
    </div>

    {{-- Segment Intelligence: Masonry Grid Layout --}}
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-12">
        @foreach($segmentStats as $segment => $stats)
            <button 
                wire:click="$set('selectedSegment', '{{ $selectedSegment === $segment ? 'all' : $segment }}')"
                class="relative bg-white rounded-3xl p-6 border-2 transition-all duration-300 transform group hover:-translate-y-2 text-left cursor-pointer {{ $selectedSegment === $segment ? 'border-'.$stats['color'].'-500 shadow-xl ring-4 ring-'.$stats['color'].'-50 shadow-'.$stats['color'].'-100' : 'border-gray-100 hover:border-'.$stats['color'].'-300 hover:shadow-lg shadow-sm' }}"
            >
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-{{ $stats['color'] }}-100 flex items-center justify-center transition-transform group-hover:scale-110">
                        <span class="text-{{ $stats['color'] }}-600 font-black text-xs uppercase">{{ substr($segment, 0, 2) }}</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $stats['count'] }}</span>
                </div>
                <h4 class="text-sm font-black text-gray-900 group-hover:text-{{ $stats['color'] }}-600 transition-colors uppercase tracking-wider mb-2">{{ $segment }}</h4>
                <p class="text-[11px] leading-relaxed text-gray-400 font-bold overflow-hidden line-clamp-2 h-8">{{ $stats['description'] }}</p>
            </button>
        @endforeach
    </div>

    {{-- Customer Data Engine --}}
    <div class="max-w-7xl mx-auto bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-10 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-2xl font-black text-gray-900 italic tracking-tight">Intelligence Ledger</h3>
            <div class="flex gap-4">
                <input type="text" placeholder="Filter current segment..." class="px-6 py-3 border border-gray-200 rounded-2xl bg-white text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 w-64 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto p-2">
            <table class="w-full">
                <thead>
                    <tr class="text-left py-6 border-b border-gray-50 bg-white">
                        <th wire:click="sort('name')" class="px-8 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 cursor-pointer group whitespace-nowrap">
                            <span class="flex items-center">Customer <svg class="w-3 h-3 ml-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l3-3 3 3m0 6l-3 3-3-3"></path></svg></span>
                        </th>
                        <th wire:click="sort('recency')" class="px-8 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 cursor-pointer group whitespace-nowrap">
                            <span class="flex items-center text-rose-500">Recency IQ <svg class="w-3 h-3 ml-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l3-3 3 3m0 6l-3 3-3-3"></path></svg></span>
                        </th>
                        <th wire:click="sort('frequency')" class="px-8 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 cursor-pointer group whitespace-nowrap">
                            <span class="flex items-center text-indigo-500">Freq IQ <svg class="w-3 h-3 ml-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l3-3 3 3m0 6l-3 3-3-3"></path></svg></span>
                        </th>
                        <th wire:click="sort('monetary')" class="px-8 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 cursor-pointer group whitespace-nowrap">
                            <span class="flex items-center text-emerald-500">Monetary IQ <svg class="w-3 h-3 ml-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l3-3 3 3m0 6l-3 3-3-3"></path></svg></span>
                        </th>
                        <th wire:click="sort('rfm_score')" class="px-8 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 cursor-pointer group whitespace-nowrap">
                            <span class="flex items-center text-gray-900 border-b-2 border-gray-900 pb-1">Combined IQ <svg class="w-3 h-3 ml-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l3-3 3 3m0 6l-3 3-3-3"></path></svg></span>
                        </th>
                         <th class="px-8 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 whitespace-nowrap">Segment</th>
                        <th class="px-8 py-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $c)
                        <tr class="group hover:bg-indigo-50/20 transition-all duration-200">
                            <td class="px-8 py-8 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-xl" src="https://ui-avatars.com/api/?name={{ urlencode($c->name) }}&background=6366f1&color=fff" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $c->name }}</div>
                                        <div class="text-xs font-bold text-gray-400 font-mono">{{ $c->customer_code ?? 'CUST-XXXXX' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-black text-gray-900">{{ $c->recency_score }}.0</span>
                                    <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden border border-gray-100">
                                        <div class="h-full bg-rose-500 rounded-full" style="width: {{ ($c->recency_score/5)*100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-black text-gray-900">{{ $c->frequency_score }}.0</span>
                                    <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden border border-gray-100">
                                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ ($c->frequency_score/5)*100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-black text-gray-900">{{ $c->monetary_score }}.0</span>
                                    <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden border border-gray-100">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($c->monetary_score/5)*100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 whitespace-nowrap">
                                <span class="px-3 py-1 bg-white border-2 border-gray-900 text-gray-900 rounded-xl text-lg font-black tracking-tighter">
                                    {{ $c->recency_score + $c->frequency_score + $c->monetary_score }}<span class="text-xs opacity-50 ml-0.5">/15</span>
                                </span>
                            </td>
                            <td class="px-8 py-8 whitespace-nowrap">
                                @php
                                    $color = $segmentStats[$c->rfm_segment]['color'] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em] bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100 ring-2 ring-{{ $color }}-50 ring-offset-1 ring-offset-white">
                                    {{ $c->rfm_segment }}
                                </span>
                            </td>
                            <td class="px-8 py-8 whitespace-nowrap text-right">
                                <a href="{{ route('crm.leads.show', $c->id) }}" class="inline-flex items-center px-6 py-2.5 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all hover:scale-110 hover:-rotate-2">
                                    Full 360° View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="text-5xl mb-4 grayscale opacity-50 transition-all hover:grayscale-0 hover:opacity-100 cursor-default">🔍</div>
                                    <p class="text-xl font-bold text-gray-400 italic">No intelligence matched current parameters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-10 border-t border-gray-100 bg-gray-50/50">
            {{ $customers->links() }}
        </div>
    </div>
</div>