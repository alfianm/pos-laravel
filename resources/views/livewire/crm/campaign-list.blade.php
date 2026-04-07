<div class="py-10 px-4 sm:px-6 lg:px-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto mb-10 block md:flex items-end justify-between gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase bg-indigo-50 text-indigo-700 border border-indigo-100 mb-2">
                Marketing Automation
            </div>
            <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">Campaign Studio</h2>
            <p class="text-lg text-gray-500 font-medium">Design outreach strategies linked directly to behavioral segments.</p>
        </div>
        <div class="mt-6 md:mt-0">
            <a href="{{ route('crm.campaigns.form') }}" class="px-8 py-4 bg-gray-900 text-white rounded-[2rem] shadow-xl text-sm font-black uppercase tracking-widest hover:bg-indigo-600 transition-all hover:scale-105 active:scale-95 flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Launch New Campaign
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
        {{-- Toolbar --}}
        <div class="p-10 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50/50 gap-6">
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input wire:model.live="search" type="text" placeholder="Search strategy names..." class="w-full pl-12 pr-6 py-4 border border-gray-200 rounded-3xl bg-white text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 transition-all font-medium">
            </div>

            <div class="flex gap-4 w-full md:w-auto">
                <select wire:model.live="status" class="flex-1 md:flex-none px-8 py-4 border border-gray-200 rounded-3xl bg-white text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 appearance-none pr-12 transition-all">
                    <option value="all">All States</option>
                    <option value="draft">Drafts</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="running">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left py-6 border-b border-gray-50">
                        <th class="px-10 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400">Campaign & Type</th>
                        <th class="px-10 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400">Target Segment</th>
                        <th class="px-10 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400">Benefit IQ</th>
                        <th class="px-10 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400 text-center">Engagement</th>
                        <th class="px-10 py-6 text-xs font-black uppercase tracking-[0.2em] text-gray-400">Status</th>
                        <th class="px-10 py-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($campaigns as $camp)
                        <tr class="group hover:bg-indigo-50/20 transition-all duration-300">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 transition-transform group-hover:rotate-6">
                                        @if($camp->type === 'voucher')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                        @elseif($camp->type === 'broadcast')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="space-y-1">
                                        <div class="text-sm font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $camp->name }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $camp->type }} Strategy</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $camp->target_segment ? 'bg-indigo-50 border-indigo-100 text-indigo-700' : 'bg-gray-50 border-gray-100 text-gray-500' }}">
                                    {{ $camp->target_segment ?? 'Omni (All)' }}
                                </span>
                            </td>
                            <td class="px-10 py-8">
                                @if($camp->voucher)
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 italic font-serif">#{{ $camp->voucher->code }}</span>
                                        <span class="text-[10px] font-bold text-emerald-600">{{ $camp->voucher->type === 'percentage' ? $camp->voucher->discount_value.'%' : 'Rp'.number_format($camp->voucher->discount_value) }} discount</span>
                                    </div>
                                @elseif($camp->bonus_points > 0)
                                    <span class="text-sm font-black text-amber-600">+{{ number_format($camp->bonus_points) }} Bonus IQ</span>
                                @else
                                    <span class="text-sm font-bold text-gray-400 italic">No Benefit Linked</span>
                                @endif
                            </td>
                            <td class="px-10 py-8 text-center">
                                <div class="flex justify-center gap-6">
                                    <div class="text-center">
                                        <p class="text-[9px] font-black uppercase text-gray-400 mb-1">Reachable</p>
                                        <p class="text-lg font-black text-gray-900">{{ number_format($camp->reach_count) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] font-black uppercase text-gray-400 mb-1">Conversions</p>
                                        <p class="text-lg font-black text-emerald-600">{{ number_format($camp->conversion_count) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                @php
                                    $statusColor = match($camp->status) {
                                        'running' => 'emerald',
                                        'scheduled' => 'indigo',
                                        'completed' => 'blue',
                                        'cancelled' => 'rose',
                                        default => 'gray'
                                    };
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-{{ $statusColor }}-500 mr-2 animate-pulse"></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-{{ $statusColor }}-700 italic font-serif">{{ $camp->status }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0">
                                    <a href="{{ route('crm.campaigns.form', $camp->id) }}" class="p-3 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-900 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <button wire:click="deleteCampaign('{{ $camp->id }}')" class="p-3 bg-white border border-gray-200 rounded-xl text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 grayscale hover:grayscale-0 transition-all duration-700 hover:rotate-12 cursor-pointer">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                    </div>
                                    <h5 class="text-xl font-black text-gray-400 italic">No marketing campaigns detected.</h5>
                                    <p class="text-gray-400 font-medium mt-2">Start your automated presence by launching a new outreach strategy.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-10 border-t border-gray-100 bg-gray-50/50">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
