<?php

namespace App\Livewire\CustomerPortal;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::guard('customer')->attempt([
            'email' => $this->email,
            'password' => $this->password,
            'portal_active' => true
        ], $this->remember)) {
            
            $customer = Auth::guard('customer')->user();
            $customer->update(['last_portal_login_at' => now()]);

            return redirect()->route('customer.dashboard');
        }

        session()->flash('error', 'Login gagal. Silakan periksa email/password atau status akun Anda.');
    }

    public function render()
    {
        return view('livewire.customer-portal.login');
    }
}
