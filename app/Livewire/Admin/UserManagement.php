<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('User Management')]
class UserManagement extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    // Toggle Tampilan Form
    public bool $showCreateForm = false;
    public bool $showChangePasswordForm = false;

    // Form Tambah User
    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Form Edit User (Inline)
    public ?int $editingUserId = null;
    public string $edit_name = '';
    public string $edit_email = '';
    public string $edit_role = '';
    public string $edit_password = '';
    public string $edit_password_confirmation = '';

    // Form Ganti Password (User Akun Sendiri)
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleCreateForm()
    {
        $this->cancelEdit();
        $this->showChangePasswordForm = false;
        $this->showCreateForm = !$this->showCreateForm;
    }

    public function toggleChangePasswordForm()
    {
        $this->cancelEdit();
        $this->showCreateForm = false;
        $this->showChangePasswordForm = !$this->showChangePasswordForm;
    }

    public function updateOwnPassword()
    {
        $this->validate([
            'current_password'       => ['required', 'current_password'],
            'new_password'            => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required'         => 'Kata sandi lama wajib diisi.',
            'current_password.current_password' => 'Kata sandi lama tidak cocok.',
            'new_password.required'             => 'Kata sandi baru wajib diisi.',
            'new_password.confirmed'            => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation', 'showChangePasswordForm']);
        $this->resetValidation();

        session()->flash('success', 'Kata sandi Anda berhasil diperbarui!');
    }

    public function storeUser()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|max:255|unique:users,email',
            'role'     => 'required|exists:roles,name',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Username wajib diisi.',
            'email.unique'      => 'Username sudah digunakan.',
            'role.required'     => 'Silakan pilih level role.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->assignRole($this->role);

        $this->reset(['name', 'email', 'role', 'password', 'password_confirmation', 'showCreateForm']);
        $this->resetValidation();

        session()->flash('success', 'User berhasil ditambahkan!');
    }

    public function editUser($id)
    {
        $this->resetValidation();
        $this->showCreateForm = false;
        $this->showChangePasswordForm = false;

        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->edit_name     = $user->name;
        $this->edit_email    = $user->email;
        $this->edit_role     = $user->roles->pluck('name')->first() ?? '';
        $this->edit_password = '';
        $this->edit_password_confirmation = '';
    }

    public function cancelEdit()
    {
        $this->reset(['editingUserId', 'edit_name', 'edit_email', 'edit_role', 'edit_password', 'edit_password_confirmation']);
        $this->resetValidation();
    }

    public function updateUser()
    {
        if (!$this->editingUserId) return;

        $user = User::findOrFail($this->editingUserId);

        $rules = [
            'edit_name'  => 'required|string|max:255',
            'edit_email' => 'required|string|max:255|unique:users,email,' . $user->id,
            'edit_role'  => 'required|exists:roles,name',
        ];

        if (!empty($this->edit_password)) {
            $rules['edit_password'] = 'string|min:6|confirmed';
        }

        $this->validate($rules, [
            'edit_name.required'     => 'Nama lengkap wajib diisi.',
            'edit_email.required'    => 'Username wajib diisi.',
            'edit_email.unique'      => 'Username sudah digunakan.',
            'edit_role.required'     => 'Silakan pilih level role.',
            'edit_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $data = [
            'name'  => $this->edit_name,
            'email' => $this->edit_email,
        ];

        if (!empty($this->edit_password)) {
            $data['password'] = Hash::make($this->edit_password);
        }

        $user->update($data);
        $user->syncRoles([$this->edit_role]);

        $this->cancelEdit();
        session()->flash('success', 'Data user berhasil diperbarui!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        $hasRO  = \Illuminate\Support\Facades\DB::table('request_orders')->where('requested_by', $user->id)->exists();
        $hasRFP = \Illuminate\Support\Facades\DB::table('request_for_payments')->where('requested_by', $user->id)->exists();

        if ($hasRO || $hasRFP) {
            session()->flash('error', "User {$user->name} tidak dapat dihapus karena memiliki riwayat transaksi RO / RFP.");
            return;
        }

        $user->delete();

        if ($this->editingUserId === $id) {
            $this->cancelEdit();
        }

        session()->flash('success', "User {$user->name} berhasil dihapus!");
    }

    public function render()
    {
        // Jika rute yang diakses adalah halaman Ganti Password Mandiri
        if (request()->routeIs('password.change')) {
            return view('livewire.auth.change-password');
        }

        // Default tampilan User Management (Admin)
        $users = User::with(['roles', 'subsidiary'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('auth.userlist', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
