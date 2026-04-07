<?php

namespace App\Livewire\Roles;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission;
use App\Models\Role;

#[Layout('layouts.app')]
class RoleIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $showPermissionsModal = false;

    public $editId = null;

    public $name = '';

    public $selectedPermissions = [];

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,'.$this->editId,
        ];
    }

    public function updatingSearch()
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
        $this->editId = $id;
        $role = Role::findOrFail($id);
        $this->name = $role->name;
        $this->showModal = true;
    }

    public function openPermissionsModal($id)
    {
        $role = Role::findOrFail($id);
        $this->editId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showPermissionsModal = true;
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->selectedPermissions = [];
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closePermissionsModal()
    {
        $this->showPermissionsModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            $role = Role::findOrFail($this->editId);
            $role->update(['name' => $this->name]);
            session()->flash('message', 'Role berhasil diperbarui.');
        } else {
            Role::create(['name' => $this->name, 'guard_name' => 'web']);
            session()->flash('message', 'Role berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function savePermissions()
    {
        $role = Role::findOrFail($this->editId);
        $role->syncPermissions($this->selectedPermissions);
        session()->flash('message', 'Permissions berhasil diperbarui.');
        $this->closePermissionsModal();
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['super_admin'])) {
            session()->flash('error', 'Role super_admin tidak dapat dihapus.');

            return;
        }

        $role->delete();
        session()->flash('message', 'Role berhasil dihapus.');
    }

    public function getPermissionGroups()
    {
        $permissions = Permission::all();

        $groups = [];
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $group = $parts[1] ?? 'other';
            if (! isset($groups[$group])) {
                $groups[$group] = [];
            }
            $groups[$group][] = $permission;
        }

        return $groups;
    }

    public function render()
    {
        $roles = Role::with(['permissions', 'users'])
            ->when($this->search, function ($query) {
                $query->where('name', 'ilike', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.roles.role-index', [
            'roles' => $roles,
        ]);
    }
}
