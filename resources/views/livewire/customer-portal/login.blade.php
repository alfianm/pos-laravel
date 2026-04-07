<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 dark:bg-slate-900 px-4">
    <div class="mb-10 text-center animate-fade-in">
        <div class="w-16 h-16 bg-indigo-600 rounded-3xl shadow-2xl shadow-indigo-600/30 mx-auto flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 118 0v4M5 9h14l1 12H4L5 9z"></path></svg> 
        </div>
        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Customer Portal</h2>
        <p class="text-slate-500 dark:text-slate-400 font-medium italic mt-1">Akses riwayat pesanan dan profil Anda dengan mudah.</p>
    </div>

    <div class="w-full sm:max-w-md bg-white dark:bg-slate-800 shadow-2xl rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50 p-8 sm:p-12 overflow-hidden relative">
        <form wire:submit.prevent="login" class="space-y-8 relative z-10">
            @if(session()->has('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 text-xs font-black rounded-2xl border border-rose-100 dark:border-rose-900/50 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Email Anda</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <input type="email" wire:model.defer="email" class="block w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="yourname@email.com">
                </div>
                @error('email') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Kata Sandi</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    <input type="password" wire:model.defer="password" class="block w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-8 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white font-bold" placeholder="••••••••">
                </div>
                @error('password') <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-3 group cursor-pointer">
                    <input type="checkbox" wire:model="remember" class="w-5 h-5 rounded-lg border-2 border-slate-200 text-indigo-600 focus:ring-0 transition-all">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 transition-colors">Tetap Masuk</span>
                </label>
                <a href="#" class="text-[10px] font-black text-slate-400 hover:text-indigo-600 dark:hover:text-white uppercase tracking-widest transition-colors">Lupa Password?</a>
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full py-5 bg-indigo-600 text-white font-black rounded-[2rem] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs flex justify-center items-center gap-3 disabled:opacity-50">
                <span wire:loading.remove>Masuk ke Portal</span>
                <span wire:loading>Memverifikasi...</span>
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1m0-11V4"></path></svg>
            </button>
        </form>
    </div>
    
    <p class="mt-12 text-center text-slate-400 font-medium text-xs">Akses portal disediakan eksklusif oleh <span class="text-slate-900 dark:text-white font-black uppercase">Your POS System</span></p>
</div>
