<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Data Lead / Prospek</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium italic">Kelola calon pelanggan potensial dan pantau progres konversi.</p>
            </div>
            <div>
                <a href="{{ route('crm.leads.create') }}" wire:navigate class="inline-flex items-center px-10 py-4 bg-indigo-600 border border-transparent rounded-[2rem] font-black text-sm text-white hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 whitespace-nowrap uppercase tracking-widest leading-none">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Lead
                </a>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50 p-6 mb-8 mx-4 sm:mx-0 flex flex-col md:flex-row gap-6">
            <div class="relative flex-1 group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama, No. HP, atau ID Lead..." class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-medium">
            </div>
            <div class="sm:w-64">
                <select wire:model.live="filterStage" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-gray-500 font-bold text-xs uppercase tracking-widest focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    <option value="">Semua Tahapan</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-2xl sm:rounded-[3rem] border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Lead & Summary</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Contact Info</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Stage / Status</th>
                             <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">Source</th>
                             <th class="px-8 py-5 text-right w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($leads as $lead)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all">
                             <td class="px-8 py-7">
                                 <div class="flex items-center gap-4">
                                     <div class="relative">
                                         <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 font-black text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                                             {{ substr($lead->name, 0, 1) }}
                                         </div>
                                     </div>
                                     <div>
                                         <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">{{ $lead->lead_no }}</div>
                                         <div class="text-sm font-black text-slate-900 dark:text-white tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $lead->name }}</div>
                                     </div>
                                 </div>
                             </td>
                             <td class="px-8 py-7">
                                 <div class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $lead->phone ?? '-' }}</div>
                                 <div class="text-xs text-slate-400 mt-1 font-medium truncate max-w-[150px]">{{ $lead->email ?? 'no-email@store.com' }}</div>
                             </td>
                             <td class="px-8 py-7">
                                 <div class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ 
                                     $lead->status === 'converted' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 
                                     'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400'
                                 }}">
                                     {{ $lead->stage->name ?? 'Prospect' }}
                                 </div>
                             </td>
                             <td class="px-8 py-7">
                                 <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-3 py-1 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                     {{ $lead->source->name ?? 'Organic' }}
                                 </span>
                             </td>
                             <td class="px-8 py-7 text-right">
                                 <a href="{{ route('crm.leads.show', $lead->id) }}" wire:navigate class="p-3 text-slate-300 hover:text-indigo-600 transition-colors group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 rounded-2xl inline-block">
                                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                 </a>
                             </td>
                        </tr>
                        @empty
                        <tr>
                             <td colspan="5" class="px-8 py-32 text-center">
                                 <div class="flex flex-col items-center">
                                      <div class="p-10 bg-slate-50 dark:bg-slate-900 rounded-[3rem] mb-6 shadow-inner">
                                          <svg class="w-16 h-16 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                      </div>
                                      <p class="text-slate-900 dark:text-white font-black text-xl mb-1">Belum ada Lead</p>
                                      <p class="text-slate-400 font-medium">Gunakan tombol 'Tambah Lead' untuk mulai mencatat prospek.</p>
                                 </div>
                             </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-50 dark:border-slate-700">
                    {{ $leads->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
