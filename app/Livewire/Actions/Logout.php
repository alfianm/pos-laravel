<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        \App\Services\AuditLogService::log('logout', Auth::user(), [], [], 'auth');

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
