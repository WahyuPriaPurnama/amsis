<?php

namespace App\Http\Controllers;

use App\Models\HRD\Subsidiary;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::with('permissions')->orderBy('name', 'asc')->get(),
            'permissions' => Permission::orderBy('name', 'asc')->get(),
            'users' => User::with('roles')->orderBy('name', 'asc')->get(),
            'subsidiaries'=>Subsidiary::orderBy('name','asc')->get(),
        ]);
    }

    public function storeRole(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles']);
        Role::create(['name' => $request->name]);
        return back()->with('alert', 'Role created.');
    }

    public function storePermission(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions']);
        Permission::create(['name' => $request->name]);
        return back()->with('alert', 'Permission created.');
    }

    public function assignPermissionToRole(Request $request)
    {
        $role = Role::findByName($request->role);
        $role->givePermissionTo($request->permission);
        return back()->with('alert', 'Permission assigned to role.');
    }

    public function assignRoleToUser(Request $request)
    {
        $user = User::find($request->user_id);
        $user->syncRoles($request->role);
        return back()->with('alert', 'Role assigned to user.');
    }
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        // Sync permissions
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('alert', 'Role updated and permissions synced.');
    }
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Optional: prevent deleting protected roles
        if (in_array($role->name, ['super-admin', 'employee'])) {
            return back()->with('alert2', 'Role ini tidak bisa dihapus.');
        }

        $role->delete();

        return back()->with('alert', 'Role berhasil dihapus.');
    }

    public function assignSubsidiary(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'subsidiaries' => 'required|array',
            'subsidiaries.*' => 'exists:subsidiaries,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        // Sinkronisasi pivot role_subsidiary
        $role->subsidiaries()->sync($request->subsidiaries);

        return back()->with('success', 'Subsidiaries assigned to role successfully.');
    }
}
