<?php

namespace App\Livewire\Saas;

use App\Models\PaymentGatewayConfig;
use Livewire\Component;

class PaymentGatewayManager extends Component
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

    protected $rules = [
        'name' => 'required|string|max:100',
        'secretKey' => 'required|string',
        'publicKey' => 'nullable|string',
        'webhookSecret' => 'nullable|string',
        'isActive' => 'boolean',
        'isTestMode' => 'boolean',
    ];

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
            $this->resetFields($provider);
        }
    }

    public function resetFields($provider)
    {
        $this->name = ucfirst($provider);
        $this->secretKey = '';
        $this->publicKey = '';
        $this->webhookSecret = '';
        $this->isActive = false;
        $this->isTestMode = true;
    }

    public function save()
    {
        $this->validate();

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
        session()->flash('success', 'Configuration for ' . $this->name . ' has been saved.');
    }

    public function render()
    {
        return view('livewire.saas.payment-gateway-manager')
            ->layout('layouts.app');
    }
}
