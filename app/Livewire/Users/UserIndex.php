<?php

namespace App\Livewire\Users;

use App\Models\Branch;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $role_filter = '';

    public $showModal = false;

    public $showRolesModal = false;

    public $editId = null;

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $branch_id = '';

    public $selectedRoles = [];

    protected $queryString = ['search', 'role_filter'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->editId,
            'password' => $this->editId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'selectedRoles' => 'array',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->branch_id = $user->active_branch_id;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showModal = true;
    }

    public function openRolesModal($id)
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->name = $user->name;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showRolesModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->branch_id = '';
        $this->selectedRoles = [];
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeRolesModal()
    {
        $this->showRolesModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            $user = User::findOrFail($this->editId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'active_branch_id' => $this->branch_id ?: null,
            ]);

            if ($this->password) {
                $user->update(['password' => bcrypt($this->password)]);
            }

            $user->syncRoles($this->selectedRoles);
            session()->flash('message', 'User berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'tenant_id' => auth()->user()->tenant_id,
                'active_branch_id' => $this->branch_id ?: null,
                'email_verified_at' => now(),
            ]);

            $user->assignRole($this->selectedRoles);
            session()->flash('message', 'User berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function saveRoles()
    {
        $user = User::findOrFail($this->editId);
        $user->syncRoles($this->selectedRoles);
        session()->flash('message', 'Role berhasil diperbarui.');
        $this->closeRolesModal();
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun sendiri.');

            return;
        }

        $user->delete();
        session()->flash('message', 'User berhasil dihapus.');
    }

    public function render()
    {
        $users = User::with(['roles', 'activeBranch'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%'.$this->search.'%')
                        ->orWhere('email', 'ilike', '%'.$this->search.'%');
                });
            })
            ->when($this->role_filter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->role_filter);
                });
            })
            ->orderBy('name')
            ->paginate(10);

        $roles = Role::orderBy('name')->get();
        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

        return view('livewire.users.user-index', [
            'users' => $users,
            'roles' => $roles,
            'branches' => $branches,
        ]);
    }
}
