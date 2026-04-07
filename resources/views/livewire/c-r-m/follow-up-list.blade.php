<div class="space-y-6" x-data="{ completeModal: false, completeId: null, completeNotes: '' }">
    <div class="flex items-center justify-between">
        <h3 class="text-xl font-black text-slate-900 dark:text-white">Aktivitas Follow-up</h3>
        <button wire:click="toggleForm" class="px-5 py-2.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-90 text-[10px] uppercase tracking-widest leading-none">
            {{ $showForm ? 'Batal' : 'Jadwalkan Follow-up' }}
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-400 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if($showForm)
    <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-700 animate-fade-in-down">
        <form wire:submit.prevent="saveFollowUp" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Media Komunikasi</label>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach(['call' => '📞', 'email' => '✉️', 'chat' => '💬', 'meet' => '🤝'] as $t => $emoji)
                            <button type="button" wire:click="$set('type', '{{ $t }}')" class="py-4 rounded-2xl border-2 transition-all font-black text-lg {{ $type === $t ? 'bg-indigo-600 border-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 text-slate-400 hover:border-indigo-300' }}">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                    @error('type') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Jadwal</label>
                    <input type="datetime-local" wire:model="scheduled_at" class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold">
                    @error('scheduled_at') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Catatan / Ringkasan</label>
                <textarea wire:model="content" rows="3" class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="Tuliskan poin penting..."></textarea>
                @error('content') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-800">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model.live="is_recurring" class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Jadwalkan Berulang</span>
                </label>

                @if($is_recurring)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tipe Pengulangan</label>
                        <select wire:model="recurrence_type" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-bold">
                            <option value="">Pilih...</option>
                            <option value="daily">Harian</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                        @error('recurrence_type') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Setiap</label>
                        <input type="number" wire:model="recurrence_interval" min="1" max="30" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-bold">
                        @error('recurrence_interval') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai</label>
                        <input type="date" wire:model="recurrence_end_date" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-bold">
                        @error('recurrence_end_date') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Pengingat</label>
                <select wire:model="reminder_minutes_before" class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold">
                    <option value="">Tanpa pengingat</option>
                    <option value="5">5 menit sebelum</option>
                    <option value="10">10 menit sebelum</option>
                    <option value="15">15 menit sebelum</option>
                    <option value="30">30 menit sebelum</option>
                    <option value="60">1 jam sebelum</option>
                </select>
            </div>

            <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 uppercase tracking-widest text-xs flex justify-center items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwalkan Follow-up
            </button>
        </form>
    </div>
    @endif

    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <button wire:click="setFilter('all')" class="px-4 py-2 rounded-xl font-bold text-xs transition-all {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
            Semua
        </button>
        <button wire:click="setFilter('pending')" class="px-4 py-2 rounded-xl font-bold text-xs transition-all {{ $filter === 'pending' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
            Menunggu
        </button>
        <button wire:click="setFilter('overdue')" class="px-4 py-2 rounded-xl font-bold text-xs transition-all {{ $filter === 'overdue' ? 'bg-rose-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
            Terlambat
        </button>
        <button wire:click="setFilter('completed')" class="px-4 py-2 rounded-xl font-bold text-xs transition-all {{ $filter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
            Selesai
        </button>
    </div>

    <div class="space-y-4">
        @forelse($followUps as $fu)
        <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-lg transition-all relative overflow-hidden @if($fu->isOverdue()) border-l-4 border-l-rose-500 @endif @if($fu->status === 'completed') border-l-4 border-l-emerald-500 @endif">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-2xl flex items-center justify-center text-xl shadow-inner border border-slate-100 dark:border-slate-700">
                        @if($fu->type === 'call') 📞
                        @elseif($fu->type === 'email') ✉️
                        @elseif($fu->type === 'chat') 💬
                        @elseif($fu->type === 'meet') 🤝
                        @else ❓ @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-[10px] font-black uppercase tracking-widest @if($fu->status === 'completed') text-emerald-600 @elseif($fu->isOverdue()) text-rose-600 @else text-indigo-600 @endif">
                                {{ $fu->status === 'completed' ? 'SELESAI' : ($fu->isOverdue() ? 'TERLAMBAT' : 'MENUNGGU') }}
                            </span>
                            <span class="text-[10px] text-slate-300">•</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $fu->scheduled_at->format('d M Y, H:i') }}</span>
                            @if($fu->is_recurring)
                                <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[9px] font-bold rounded-full">BERULANG</span>
                            @endif
                            @if($fu->reminder_minutes_before)
                                <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-[9px] font-bold rounded-full">⏰ {{ $fu->reminder_minutes_before }}m</span>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-relaxed">{{ $fu->notes ?? $fu->content ?? '-' }}</p>
                        @if($fu->completed_at)
                            <p class="text-[10px] text-slate-400 mt-1">Selesai: {{ $fu->completed_at->format('d M Y, H:i') }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest block mb-1">OLEH</span>
                    <span class="text-xs font-bold text-slate-400 group-hover:text-indigo-600 transition-colors">{{ $fu->performer->name ?? 'System' }}</span>
                </div>
            </div>

            @if($fu->status === 'pending')
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex gap-2">
                <button wire:click="openCompleteModal('{{ $fu->id }}')" class="flex-1 px-4 py-2.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 font-bold text-xs rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Selesaikan
                </button>
                <button wire:click="cancelFollowUp('{{ $fu->id }}')" wire:confirm="Yakin ingin membatalkan follow-up ini?" class="px-4 py-2.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all">
                    Batalkan
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-16 bg-slate-50 dark:bg-slate-900/50 rounded-3xl">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-3xl mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <p class="text-slate-400 font-bold">Belum ada follow-up tercatat.</p>
            <p class="text-slate-400 text-sm mt-1">Klik "Jadwalkan Follow-up" untuk menambah.</p>
        </div>
        @endforelse

        {{ $followUps->links() }}
    </div>

    @if($completeFollowUpId)
    <div x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click="$set('completeFollowUpId', null)" onclick="$wire.completeFollowUpId = null">
        <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden" wire:click.stop>
            <div class="p-8">
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6">Selesaikan Follow-up</h3>
                <form wire:submit.prevent="completeFollowUp">
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 block mb-2">Catatan Penyelesaian (Opsional)</label>
                            <textarea wire:model="completeNotes" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tuliskan hasil follow-up..."></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" wire:click="$set('completeFollowUpId', null')" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-all text-sm">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all text-sm">
                            Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>