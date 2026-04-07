<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Kustom Domain (White-label)</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gunakan domain kustom Anda sendiri untuk toko ini.</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-400 font-bold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Instructions Column --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- DNS Setup Instructions --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2rem] border border-indigo-100 dark:border-gray-700 relative">
                    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                        <svg class="w-32 h-32 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>

                    <div class="p-8">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Instruksi Konfigurasi DNS</h3>
                        
                        <div class="prose prose-slate dark:prose-invert max-w-none">
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Untuk menghubungkan domain kustom Anda, silakan buat record DNS berikut pada domain registrar Anda (GoDaddy, Namecheap, Cloudflare, dll):
                            </p>

                            <div class="space-y-4">
                                <div class="bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-2xl p-6">
                                    <h4 class="text-indigo-900 dark:text-indigo-300 font-bold mb-3 flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-white text-[10px]">1</span>
                                        Root Domain (A Record)
                                    </h4>
                                    <p class="text-sm text-indigo-800/70 dark:text-indigo-400/70 mb-3">Gunakan untuk domain utama (misal: myshop.id)</p>
                                    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 font-mono text-sm border border-indigo-100 dark:border-gray-700 flex justify-between items-center group">
                                        <code class="text-indigo-600 dark:text-indigo-400">@ &nbsp;&nbsp; A &nbsp;&nbsp; {{ $serverIp }}</code>
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg text-gray-400 hover:text-indigo-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-2xl p-6">
                                    <h4 class="text-indigo-900 dark:text-indigo-300 font-bold mb-3 flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-white text-[10px]">2</span>
                                        Subdomain (CNAME Record)
                                    </h4>
                                    <p class="text-sm text-indigo-800/70 dark:text-indigo-400/70 mb-3">Gunakan untuk subdomain (misal: shop.mydomain.com)</p>
                                    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 font-mono text-sm border border-indigo-100 dark:border-gray-700 flex justify-between items-center group">
                                        <code class="text-indigo-600 dark:text-indigo-400">shop &nbsp;&nbsp; CNAME &nbsp;&nbsp; cname.rasanusa.com</code>
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg text-gray-400 hover:text-indigo-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/10 border-l-4 border-amber-400 rounded-r-2xl">
                                <div class="flex gap-3">
                                    <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm text-amber-800 dark:text-amber-400">
                                        <strong>Catatan:</strong> Perubahan DNS dapat memakan waktu hingga 24 jam (Propagasi). Tim kami akan menerbitkan sertifikat SSL otomatis segera setelah domain Anda terpointing dengan benar.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Domain List Card --}}
                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Domain Anda</h3>
                        <div class="overflow-x-auto -mx-8">
                            <table class="w-full text-left border-separate border-spacing-0">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Domain</th>
                                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">SSL</th>
                                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    @forelse($domains as $item)
                                    <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/5 transition-all">
                                        <td class="px-8 py-5">
                                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ $item->domain }}</div>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($item->is_primary)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase tracking-tighter">Primary</span>
                                                @endif
                                                @if($item->is_verified)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-tighter border border-emerald-100 dark:border-emerald-800">Verified</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 uppercase tracking-tighter border border-amber-100 dark:border-amber-800">Verifying...</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @php
                                                $sslColor = match($item->ssl_status) {
                                                    'active' => 'emerald',
                                                    'pending' => 'amber',
                                                    default => 'rose'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-sm font-bold text-{{ $sslColor }}-600 dark:text-{{ $sslColor }}-400 capitalize">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"></path></svg>
                                                {{ $item->ssl_status }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @if($item->is_active)
                                                <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-black text-xs uppercase tracking-widest">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-gray-400 font-bold text-xs uppercase tracking-widest">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-8 py-12 text-center text-gray-400 italic">Belum ada domain kustom</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="space-y-6">
                {{-- Add Domain Card --}}
                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Ajukan Domain</h3>
                    <form wire:submit.prevent="addDomain" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Domain Anda</label>
                            <input type="text" wire:model="newDomain" placeholder="contoh: shop.mybrand.com" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                            @error('newDomain') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center px-6 py-4 bg-indigo-600 border border-transparent rounded-2xl font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95">
                                <span wire:loading.remove>Hubungkan Domain</span>
                                <span wire:loading>Memproses...</span>
                        </button>
                    </form>
                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg text-indigo-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-2.332 9-7.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider mb-1">Gratis SSL</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Setiap domain kustom mendapatkan sertifikat SSL Gratis selamanya.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg text-indigo-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider mb-1">White-label</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hilangkan brand RasaNusa dari domain di hadapan pelanggan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Need Help Card --}}
                <div class="bg-gradient-to-br from-gray-900 to-slate-800 p-8 rounded-[2rem] text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <h3 class="text-xl font-black mb-2 relative z-10">Butuh Bantuan?</h3>
                    <p class="text-sm text-gray-300 mb-6 relative z-10 leading-relaxed">Tim dukungan kami siap membantu mengonfigurasi domain DNS Anda.</p>
                    <a href="#" class="relative z-10 inline-flex items-center text-sm font-bold text-indigo-400 hover:text-indigo-300 transition-colors">
                        Buka Tiket Dukungan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
