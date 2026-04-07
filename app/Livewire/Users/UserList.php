<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.users.user-list', [
            'users' => User::with(['tenant', 'roles'])->latest()->paginate(10),
        ]);
    }
}
