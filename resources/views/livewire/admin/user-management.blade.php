<div>
    <div class="container mt-3">
        @component('components.card')
        @slot('header')
        User Management
        @endslot
        <div class="d-flex justify-content-between mb-3 gap-2 flex-wrap">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" wire:click="toggleCreateForm">
                    <i class="bi bi-person-fill-add me-1"></i> {{ $showCreateForm ? 'Tutup Form' : 'Tambah User' }}
                </button>
                <button type="button" class="btn btn-warning text-dark" wire:click="toggleChangePasswordForm">
                    <i class="bi bi-key-fill me-1"></i> {{ $showChangePasswordForm ? 'Tutup Form Password' : 'Ganti Password Saya' }}
                </button>
                <x-buttons.excel href="{{ route('users.export') }}" class="btn btn-success">
                </x-buttons.excel>
            </div>

            {{-- Live Search Input --}}
            <div class="input-group" style="max-width: 300px;">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama / username...">
                <button class="btn btn-primary" type="button">Cari</button>
            </div>
        </div>

        {{-- Form Inline Ganti Password Akun Sendiri --}}
        @if ($showChangePasswordForm)
        <div class="card card-body bg-light mb-4 border-warning">
            <h5 class="mb-3">🔑 Ganti Password Akun Saya</h5>
            <form wire:submit="updateOwnPassword">
                <div class="row g-3">
                    <div class="col-md-4" x-data="{ show: false }">
                        <label class="form-label">Kata Sandi Lama</label>
                        <div class="input-group">
                            <input :type="show ? 'text' : 'password'" class="form-control @error('current_password') is-invalid @enderror" wire:model="current_password">
                            <button type="button" class="btn btn-outline-secondary" @click="show = !show" tabindex="-1">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4" x-data="{ show: false }">
                        <label class="form-label">Kata Sandi Baru</label>
                        <div class="input-group">
                            <input :type="show ? 'text' : 'password'" class="form-control @error('new_password') is-invalid @enderror" wire:model="new_password">
                            <button type="button" class="btn btn-outline-secondary" @click="show = !show" tabindex="-1">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        @error('new_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4" x-data="{ show: false }">
                        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <div class="input-group">
                            <input :type="show ? 'text' : 'password'" class="form-control" wire:model="new_password_confirmation">
                            <button type="button" class="btn btn-outline-secondary" @click="show = !show" tabindex="-1">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-secondary me-2" wire:click="toggleChangePasswordForm">Batal</button>
                    <button type="submit" class="btn btn-warning" wire:target="updateOwnPassword" wire:loading.attr="disabled">
                        <span wire:target="updateOwnPassword" wire:loading.remove>Update Password</span>
                        <span wire:target="updateOwnPassword" wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Form Inline Tambah User --}}
        @if ($showCreateForm)
        <div class="card card-body bg-light mb-4 border-primary">
            <h5 class="mb-3">➕ Tambah User Baru</h5>
            <form wire:submit="storeUser">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Nama Lengkap">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Username / Email</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="Username">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Role</label>
                        <select wire:model="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach ($roles as $roleItem)
                            <option value="{{ $roleItem->name }}">{{ $roleItem->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="Password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Ulangi Password">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-secondary me-2" wire:click="toggleCreateForm">Batal</button>
                    <button type="submit" class="btn btn-primary" wire:target="storeUser" wire:loading.attr="disabled">
                        <span wire:target="storeUser" wire:loading.remove>Simpan User</span>
                        <span wire:target="storeUser" wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Table Data --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NAMA</th>
                        <th>USERNAME</th>
                        <th>ROLE</th>
                        <th>PLANT</th>
                        <th>MENU</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr wire:key="user-row-{{ $user->id }}">
                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-info text-dark">{{ implode(', ', $user->getRoleNames()->toArray()) }}</span></td>
                        <td>{{ $user->subsidiary?->name ?? '-' }}</td>
                        <td>
                            {{-- Edit Button --}}
                            <button type="button"
                                class="btn btn-primary btn-sm"
                                wire:click="editUser({{ $user->id }})"
                                wire:target="editUser({{ $user->id }})"
                                wire:loading.attr="disabled"
                                title="Edit Data">
                                <span wire:target="editUser({{ $user->id }})" wire:loading.remove><i class="bi bi-pencil-square"></i></span>
                                <span wire:target="editUser({{ $user->id }})" wire:loading><span class="spinner-border spinner-border-sm" role="status"></span></span>
                            </button>

                            {{-- Delete Button --}}
                            <button type="button"
                                wire:click="deleteUser({{ $user->id }})"
                                wire:confirm="Yakin ingin menghapus user {{ $user->name }}?"
                                wire:target="deleteUser({{ $user->id }})"
                                wire:loading.attr="disabled"
                                class="btn btn-danger btn-sm"
                                title="Delete">
                                <span wire:target="deleteUser({{ $user->id }})" wire:loading.remove><i class="bi bi-trash3-fill"></i></span>
                                <span wire:target="deleteUser({{ $user->id }})" wire:loading><span class="spinner-border spinner-border-sm" role="status"></span></span>
                            </button>
                        </td>
                    </tr>

                    {{-- Form Inline Edit --}}
                    @if ($editingUserId === $user->id)
                    <tr wire:key="edit-row-{{ $user->id }}" class="table-warning">
                        <td colspan="6">
                            <div class="p-3">
                                <h6 class="fw-bold mb-3">✏️ Edit User: {{ $user->name }}</h6>
                                <form wire:submit="updateUser">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control @error('edit_name') is-invalid @enderror" wire:model="edit_name">
                                            @error('edit_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control @error('edit_email') is-invalid @enderror" wire:model="edit_email">
                                            @error('edit_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Role</label>
                                            <select wire:model="edit_role" class="form-select @error('edit_role') is-invalid @enderror">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($roles as $roleItem)
                                                <option value="{{ $roleItem->name }}">{{ $roleItem->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('edit_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Password Baru (Opsional)</label>
                                            <input type="password" class="form-control @error('edit_password') is-invalid @enderror" wire:model="edit_password" placeholder="Biarkan kosong jika tetap">
                                            @error('edit_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Konfirmasi Password</label>
                                            <input type="password" class="form-control" wire:model="edit_password_confirmation" placeholder="Ulangi password">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-secondary me-2" wire:click="cancelEdit">Batal</button>
                                        <button type="submit" class="btn btn-success" wire:target="updateUser" wire:loading.attr="disabled">
                                            <span wire:target="updateUser" wire:loading.remove>Update Data</span>
                                            <span wire:target="updateUser" wire:loading>Menyimpan...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Data user tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
        @endcomponent
    </div>
</div>