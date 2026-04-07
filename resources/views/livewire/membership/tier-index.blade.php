<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Membership Tiers</h1>
            <p class="text-sm text-gray-500 mt-1">Atur tingkatan member dan reward poin berdasarkan pengeluaran.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('membership.tiers.create') }}" wire:navigate 
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Tier
            </a>
        </div>
    </div>

    <!-- Stats summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest leading-none">Total Tiers</p>
                <p class="text-xl font-black text-gray-900 mt-1">{{ $tiers->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.519-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest leading-none">Top Tier Spend</p>
                <p class="text-xl font-black text-gray-900 mt-1">Rp{{ number_format($tiers->max('min_spending') ?: 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest leading-none">Highest Multiplier</p>
                <p class="text-xl font-black text-gray-900 mt-1">{{ $tiers->max('point_multiplier') ?: '1.0' }}x</p>
            </div>
        </div>
    </div>

    <!-- List table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama tier..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Tier Name</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-right">Min. Spending</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-center">Point Multiplier</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tiers as $tier)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20 font-black text-xs uppercase">
                                        {{ substr($tier->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 leading-tight">{{ $tier->name }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5 tracking-tight uppercase font-medium">Auto-upgrade enabled</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right font-black text-gray-900 tracking-tight">
                                Rp{{ number_format($tier->min_spending, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black tracking-widest uppercase ring-1 ring-emerald-100">
                                        {{ (float)$tier->point_multiplier }}x Multiplier
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('membership.tiers.edit', $tier->id) }}" wire:navigate 
                                       class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all active:scale-90" title="Edit Tier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button onclick="confirm('Yakin ingin menghapus tier ini?') || event.stopImmediatePropagation()" 
                                            wire:click="deletingTier('{{ $tier->id }}')" 
                                            class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-90" title="Hapus Tier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <p class="text-gray-900 font-bold tracking-tight">Belum ada Membership Tier</p>
                                <p class="text-sm text-gray-400 mt-1">Gunakan tombol Tambah Tier di atas untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tiers->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $tiers->links() }}
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-6 right-6 px-6 py-4 bg-emerald-600 text-white rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in-up z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-6 right-6 px-6 py-4 bg-rose-600 text-white rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in-up z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="font-bold tracking-tight">{{ session('error') }}</span>
        </div>
    @endif
</div>
