<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceAccount;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AccountForm extends Component
{
    public $accountId;

    public $marketplace = 'shopee';

    public $name;

    public $api_key;

    public $api_secret;

    public $access_token;

    public $refresh_token;

    public $isEdit = false;

    protected function rules()
    {
        return [
            'marketplace' => 'required|string|in:shopee,tokopedia,lazada,bukalapak,blibli',
            'name' => 'required|string|max:150',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string',
            'access_token' => 'nullable|string',
            'refresh_token' => 'nullable|string',
        ];
    }

    public function mount($accountId = null)
    {
        if ($accountId) {
            $this->accountId = $accountId;
            $account = MarketplaceAccount::findOrFail($accountId);
            $this->marketplace = $account->marketplace;
            $this->name = $account->name;
            $this->api_key = $account->api_key;
            $this->api_secret = $account->api_secret;
            $this->access_token = $account->access_token;
            $this->refresh_token = $account->refresh_token;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'marketplace' => $this->marketplace,
            'name' => $this->name,
            'api_key' => $this->api_key ?: null,
            'api_secret' => $this->api_secret ?: null,
            'access_token' => $this->access_token ?: null,
            'refresh_token' => $this->refresh_token ?: null,
            'status' => 'active',
        ];

        if ($this->isEdit) {
            $account = MarketplaceAccount::findOrFail($this->accountId);
            $account->update($data);
            session()->flash('message', 'Akun Marketplace berhasil diperbarui.');
        } else {
            MarketplaceAccount::create($data);
            session()->flash('message', 'Akun Marketplace berhasil ditambahkan.');
        }

        return redirect()->route('omnichannel.accounts.index');
    }

    public function getPlatformOptions()
    {
        return [
            'shopee' => [
                'name' => 'Shopee', 
                'color' => 'orange',
                'logo' => asset('assets/marketplace/shopee.png')
            ],
            'tokopedia' => [
                'name' => 'Tokopedia', 
                'color' => 'emerald',
                'logo' => asset('assets/marketplace/tokopedia.png')
            ],
            'lazada' => [
                'name' => 'Lazada', 
                'color' => 'blue',
                'logo' => asset('assets/marketplace/lazada.png')
            ],
            'bukalapak' => [
                'name' => 'Bukalapak', 
                'color' => 'rose',
                'logo' => asset('assets/marketplace/bukalapak.png')
            ],
            'blibli' => [
                'name' => 'Blibli', 
                'color' => 'indigo',
                'logo' => asset('assets/marketplace/blibli.png')
            ],
        ];
    }

    public function render()
    {
        return view('livewire.marketplace.account-form', [
            'platformOptions' => $this->getPlatformOptions(),
        ]);
    }
}
