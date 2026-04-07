<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Penawaran Harga (Proposal)</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium italic">Kelola kuotasi dan penawaran aktif Anda.</p>
            </div>
            <div>
                <a href="{{ route('crm.proposals.create') }}" wire:navigate class="inline-flex items-center px-10 py-4 bg-indigo-600 border border-transparent rounded-[2rem] font-black text-sm text-white hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 whitespace-nowrap uppercase tracking-widest leading-none">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Buat Proposal
                </a>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50 p-6 mb-8 mx-4 sm:mx-0 flex flex-col md:flex-row gap-6">
            <div class="relative flex-1 group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Proposal atau Nama Lead..." class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-medium">
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-2xl sm:rounded-[3rem] border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Invoice / Proposal</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Customer / Lead</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono text-center">Date / Valid</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Status</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono text-right">Total Amount</th>
                             <th class="px-8 py-5 text-right w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($proposals as $prop)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all">
                             <td class="px-8 py-7">
                                 <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">PROPOSAL</div>
                                 <div class="text-sm font-black text-slate-900 dark:text-white tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $prop->proposal_no }}</div>
                             </td>
                             <td class="px-8 py-7">
                                 <div class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $prop->lead->name ?? ($prop->customer->name ?? '-') }}</div>
                                 <div class="text-[10px] text-indigo-500 dark:text-indigo-400 mt-1 font-black uppercase tracking-widest">{{ $prop->lead_id ? 'PROSPEK' : 'CUSTOMER' }}</div>
                             </td>
                             <td class="px-8 py-7 text-center">
                                 <div class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $prop->proposal_date->format('d/m/Y') }}</div>
                                 <div class="text-[10px] text-slate-400 font-medium italic">Hingga {{ $prop->valid_until->format('d/m/Y') }}</div>
                             </td>
                             <td class="px-8 py-7">
                                 <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ 
                                     $prop->status === 'accepted' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30' : 
                                     ($prop->status === 'rejected' ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/30' : 'bg-slate-100 text-slate-600 dark:bg-slate-700')
                                 }}">
                                     {{ $prop->status }}
                                 </span>
                             </td>
                             <td class="px-8 py-7 text-right">
                                 <div class="text-base font-black text-slate-900 dark:text-white tracking-tight">Rp {{ number_format($prop->total_amount, 0, ',', '.') }}</div>
                             </td>
                             <td class="px-8 py-7 text-right">
                                 <a href="#" class="p-3 text-slate-300 hover:text-indigo-600 transition-colors group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 rounded-2xl inline-block">
                                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                 </a>
                             </td>
                        </tr>
                        @empty
                        <tr>
                             <td colspan="6" class="px-8 py-32 text-center">
                                 <div class="flex flex-col items-center">
                                      <div class="p-10 bg-slate-50 dark:bg-slate-900 rounded-[3rem] mb-6 shadow-inner">
                                          <svg class="w-16 h-16 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                      </div>
                                      <p class="text-slate-900 dark:text-white font-black text-xl mb-1">Daftar Proposal Kosong</p>
                                      <p class="text-slate-400 font-medium">Buat penawaran harga pertama untuk prospek Anda.</p>
                                 </div>
                             </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($proposals->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-50 dark:border-slate-700">
                    {{ $proposals->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
