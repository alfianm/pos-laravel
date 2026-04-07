<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();
        \App\Services\AuditLogService::login();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Header -->
    <div class="mb-10">
        <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight font-display mb-3">Welcome back!</h2>
        <p class="text-gray-500 font-medium leading-relaxed">
            Simplify your workflow and boost your productivity with <span class="font-bold text-gray-900">POS PIS
                PUS</span>. Get started for free.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <div class="relative group">
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus
                    autocomplete="username" placeholder="Username or Email"
                    class="w-full bg-white border-gray-200 rounded-full py-4 px-6 text-sm font-medium text-gray-900 focus:ring-4 focus:ring-gray-100 focus:border-gray-900 transition-all border placeholder:text-gray-400">
            </div>
            @if($errors->has('form.email'))
                <span
                    class="text-rose-500 text-xs font-bold mt-2 ml-4 block tracking-tight">{{ $errors->first('form.email') }}</span>
            @endif
        </div>

        <!-- Password -->
        <div>
            <div class="relative group">
                <input wire:model="form.password" id="password" type="password" name="password" required
                    autocomplete="current-password" placeholder="Password"
                    class="w-full bg-white border-gray-200 rounded-full py-4 px-6 text-sm font-medium text-gray-900 focus:ring-4 focus:ring-gray-100 focus:border-gray-900 transition-all border placeholder:text-gray-400">
                <button type="button" class="absolute inset-y-0 right-0 pr-6 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
            </div>
            @if($errors->has('form.password'))
                <span
                    class="text-rose-500 text-xs font-bold mt-2 ml-4 block tracking-tight">{{ $errors->first('form.password') }}</span>
            @endif
            <div class="flex justify-end mt-2">
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-gray-900 hover:underline" href="{{ route('password.request') }}"
                        wire:navigate>
                        Forgot Password?
                    </a>
                @endif
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" wire:loading.attr="disabled" wire:target="login"
                class="w-full py-4 bg-black hover:bg-gray-800 disabled:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-80 text-white rounded-full font-bold text-sm transition-all shadow-lg active:scale-95 group flex items-center justify-center gap-3">
                <span wire:loading.remove wire:target="login">Login</span>
                <span wire:loading wire:target="login">Signing in...</span>
                <svg wire:loading wire:target="login" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"
                    aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                </svg>
            </button>
        </div>

        <!-- Register Link -->
        <div class="mt-8 text-center text-sm">
            <span class="text-gray-500 font-medium tracking-tight">Don't have an account?</span>
            <a href="{{ route('register') }}" wire:navigate
                class="text-gray-900 font-extrabold hover:underline ml-1 tracking-tight">
                Register now
            </a>
        </div>
    </form>
</div>