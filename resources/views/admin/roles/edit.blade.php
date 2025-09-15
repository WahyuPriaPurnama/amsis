@extends('layouts.app')
@section('title', "Edit Role: $role->name")

@section('content')
    <div class="container py-4">
        @component('components.card')
            @slot('header')
                ✏️ Edit Role: <strong>{{ $role->name }}</strong>
            @endslot

            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($permissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                    value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                    {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">← Back</a>
                    <button type="submit" class="btn btn-success">Update Role</button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection
