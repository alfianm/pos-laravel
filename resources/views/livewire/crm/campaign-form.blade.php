<div class="py-10 px-4 sm:px-6 lg:px-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-4xl mx-auto mb-10">
        <a href="{{ route('crm.campaigns.index') }}" class="inline-flex items-center text-sm font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors mb-6 group">
            <svg class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Strategy HQ
        </a>

        <div class="bg-white rounded-[3rem] p-12 shadow-xl border border-gray-100 relative overflow-hidden group">
            {{-- Accent Gradients --}}
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-50/50 rounded-full blur-3xl transition-all group-hover:scale-125 duration-1000"></div>
            
            <div class="relative z-10">
                <div class="mb-12">
                    <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight italic">{{ $campaignId ? 'Refine Strategy' : 'Strategy Architect' }}</h2>
                    <p class="text-lg text-gray-500 font-medium mt-2">Design an automated presence with precision targeting.</p>
                </div>

                <form wire:submit="save" class="space-y-10">
                    {{-- Primary Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 pl-2">Strategy Name</label>
                            <input wire:model="name" type="text" class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all shadow-inner" placeholder="e.g. VIP Recovery Mar 2026">
                            @error('name') <span class="text-rose-500 text-[10px] font-black uppercase tracking-widest pl-4">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 pl-2">Presence Channel</label>
                            <select wire:model.live="type" class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all shadow-inner appearance-none pr-12">
                                <option value="voucher">Voucher Injection (Redeemable)</option>
                                <option value="broadcast">Broadcasting Unit (Notice Only)</option>
                                <option value="loyalty_bonus">Point Escalation (Loyalty Bonus)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Segmentation Targeting --}}
                    <div class="p-10 bg-indigo-50/30 rounded-[2.5rem] border border-indigo-50/50 relative group/target">
                        <div class="absolute top-6 right-8 text-indigo-300 opacity-20 group-hover/target:opacity-40 transition-opacity">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>

                        <h4 class="text-lg font-black text-indigo-900 mb-6 italic">Target Audience Intelligence</h4>
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-[0.2em] text-indigo-400 pl-2">Segment Cluster</label>
                                <select wire:model="target_segment" class="w-full px-8 py-5 bg-white border border-indigo-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                    @foreach($segments as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Benefits & Logic --}}
                    <div class="p-10 bg-emerald-50/30 rounded-[2.5rem] border border-emerald-50/50 relative group/benefit">
                         <div class="absolute top-6 right-8 text-emerald-300 opacity-20 group-hover/benefit:opacity-40 transition-opacity">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>

                        <h4 class="text-lg font-black text-emerald-900 mb-6 italic">Benefit IQ Mapping</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            @if($type === 'voucher')
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400 pl-2">Voucher Unit</label>
                                    <select wire:model="voucher_id" class="w-full px-8 py-5 bg-white border border-emerald-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm">
                                        <option value="">Select Benefit Unit...</option>
                                        @foreach($vouchers as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($type === 'loyalty_bonus')
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400 pl-2">Points Escalation</label>
                                    <div class="relative">
                                        <input wire:model="bonus_points" type="number" class="w-full px-8 py-5 bg-white border border-emerald-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm" placeholder="e.g. 50">
                                        <span class="absolute right-8 top-1/2 -translate-y-1/2 text-xs font-black text-emerald-600 uppercase tracking-widest">IQ Points</span>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-2 md:col-span-1">
                                <label class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400 pl-2">Deployment Status</label>
                                <select wire:model="status" class="w-full px-8 py-5 bg-white border border-emerald-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm">
                                    <option value="draft">Blueprint (Draft)</option>
                                    <option value="scheduled">Committed (Scheduled)</option>
                                    <option value="running" class="text-emerald-600 font-black">Live Deployment (Active)</option>
                                    <option value="completed">Archive (Completed)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Message & Content --}}
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 pl-2">Communication Payload (Message)</label>
                        <textarea wire:model="message" rows="4" class="w-full px-8 py-6 bg-gray-50 border border-gray-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all shadow-inner" placeholder="Your strategy message here..."></textarea>
                    </div>

                    {{-- Scheduling --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 pl-2">Strategy Start</label>
                            <input wire:model="starts_at" type="datetime-local" class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 pl-2">Strategy Termination</label>
                            <input wire:model="ends_at" type="datetime-local" class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[2rem] text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="pt-10 flex justify-end gap-6 border-t border-gray-50">
                        <a href="{{ route('crm.campaigns.index') }}" class="px-10 py-5 text-gray-400 hover:text-gray-900 text-sm font-black uppercase tracking-widest transition-colors">Abort Strategy</a>
                        <button type="submit" class="px-16 py-5 bg-gray-900 text-white rounded-[2rem] shadow-2xl text-sm font-black uppercase tracking-widest hover:bg-emerald-600 transition-all hover:scale-105 active:scale-95 shadow-emerald-200">
                             {{ $campaignId ? 'Execute Update' : 'Launch Strategy' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
