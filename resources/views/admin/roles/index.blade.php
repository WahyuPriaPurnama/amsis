@extends('layouts.app')
@section('title', 'Roles and Permission')

@section('content')
    <div class="container py-4">
        @component('components.card')
            @slot('header')
                <h2 class="mb-0">🔐 Role & Permission Management</h2>
            @endslot

            <div class="row g-4">
                {{-- Create Role --}}
                <div class="col-md-6">
                    <form method="POST" action="{{ route('roles.store') }}" class="card card-body shadow-sm">
                        @csrf
                        <h5>Create Role</h5>
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Role name" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Create Role</button>
                    </form>
                </div>


                {{-- Assign Permission to Role --}}
                <div class="col-md-6">
                    <form method="POST" action="{{ route('roles.assign.permission') }}" class="card card-body shadow-sm">
                        @csrf
                        <h5>Assign Permission to Role</h5>
                        <div class="mb-3">
                            <select name="role" class="form-select">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="permission" class="form-select">
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Assign Permission</button>
                    </form>
                </div>

                {{-- Assign Role to User --}}
                <div class="col-md-6">
                    <form method="POST" action="{{ route('users.assign.role') }}" class="card card-body shadow-sm">
                        @csrf
                        <h5>Assign Role to User</h5>
                        <div class="mb-3">
                            <select name="user_id" class="form-select">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100">Assign Role</button>
                    </form>
                </div>

                {{-- Role List --}}
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4>Edit Role & Assign Permissions</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Role Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $role->name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assign Permissions</label>
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $permission->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Role</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>


                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Roles & Permissions</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($roles as $role)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $role->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $role->permissions->pluck('name')->join(', ') ?: 'No permissions' }}
                                            </small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('roles.edit', $role->id) }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>

                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus role {{ $role->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>
@endsection
