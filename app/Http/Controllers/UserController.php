<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.userlist', [
            'roles' => Role::with('permissions')->orderBy('name', 'asc')->get(),
            'users' => User::with('roles')->orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|string',
            'role' => 'required|string|max:25',
            'email' => 'required|email',
            'password' => 'min:8|nullable|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : null,
        ]);

        return redirect()->route('users.index')->with('alert', "input data {$validated['name']} berhasil");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('auth.userlist', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|string',
            'roles' => 'required|string|max:25', // kalau hanya satu role
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'min:8|nullable|confirmed'
        ]);

        $updateData = [
            'name' => ucwords(strtolower($validated['name'])),
            'email' => $validated['email'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // update data user
        $user->update($updateData);

        // sinkronisasi role dengan Spatie
        $user->syncRoles([$validated['roles']]);

        return redirect()->route('users.index')
            ->with('alert', 'Update data ' . e($validated['name']) . ' berhasil');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('alert', 'User ' . e($user->name) . ' berhasil dihapus');
    }

    public function export()
    {

        return Excel::download(new UsersExport, 'amsis-users ' . now() . '.xlsx');
    }

    public function editPassword()
    {
        return view('employees.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::find(auth()->id());

        if (!Hash::check($validated['current_password'], $user->password)) {
            // Tambahkan error ke session tanpa menimpa error validasi
            return back()->withErrors([
                'current_password' => 'Password lama tidak cocok'
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employees.show', $user->employee_id)
            ->with('alert', 'Password berhasil diubah.');
    }
}
