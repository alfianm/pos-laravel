<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            h1, h2, h3, h4, .font-display { font-family: 'Outfit', sans-serif; }
        </style>
    </head>
    <body class="bg-white text-slate-900 antialiased overflow-hidden h-screen">
        <div class="flex h-screen">
            <!-- Left Column: Login Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 lg:px-24 xl:px-32 bg-white relative">
                <div class="max-w-md w-full mx-auto">
                    {{ $slot }}
                </div>
            </div>

            <!-- Right Column: Marketing Slider -->
            <div class="hidden lg:flex lg:w-1/2 p-8 h-full">
                <div x-data="{ 
                        activeSlide: 0, 
                        slides: [
                            { 
                                image: '{{ asset('assets/images/slider-1.png') }}', 
                                title: 'Monitor Penjualan <span class=\'text-indigo-600\'>Real-time</span>',
                                desc: 'Pantau grafik penjualan dan performa toko Anda dari mana saja dengan dashboard interaktif.'
                            },
                            { 
                                image: '{{ asset('assets/images/slider-2.png') }}', 
                                title: 'Manajemen Stok <span class=\'text-amber-600\'>Presisi</span>',
                                desc: 'Kontrol inventaris antar cabang dengan akurat dan efisien menggunakan sistem mutasi otomatis.'
                            },
                            { 
                                image: '{{ asset('assets/images/slider-3.png') }}', 
                                title: 'Transaksi POS <span class=\'text-emerald-600\'>Cepat & Aman</span>',
                                desc: 'Proses checkout kilat dengan berbagai metode pembayaran dan cetak struk instan.'
                            }
                        ],
                        init() {
                            setInterval(() => {
                                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                            }, 5000);
                        }
                    }"
                    class="w-full h-full bg-slate-50 border border-slate-100 dark:bg-gray-900 dark:border-gray-800 rounded-[3rem] flex flex-col items-center justify-center p-12 relative overflow-hidden transition-colors duration-500">
                    
                    {{-- Animated Background Blobs --}}
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500/5 rounded-full blur-[100px] animate-pulse"></div>
                    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-500/5 rounded-full blur-[100px] animate-pulse"></div>

                    <!-- Slide Content Wrapper -->
                    <div class="relative w-full flex-1 overflow-visible">
                        @foreach([0,1,2] as $index)
                        <div x-show="activeSlide === {{ $index }}"
                            x-transition:enter="transition ease-out duration-1000"
                            x-transition:enter-start="opacity-0 translate-x-32 scale-95"
                            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-x-32 scale-95"
                            class="absolute inset-0 flex flex-col items-center justify-center p-12"
                            x-cloak>
                            
                            {{-- Illustration Container (Enlarged) --}}
                            <div class="relative w-full max-w-[32rem] mb-12 group">
                                <div class="absolute inset-0 bg-indigo-500/10 dark:bg-white/5 rounded-[3.5rem] transform translate-y-6 translate-x-6 transition-transform group-hover:translate-x-8 group-hover:translate-y-8"></div>
                                <div class="relative aspect-[16/10] rounded-[3.5rem] overflow-hidden border-[6px] border-white dark:border-gray-800 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] z-10 transition-transform group-hover:scale-[1.02] duration-700">
                                    <img :src="slides[{{ $index }}].image" 
                                         :alt="slides[{{ $index }}].title" 
                                         class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Decorative Badge -->
                                <div class="absolute -top-6 -right-6 bg-white dark:bg-gray-800 px-6 py-4 rounded-[1.5rem] shadow-2xl z-20 border border-slate-100 dark:border-gray-700 animate-bounce">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
                                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-700 dark:text-slate-300">System Live</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide Text -->
                            <div class="text-center px-4 max-w-xl">
                                <h2 class="text-4xl xl:text-5xl font-black text-slate-900 dark:text-white leading-tight mb-6 tracking-tight" x-html="slides[{{ $index }}].title"></h2>
                                <p class="text-slate-500 dark:text-gray-400 text-base xl:text-lg leading-relaxed opacity-90 font-medium" x-text="slides[{{ $index }}].desc"></p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Slide Navigation Dots (Fixed Bottom) -->
                    <div class="flex gap-4 pb-16 relative z-20">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="activeSlide = index"
                                :class="activeSlide === index ? 'w-12 bg-indigo-600' : 'w-3 bg-slate-200 dark:bg-gray-700'"
                                class="h-3 rounded-full transition-all duration-500 focus:outline-none hover:bg-indigo-400 shadow-sm"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>