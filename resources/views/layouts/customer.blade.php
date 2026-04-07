<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Portal - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="h-full overflow-hidden text-slate-900 dark:text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-72">
                <div class="flex flex-col flex-grow bg-white dark:bg-slate-800 border-r border-slate-100 dark:border-slate-700/50 pt-10 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-8 mb-12">
                        <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 118 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <span class="ml-4 text-xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Portal</span>
                    </div>
                    
                    <nav class="flex-1 px-4 space-y-2">
                        <a href="{{ route('customer.dashboard') }}" wire:navigate class="group flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('customer.dashboard') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                            <svg class="mr-4 h-5 w-5 {{ request()->routeIs('customer.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Beranda
                        </a>
                        <a href="{{ route('customer.orders') }}" wire:navigate class="group flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('customer.orders') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                            <svg class="mr-4 h-5 w-5 {{ request()->routeIs('customer.orders') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 118 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Riwayat Belanja
                        </a>
                        <a href="{{ route('customer.profile') }}" wire:navigate class="group flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('customer.profile') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                            <svg class="mr-4 h-5 w-5 {{ request()->routeIs('customer.profile') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profil Saya
                        </a>
                    </nav>

                    <div class="px-8 mt-12">
                         <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
                              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center mb-3 leading-none">Butuh Bantuan?</p>
                              <a href="#" class="w-full py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-[10px] font-black uppercase tracking-widest text-center rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 block transition-all hover:translate-y-[-2px] active:scale-95">Hubungi Kami</a>
                         </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content Space --}}
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            {{-- Top Navbar --}}
            <header class="relative bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700/50 z-20">
                <div class="px-8 py-5 flex items-center justify-between">
                    <div class="flex-1">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] font-mono select-none">PORTAL VERSION 1.0</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <button onclick="document.getElementById('logout-form').submit()" class="p-3 text-slate-400 hover:text-rose-500 transition-colors active:scale-90">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 relative overflow-y-auto focus:outline-none bg-slate-50 dark:bg-slate-900 custom-scrollbar">
                {{ $slot }}
            </main>
        </div>
    </div>

    <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" class="hidden">@csrf</form>
</body>
</html>
