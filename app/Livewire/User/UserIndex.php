<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function deletingUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Tidak bisa menghapus diri sendiri.');
            return;
        }

        $user->delete();
        session()->flash('success', 'User berhasil dihapus.');
    }

    public function render()
    {
        $users = User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->search, function($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('email', 'ilike', '%' . $this->search . '%');
            })
            ->with(['roles', 'activeBranch'])
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.user.user-index', [
            'users' => $users
        ]);
    }
}
