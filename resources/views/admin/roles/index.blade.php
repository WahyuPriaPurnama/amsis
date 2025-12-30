@extends('layouts.app')
@section('title', 'Roles and Permission')

@section('content')
    <div class="container py-4">
        @component('components.card')
            @slot('header')
                🔐 Role & Permission Management
            @endslot

            <div class="row row-cols-1 row-cols-md-2 g-3">
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
                <h5 class="mb-3">📋 Roles, Permissions & Subsidiaries</h5>
                <ul class="list-group shadow-sm">
                    @foreach ($roles as $role)
                        <li class="list-group-item flex-wrap">
                            <div class="d-flex justify-content-between align-items-start">
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
@endsection
