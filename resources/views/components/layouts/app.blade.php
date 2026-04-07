<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ sidebarOpen: false }"
      class="h-full scroll-smooth bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ChainPOS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="h-full overflow-hidden text-gray-900 bg-gray-50 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 bg-gray-50 relative overflow-hidden">
                <header class="bg-white border-b border-gray-200 z-30 shrink-0 h-16 flex items-center px-6">
                    <div class="flex w-full items-center justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-900 p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>

                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 text-sm">Welcome, </span>
                                <span class="text-gray-900 font-semibold text-sm">{{ auth()->user()->name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Search -->
                            <div class="relative hidden lg:block">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                                </div>
                                <input type="text" placeholder="Search anything" class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                            </div>

                            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Notifications">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.003 6.003 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.671 6.165 6 8.387 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path></svg>
                                <span class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            </button>

                            <!-- Notes/Orders icon -->
                            <button class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </button>

                            <!-- Date Label -->
                            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm font-medium text-gray-700">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ now()->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto">
                    <div class="h-full p-4 sm:p-6 lg:p-6">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        <script>
            window.formatRupiah = function(value) {
                if (!value) return '';
                value = value.toString().replace(/[^0-9]/g, '');
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            };

            window.parseRupiah = function(value) {
                if (!value) return 0;
                return parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;
            };

            document.addEventListener('alpine:init', () => {
                Alpine.data('moneyMask', (modelName) => ({
                    displayValue: '',
                    init() {
                        this.displayValue = window.formatRupiah(this.$wire.get(modelName));
                        this.$watch('displayValue', (val) => {
                            let numeric = window.parseRupiah(val);
                            this.$wire.set(modelName, numeric);
                            this.displayValue = window.formatRupiah(numeric);
                        });
                    }
                }));

                // Simple directive for input masking
                Alpine.directive('money', (el, { expression }, { evaluate }) => {
                    el.addEventListener('input', (e) => {
                        let cursor = e.target.selectionStart;
                        let oldLen = e.target.value.length;
                        let numeric = e.target.value.replace(/[^0-9]/g, '');
                        if (numeric === '') {
                            e.target.value = '';
                            return;
                        }
                        let formatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(numeric);
                        
                        e.target.value = formatted;
                        
                        // Adjust cursor
                        let newLen = formatted.length;
                        e.target.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));
                    });
                });
            });
        </script>
        
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('swal', (event) => {
                    const data = event[0];
                    Swal.fire({
                        title: data.title || (data.type === 'success' ? 'Success!' : 'Error!'),
                        text: data.text || '',
                        icon: data.type || 'info',
                        confirmButtonText: data.confirmButtonText || 'OK',
                        confirmButtonColor: '#4f46e5', // Indigo-600
                        customClass: {
                            popup: 'rounded-[2rem] dark:bg-gray-800 dark:text-white border-0',
                            title: 'font-black tracking-tight',
                            confirmButton: 'rounded-xl px-10 py-3 font-black uppercase tracking-widest text-xs',
                        }
                    });
                });

                Livewire.on('message', (message) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: Array.isArray(message) ? message[0] : message,
                        customClass: {
                            popup: 'rounded-2xl dark:bg-gray-800 dark:text-white',
                        }
                    });
                });
            });
        </script>
    </body>
</html>
