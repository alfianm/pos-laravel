<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-10 gap-4">
            <div class="flex items-center gap-6">
                <a href="{{ route('crm.leads.index') }}" wire:navigate class="p-3 bg-white dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all shadow-sm border border-slate-200 dark:border-slate-700 active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <div class="flex items-center gap-4">
                        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $lead->name }}</h1>
                        <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-full">{{ $lead->stage->name ?? 'New' }}</span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Lead ID: <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 font-mono text-xs ml-1">{{ $lead->lead_no }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(session()->has('success'))
                    <div class="px-4 py-3 bg-emerald-50 text-emerald-600 text-xs font-black rounded-xl border border-emerald-100 flex items-center gap-2 animate-bounce">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                <a href="{{ route('crm.leads.edit', $lead->id) }}" wire:navigate class="px-6 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 shadow-sm text-sm">Ubah Lead</a>
                
                @if($lead->status !== 'converted')
                    <button wire:click="convertToCustomer" wire:confirm="Apakah Anda yakin ingin mengubah lead ini menjadi customer tetap?" class="px-8 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 text-sm uppercase tracking-widest">Konversi Customer</button>
                @else
                    <span class="px-8 py-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-black rounded-2xl text-sm uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Converted
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Left Column: Details --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Info Grid --}}
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-8">Data Lengkap Prospek</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Email Address</label>
                                <p class="text-slate-900 dark:text-white font-bold">{{ $lead->email ?: 'Tidak mencantumkan email' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">WhatsApp / No. HP</label>
                                <p class="text-slate-900 dark:text-white font-black text-xl tracking-tight">{{ $lead->phone ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Sumber Lead</label>
                                <div class="inline-block px-3 py-1 bg-slate-50 dark:bg-slate-900/50 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-700">{{ $lead->source->name ?? 'Organic' }}</div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Cabang Penanggung Jawab</label>
                                <p class="text-slate-900 dark:text-white font-bold">{{ $lead->branch->name ?? 'Semua Cabang' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Sales Assigned</label>
                                <p class="text-slate-900 dark:text-white font-bold">{{ $lead->assignee->name ?? 'Unassigned' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Tanggal Entry</label>
                                <p class="text-slate-900 dark:text-white font-bold">{{ $lead->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-slate-50 dark:border-slate-700">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-2">Alamat Lengkap</label>
                        <p class="text-slate-600 dark:text-slate-300 font-medium leading-relaxed">{{ $lead->address ?: 'Alamat belum diinput.' }}</p>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-50 dark:border-slate-700">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-2">Catatan Internal</label>
                        <div class="bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-400 p-4 rounded-r-2xl">
                             <p class="text-amber-800 dark:text-amber-200 text-sm font-medium italic">{{ $lead->notes ?: 'Belum ada catatan khusus.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-8">Customer Timeline</h3>
                    <div class="relative space-y-8 before:absolute before:inset-y-0 before:left-[15px] before:w-0.5 before:bg-slate-100 dark:before:bg-slate-700 pl-4">
                        @forelse($lead->timelines as $event)
                        <div class="relative pl-10">
                            <div class="absolute left-[-21px] top-1.5 w-8 h-8 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center shadow-sm {{ 
                                $event->event_type === 'lead_created' ? 'bg-indigo-500' : 'bg-emerald-500'
                            }}">
                                 @if($event->event_type === 'lead_created')
                                     <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                 @else
                                     <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                 @endif
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1 font-mono">{{ $event->created_at->format('d M Y, H:i') }}</span>
                                <p class="text-slate-900 dark:text-white font-black tracking-tight leading-none mb-1">{{ $event->title }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">{{ $event->description }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <p class="text-slate-400 font-medium italic">Belum ada aktivitas tercatat.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Activity History --}}
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50">
                    <livewire:c-r-m.follow-up-list :leadId="$lead->id" :wire:key="'fw-'.$lead->id" />
                </div>
            </div>

            {{-- Right Column: Follow-up Actions --}}
            <div class="space-y-8">
                <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-600/30">
                    <h4 class="text-lg font-black mb-2 tracking-tight">Cepat & Responsif</h4>
                    <p class="text-indigo-100/80 text-sm font-medium leading-relaxed mb-6">Hubungi prospek segera untuk menaikkan tingkat konversi hingga 40%.</p>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <a href="tel:{{ $lead->phone }}" class="p-4 bg-white/10 hover:bg-white/20 rounded-2xl transition-all border border-white/10 group active:scale-95">
                             <div class="text-lg mb-1 group-hover:scale-110 transition-transform">📞</div>
                             <span class="text-[10px] font-black uppercase tracking-widest text-white/90">Telepon</span>
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="p-4 bg-white/10 hover:bg-white/20 rounded-2xl transition-all border border-white/10 group active:scale-95">
                             <div class="text-lg mb-1 group-hover:scale-110 transition-transform">💬</div>
                             <span class="text-[10px] font-black uppercase tracking-widest text-white/90">WhatsApp</span>
                        </a>
                    </div>
                </div>

                {{-- Status Card --}}
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl border border-slate-100 dark:border-slate-700/50">
                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 border-b border-slate-50 dark:border-slate-700 pb-4">Statistik Prospek</h4>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Follow-ups</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white">{{ $lead->follow_ups_count }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Days in Pipeline</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white underline decoration-indigo-500/30 underline-offset-4 decoration-2">
                                {{ $lead->created_at->diffInDays(now()) }} Hari
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Confidence Score</span>
                            <span class="text-xs font-black text-emerald-500 uppercase tracking-widest px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">High Match</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
