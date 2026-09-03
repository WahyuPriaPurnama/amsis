<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\HRD\Subsidiary;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
#[Title('Roles & Permissions')]
class RoleManagement extends Component
{
    // Properti Create Role
    #[Validate(['required', 'string', 'unique:roles,name'], message: [
        'required' => 'Nama role tidak boleh kosong.',
        'unique' => 'Nama role sudah ada.'
    ])]
    public string $name = '';

    // Properti Assign Role
    public string $user_id = '';
    public string $role = '';

    // Properti Edit Role (Inline)
    public ?Role $editingRole = null;
    public string $edit_name = '';
    public array $selectedPermissions = [];

    public function storeRole()
    {
        $this->validateOnly('name');

        Role::create(['name' => $this->name]);

        $this->reset('name');
        session()->flash('success', 'Role berhasil dibuat!');
    }

    public function assignRole()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($this->user_id);
        $user->syncRoles([$this->role]);

        $this->reset(['user_id', 'role']);
        session()->flash('success', "Role {$this->role} berhasil diberikan ke user {$user->name}!");
    }

    // Trigger saat tombol Edit diklik
    public function editRole($id)
    {
        $this->editingRole = Role::with('permissions')->findOrFail($id);
        $this->edit_name = $this->editingRole->name;
        $this->selectedPermissions = $this->editingRole->permissions->pluck('name')->toArray();
    }

    public function cancelEdit()
    {
        $this->reset(['editingRole', 'edit_name', 'selectedPermissions']);
    }

    public function updateRole()
    {
        $this->validate([
            'edit_name' => 'required|string|unique:roles,name,' . $this->editingRole->id,
            'selectedPermissions' => 'nullable|array',
        ], [
            'edit_name.required' => 'Nama role tidak boleh kosong.',
            'edit_name.unique' => 'Nama role sudah digunakan.',
        ]);

        $this->editingRole->update(['name' => $this->edit_name]);
        $this->editingRole->syncPermissions($this->selectedPermissions);

        $this->cancelEdit();
        session()->flash('success', 'Role berhasil diperbarui!');
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        session()->flash('success', "Role {$role->name} berhasil dihapus!");
    }

    public function render()
    {
        return view('livewire.admin.role-management', [
            'roles' => Role::with(['permissions', 'subsidiaries'])->get(),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
            'subsidiaries' => Subsidiary::select('id', 'name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }
}
