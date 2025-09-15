@extends('layouts.app')
@section('title', 'Roles and Permission')

@section('content')
    <div class="container py-4">
        @component('components.card')
            @slot('header')
                🔐 Role & Permission Management
            @endslot

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                {{-- Create Role --}}
                <div class="col d-flex">
                    <form method="POST" action="{{ route('roles.store') }}"
                        class="card card-body shadow-sm h-100 w-100 d-flex flex-column justify-content-between">
                        @csrf
                        <div class="mb-3 flex-grow-1">
                            <h5 class="mb-3">🆕 Create Role</h5>
                            <input type="text" name="name" class="form-control" placeholder="Role name" required>
                        </div>
                        <button class="btn btn-primary w-100 mt-auto" type="submit">Create Role</button>
                    </form>
                </div>

                {{-- Assign Permission to Role --}}
                <div class="col d-flex">
                    <form method="POST" action="{{ route('roles.assign.permission') }}"
                        class="card card-body shadow-sm h-100 w-100">
                        @csrf
                        <h5 class="mb-3">🔐 Assign Permission</h5>
                        <select name="role" class="form-select mb-3">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <select name="permission" class="form-select mb-3">
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-warning w-100">Assign Permission</button>
                    </form>
                </div>

                {{-- Assign Role to User --}}
                <div class="col d-flex">
                    <form method="POST" action="{{ route('users.assign.role') }}" class="card card-body shadow-sm h-100 w-100">
                        @csrf
                        <h5 class="mb-3">👤 Assign Role to User</h5>
                        <select name="user_id" class="form-select mb-3">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <select name="role" class="form-select mb-3">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-info w-100">Assign Role</button>
                    </form>
                </div>
            </div>

            {{-- Role & Permission Overview --}}
            <div class="mt-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Roles & Permissions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($roles as $role)
                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                                    <div class="me-3">
                                        <strong>{{ $role->name }}</strong><br>
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
