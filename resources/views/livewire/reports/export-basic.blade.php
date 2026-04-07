<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-8 tracking-tight">Export Laporan Dasar</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Sales Export Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all group">
                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/10 rounded-2xl flex items-center justify-center mb-6 text-blue-600 dark:text-blue-400 font-bold text-2xl group-hover:rotate-12 transition-transform">📊</div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Laporan Penjualan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8">Export seluruh data transaksi penjualan dalam format CSV untuk analisis spreadsheet.</p>
                <button wire:click="exportSales" class="w-full py-4 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-black rounded-2xl hover:bg-slate-800 dark:hover:bg-white transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-3 active:scale-95 shadow-lg shadow-slate-900/10 dark:shadow-none">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                     Mulai Unduh
                </button>
            </div>

            {{-- Inventory Export Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all group">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl flex items-center justify-center mb-6 text-emerald-600 dark:text-emerald-400 font-bold text-2xl group-hover:rotate-12 transition-transform">📦</div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Laporan Inventori</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8">Export data stok tersedia di seluruh cabang Anda dalam format CSV.</p>
                <button wire:click="exportInventory" class="w-full py-4 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-black rounded-2xl hover:bg-slate-800 dark:hover:bg-white transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-3 active:scale-95 shadow-lg shadow-slate-900/10 dark:shadow-none">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                     Mulai Unduh
                </button>
            </div>
        </div>
    </div>
</div>
