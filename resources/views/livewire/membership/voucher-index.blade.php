<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Voucher & Promo</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola voucher belanja dan diskon untuk pelanggan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('membership.vouchers.create') }}" wire:navigate 
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Buat Voucher
            </a>
        </div>
    </div>

    <!-- Voucher Grid/List -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode voucher..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Voucher Info</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Type & Value</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Validity</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-center">Usage</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs border border-indigo-100">
                                        {{ $voucher->type === 'fixed' ? 'Rp' : '%' }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900 tracking-tight leading-none">{{ $voucher->code }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1.5 font-bold uppercase tracking-widest">Min. Order: Rp{{ number_format($voucher->min_order_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900">
                                        {{ $voucher->type === 'fixed' ? 'Rp' . number_format($voucher->value, 0, ',', '.') : $voucher->value . '%' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $voucher->type }} discount</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">
                                        Start: {{ $voucher->starts_at ? $voucher->starts_at->format('d M Y') : '-' }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">
                                        End: {{ $voucher->ends_at ? $voucher->ends_at->format('d M Y') : 'No Limit' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center">
                                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden mb-1.5 max-w-[80px]">
                                        @php 
                                            $percent = $voucher->usage_limit ? ($voucher->used_count / $voucher->usage_limit) * 100 : 0;
                                        @endphp
                                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                                    </div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">
                                        {{ $voucher->used_count }} / {{ $voucher->usage_limit ?: '∞' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('membership.vouchers.edit', $voucher->id) }}" wire:navigate 
                                       class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button onclick="confirm('Yakin ingin menghapus voucher ini?') || event.stopImmediatePropagation()" 
                                            wire:click="deletingVoucher('{{ $voucher->id }}')" 
                                            class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <p class="text-gray-900 font-bold tracking-tight">Belum ada Voucher</p>
                                <p class="text-sm text-gray-400 mt-1">Buat voucher diskon untuk menarik lebih banyak pelanggan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>

    {{-- Success/Error Toasts --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-6 right-6 px-6 py-4 bg-emerald-600 text-white rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in-up z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold tracking-tight">{{ session('success') }}</span>
        </div>
    @endif
</div>
