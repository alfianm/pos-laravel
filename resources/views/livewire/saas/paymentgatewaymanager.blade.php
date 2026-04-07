<?php

use App\Models\PaymentGatewayConfig;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public $configs = [];
    public $selectedProvider = 'xendit';
    
    // Form fields for current selected provider
    public $name = 'Xendit';
    public $secretKey = '';
    public $publicKey = '';
    public $webhookSecret = '';
    public $isActive = false;
    public $isTestMode = true;

    public function mount()
    {
        $this->loadConfigs();
        $this->switchProvider('xendit');
    }

    public function loadConfigs()
    {
        $this->configs = PaymentGatewayConfig::all()->keyBy('provider')->toArray();
    }

    public function switchProvider($provider)
    {
        $this->selectedProvider = $provider;
        $config = PaymentGatewayConfig::where('provider', $provider)->first();

        if ($config) {
            $this->name = $config->name;
            $this->secretKey = $config->config['secret_key'] ?? '';
            $this->publicKey = $config->config['public_key'] ?? '';
            $this->webhookSecret = $config->config['webhook_secret'] ?? '';
            $this->isActive = $config->is_active;
            $this->isTestMode = $config->is_test_mode;
        } else {
            $this->resetFields();
            $this->name = ucfirst($provider);
        }
    }

    public function resetFields()
    {
        $this->secretKey = '';
        $this->publicKey = '';
        $this->webhookSecret = '';
        $this->isActive = false;
        $this->isTestMode = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'secretKey' => 'required|string',
            'publicKey' => 'nullable|string',
            'webhookSecret' => 'nullable|string',
            'isActive' => 'boolean',
            'isTestMode' => 'boolean',
        ]);

        PaymentGatewayConfig::updateOrCreate(
            ['provider' => $this->selectedProvider],
            [
                'name' => $this->name,
                'config' => [
                    'secret_key' => $this->secretKey,
                    'public_key' => $this->publicKey,
                    'webhook_secret' => $this->webhookSecret,
                ],
                'is_active' => $this->isActive,
                'is_test_mode' => $this->isTestMode,
            ]
        );

        $this->loadConfigs();
        session()->flash('success', 'Configuration saved successfully.');
    }
}; ?>

<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-4xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent italic">
                Payment Gateway Configuration
            </h1>
            <p class="text-slate-400">Configure platform-wide payment systems for subscription billing.</p>
        </header>

        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 rounded-xl flex items-center shadow-lg shadow-emerald-500/10">
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
                    class="w-full text-left p-4 rounded-xl transition-all duration-200 {{ $selectedProvider === 'xendit' ? 'bg-blue-600 shadow-lg shadow-blue-600/20' : 'bg-slate-800 hover:bg-slate-700' }}"
                >
                    <div class="font-semibold">Xendit</div>
                    <div class="text-xs {{ $selectedProvider === 'xendit' ? 'text-blue-100' : 'text-slate-500' }}">Indonesia Gateway</div>
                </button>
                
                <button 
                    wire:click="switchProvider('midtrans')"
                    class="w-full text-left p-4 rounded-xl transition-all duration-200 {{ $selectedProvider === 'midtrans' ? 'bg-blue-600 shadow-lg shadow-blue-600/20' : 'bg-slate-800 hover:bg-slate-700' }}"
                >
                    <div class="font-semibold text-slate-500">Midtrans</div>
                    <div class="text-xs text-slate-600">Coming Soon</div>
                </button>
            </div>

            <!-- Configuration Form -->
            <div class="md:col-span-3 bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-2xl">
                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-700 pb-4">
                        <h2 class="text-xl font-semibold">{{ $name }} Settings</h2>
                        <div class="flex items-center space-x-2">
                             <span class="text-sm font-medium {{ $isTestMode ? 'text-amber-400' : 'text-emerald-400' }}">
                                {{ $isTestMode ? 'Test Mode' : 'Live Mode' }}
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
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Display Name</label>
                            <input type="text" wire:model="name" class="w-full bg-slate-900 border-slate-700 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Secret Key</label>
                            <input type="password" wire:model="secretKey" placeholder="xnd_development_..." class="w-full bg-slate-900 border-slate-700 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 font-mono">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Public Key (Optional)</label>
                            <input type="text" wire:model="publicKey" placeholder="xnd_public_development_..." class="w-full bg-slate-900 border-slate-700 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 font-mono">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Webhook Secret / Callback Token</label>
                            <input type="password" wire:model="webhookSecret" class="w-full bg-slate-900 border-slate-700 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-100 px-4 py-3 font-mono">
                            <p class="mt-2 text-xs text-slate-500 font-mono italic">Endpoint: {{ url('/api/webhook/' . $selectedProvider) }}</p>
                        </div>

                        <div class="pt-4 flex items-center">
                            <button 
                                type="button" 
                                wire:click="$toggle('isActive')"
                                class="mr-3 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $isActive ? 'bg-blue-600' : 'bg-slate-600' }}"
                            >
                                <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-sm font-medium text-slate-300">Enable this gateway for platform payments</span>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-700 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-xl transition-all duration-200 shadow-lg shadow-blue-600/30">
                            Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
