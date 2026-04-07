<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-4xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent italic">
                Platform Gateway Config
            </h1>
            <p class="text-slate-400">Manage payment gateway credentials and environment settings.</p>
        </header>

        @if (session()->has('success'))
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 5000)"
                class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 rounded-xl flex items-center shadow-lg shadow-emerald-500/10"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar Providers -->
            <div class="md:col-span-1 space-y-3">
                <button 
                    wire:click="switchProvider('xendit')"
                    class="w-full text-left p-4 rounded-xl transition-all duration-200 border-2 {{ $selectedProvider === 'xendit' ? 'bg-blue-600 border-blue-400 shadow-lg shadow-blue-600/20' : 'bg-slate-800 border-slate-700 hover:bg-slate-700' }}"
                >
                    <div class="font-semibold">Xendit</div>
                    <div class="text-xs {{ $selectedProvider === 'xendit' ? 'text-blue-100' : 'text-slate-500' }}">Indonesia & SE Asia</div>
                </button>
                
                <button 
                    wire:click="switchProvider('midtrans')"
                    class="w-full text-left p-4 rounded-xl transition-all duration-200 border-2 {{ $selectedProvider === 'midtrans' ? 'bg-indigo-600 border-indigo-400 shadow-lg shadow-indigo-600/20' : 'bg-slate-800 border-slate-700 hover:bg-slate-700' }}"
                >
                    <div class="font-semibold">Midtrans</div>
                    <div class="text-xs {{ $selectedProvider === 'midtrans' ? 'text-indigo-100' : 'text-slate-500' }}">Gojek/Tokopedia Group</div>
                </button>
            </div>

            <!-- Configuration Form -->
            <div class="md:col-span-3 bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-24 h-24 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </div>

                <form wire:submit.prevent="save" class="space-y-6 relative z-10">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-700 pb-4">
                        <h2 class="text-2xl font-bold flex items-center">
                            <span class="w-2 h-8 bg-blue-500 rounded-full mr-3"></span>
                            {{ $name }}
                        </h2>
                        <div class="flex items-center space-x-3 bg-slate-900 rounded-full p-1 pl-4">
                             <span class="text-xs font-bold uppercase tracking-wider {{ $isTestMode ? 'text-amber-400' : 'text-emerald-400' }}">
                                {{ $isTestMode ? 'Sandbox' : 'Production' }}
                            </span>
                            <button 
                                type="button" 
                                wire:click="$toggle('isTestMode')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $isTestMode ? 'bg-amber-600' : 'bg-emerald-600' }}"
                            >
                                <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isTestMode ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-slate-400 mb-2 group-focus-within:text-blue-400 transition-colors">Display Name</label>
                            <input type="text" wire:model="name" class="w-full bg-slate-900 border-2 border-slate-700 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 transition-all duration-200">
                            @error('name') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-slate-400 mb-2 group-focus-within:text-blue-400 transition-colors">Secret Key</label>
                            <div class="relative">
                                <input type="password" wire:model="secretKey" placeholder="xnd_development_..." class="w-full bg-slate-900 border-2 border-slate-700 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 font-mono transition-all duration-200">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                            </div>
                            @error('secretKey') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-slate-400 mb-2 group-focus-within:text-blue-400 transition-colors">Public Key</label>
                            <input type="text" wire:model="publicKey" placeholder="Optional" class="w-full bg-slate-900 border-2 border-slate-700 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 font-mono transition-all duration-200">
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-slate-400 mb-2 group-focus-within:text-blue-400 transition-colors">Webhook Secret / Callback Token</label>
                            <input type="password" wire:model="webhookSecret" class="w-full bg-slate-900 border-2 border-slate-700 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 font-mono transition-all duration-200">
                            <div class="mt-2 flex items-center bg-slate-900/50 p-2 px-3 rounded-lg border border-slate-700/50">
                                <span class="text-[10px] uppercase font-bold text-slate-500 mr-2">Endpoint URL</span>
                                <span class="text-xs text-blue-400 font-mono truncate select-all">{{ url('/api/webhook/' . $selectedProvider) }}</span>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center bg-slate-900/30 p-4 rounded-xl border border-slate-700/50">
                            <button 
                                type="button" 
                                wire:click="$toggle('isActive')"
                                class="mr-4 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $isActive ? 'bg-emerald-600' : 'bg-slate-600' }}"
                            >
                                <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <div>
                                <span class="block text-sm font-bold text-slate-200">Enable Gateway</span>
                                <span class="block text-xs text-slate-500">Enable this gateway for platform-wide SaaS subscription payments</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-700 flex justify-end">
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-200 shadow-xl shadow-blue-600/40 flex items-center"
                        >
                            <span wire:loading.remove>Save Changes</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
