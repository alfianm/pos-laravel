<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ChainPOS - Premium Multi-Branch POS & CRM</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            h1, h2, h3, h4, .font-display { font-family: 'Outfit', sans-serif; }
            .glass-morphism {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body class="h-full bg-slate-950 text-white antialiased overflow-x-hidden">
        <!-- Background Elements -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/20 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-600/20 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <header class="w-full max-w-7xl mx-auto px-6 lg:px-12 h-24 flex items-center justify-between">
                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tighter uppercase font-display">
                        Chain<span class="text-indigo-400">POS</span>
                    </span>
                </div>

                <nav class="flex items-center gap-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-black uppercase tracking-widest hover:text-indigo-400 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-black uppercase tracking-widest hover:text-indigo-400 transition-colors">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-slate-950 rounded-full text-sm font-black uppercase tracking-widest hover:bg-indigo-50 transition-all shadow-xl shadow-white/10 active:scale-95">Daftar</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </header>

            <!-- Hero Section -->
            <main class="flex-1 flex flex-col items-center justify-center px-6 text-center max-w-5xl mx-auto py-20 lg:py-32">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-full mb-8 backdrop-blur-md animate-bounce">
                    <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">New: Multi-Branch & CRM Integrated</span>
                </div>

                <h1 class="text-6xl md:text-8xl font-black tracking-tight font-display mb-8">
                    Kelola Bisnis Anda <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-500">Lebih Cerdas & Cepat.</span>
                </h1>

                <p class="text-xl md:text-2xl text-slate-400 font-medium max-w-3xl mb-12 leading-relaxed">
                    Sistem Kasir Multi-Cabang Tercanggih untuk Skala Bisnis Anda. 
                    Dilengkapi dengan CRM, Manajemen Inventaris, dan Laporan Real-time dalam satu Dashboard.
                </p>

                <div class="flex flex-col sm:flex-row gap-6 items-center">
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2rem] text-lg font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-600/40 hover:scale-105 active:scale-95 transition-all">
                        Mulai Sekarang
                    </a>
                    <a href="#features" class="flex items-center gap-3 text-lg font-bold hover:text-indigo-400 transition-colors">
                        Pelajari Selengkapnya
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                </div>

                <!-- Stats Preview -->
                <div class="mt-24 grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-16">
                    <div>
                        <div class="text-4xl font-black font-display mb-2">100+</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tenants Kita</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black font-display mb-2">500+</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-500">Cabang Aktif</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black font-display mb-2">99.9%</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-500">Uptime System</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black font-display mb-2">24/7</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-500">Support Ahli</div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-12 border-t border-white/5 bg-slate-950/50 relative z-10 mt-auto">
                <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                    <div class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.2em]">
                        &copy; 2026 ChainPOS Team. All rights reserved.
                    </div>
                    
                    <div class="flex items-center gap-8">
                        <a href="#" class="text-slate-500 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest">Privacy Policy</a>
                        <a href="#" class="text-slate-500 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest">Terms of Service</a>
                        <a href="#" class="text-slate-500 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest">Contact Us</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
