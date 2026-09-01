<div>
    <div class="container py-4">
        @component('components.card')
        @slot('header')
        🔐 Role & Permission Management
        @endslot

        <div class="row row-cols-1 row-cols-md-2 g-3">
            {{-- Create Role --}}
            <div class="col d-flex">
                <form wire:submit="storeRole"
                    class="card card-body shadow-sm h-100 w-100 d-flex flex-column justify-content-between">
                    @csrf
                    <div class="mb-3 flex-grow-1">
                        <h5 class="mb-3">🆕 Create Role</h5>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Role name">
                        @error('name')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <button class="btn btn-primary w-100 mt-auto" type="submit" wire:target="storeRole" wire:loading.attr="disabled">
                        <span wire:target="storeRole" wire:loading.remove>Create Role</span>
                        <span wire:target="storeRole" wire:loading>
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                        </span>
                    </button>
                </form>
            </div>


            {{-- Assign Role to User --}}
            <div class="col d-flex">
                <form wire:submit="assignRole" class="card card-body shadow-sm h-100 w-100 d-flex flex-column justify-content-between">
                    @csrf
                    <div>
                        <h5 class="mb-3">👤 Assign Role to User</h5>

                        {{-- Select User --}}
                        <div class="mb-3">
                            <select wire:model="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        {{-- Select Role --}}
                        <div class="mb-3">
                            <select wire:model="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button class="btn btn-info text-white w-100 mt-auto" type="submit" wire:target="assignRole" wire:loading.attr="disabled">
                        <span wire:target="assignRole" wire:loading.remove>Assign Role</span>
                        <span wire:target="assignRole" wire:loading>
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                        </span>
                    </button>
                </form>
            </div>
            @if($editingRole)
            {{-- Form Edit Role (Hanya muncul jika tombol Edit diklik) --}}
            <div class="card card-body shadow-sm mb-4 border-warning">
                <h5 class="mb-3">✏️ Edit Role: <strong>{{ $editingRole->name }}</strong></h5>
                <form wire:submit="updateRole">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" wire:model="edit_name" class="form-control @error('edit_name') is-invalid @enderror">
                        @error('edit_name')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Permissions</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($permissions as $permission)
                            <div class="form-check me-3 mb-2">
                                <input class="form-check-input" type="checkbox"
                                    wire:model="selectedPermissions"
                                    value="{{ $permission->name }}"
                                    id="edit-perm-{{ $permission->id }}">
                                <label class="form-check-label" for="edit-perm-{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" wire:click="cancelEdit" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-warning" wire:target="updateRole" wire:loading.attr="disabled">
                            <span wire:target="updateRole" wire:loading.remove>Update Role</span>
                            <span wire:target="updateRole" wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- Role & Permission Overview --}}
        <div class="mt-4">
            <h5 class="mb-3">📋 Roles, Permissions & Subsidiaries</h5>
            <ul class="list-group shadow-sm">
                @foreach ($roles as $role)
                <li class="list-group-item flex-wrap" wire:key="role-item-{{ $role->id }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-3">
                            <strong>{{ $role->name }}</strong><br>
                            <small class="text-muted">
                                {{ $role->permissions->pluck('name')->join(', ') ?: 'No permissions' }}
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            {{-- Edit Button --}}
                            <button type="button"
                                wire:click="editRole({{ $role->id }})"
                                class="btn btn-sm btn-outline-primary">
                                Edit
                            </button>

                            {{-- Delete Button (Sudah diisolasi wire:target) --}}
                            <button type="button"
                                wire:click="deleteRole({{ $role->id }})"
                                wire:confirm="Yakin ingin menghapus role {{ $role->name }}?"
                                wire:target="deleteRole({{ $role->id }})"
                                wire:loading.attr="disabled"
                                class="btn btn-sm btn-outline-danger">
                                <span wire:target="deleteRole({{ $role->id }})" wire:loading.remove>Delete</span>
                                <span wire:target="deleteRole({{ $role->id }})" wire:loading>
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    {{-- Checklist Subsidiaries --}}
                    <form method="POST" action="{{ route('roles.assign.subsidiary') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role->id }}">
                        <div class="row">
                            @foreach ($subsidiaries as $subsidiary)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="subsidiaries[]"
                                        value="{{ $subsidiary->id }}"
                                        id="role-{{ $role->id }}-subsidiary-{{ $subsidiary->id }}"
                                        {{ $role->subsidiaries->contains($subsidiary->id) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="role-{{ $role->id }}-subsidiary-{{ $subsidiary->id }}">
                                        {{ $subsidiary->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-sm btn-success mt-2">Update Subsidiaries</button>
                    </form>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endcomponent
</div>