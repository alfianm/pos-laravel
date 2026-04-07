<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Branch;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserForm extends Component
{
    public $userId = null;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $role = 'staff';
    public $selected_branches = [];
    public $active_branch_id;
    public $is_super_admin = false;

    public $isEdit = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'phone' => 'nullable|string|max:20',
            'password' => $this->isEdit ? 'nullable|min:8' : 'required|min:8',
            'role' => 'required|string',
            'selected_branches' => 'nullable|array',
            'active_branch_id' => 'nullable|uuid|exists:branches,id',
        ];
    }

    public function mount($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
            $this->isEdit = true;
            $user = User::findOrFail($userId);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->role = $user->roles->first()?->name ?? 'staff';
            $this->selected_branches = $user->branches->pluck('id')->toArray();
            $this->active_branch_id = $user->active_branch_id;
            $this->is_super_admin = $user->is_super_admin;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'active_branch_id' => $this->active_branch_id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            session()->flash('success', 'User berhasil diperbarui!');
        } else {
            $user = User::create($data);
            session()->flash('success', 'User berhasil dibuat!');
        }

        // Sync Roles
        $user->syncRoles($this->role);

        // Sync Branches (The UI for Assignment)
        $user->branches()->sync($this->selected_branches);

        return redirect()->route('settings.users.index');
    }

    public function render()
    {
        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $roles = Role::where('name', '!=', 'super_admin')->get();

        return view('livewire.user.user-form', [
            'branches' => $branches,
            'roles' => $roles
        ]);
    }
}
