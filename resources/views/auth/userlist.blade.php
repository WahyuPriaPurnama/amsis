@extends('layouts.app')
@section('title', 'List User')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                User Management
            @endslot
            <div class="d-flex justify-content-between mb-3 gap-2">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser"
                        data-bs-toggle="tooltip" title="Tambah User">
                        <i class="bi bi-person-fill-add"></i>
                    </button>
                    <x-buttons.excel href="{{ route('users.export') }}" class="btn btn-success">
                    </x-buttons.excel>
                </div>
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari nama atau email">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="addUserLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addUserLabel">Tambah User</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('users.store') }}" method="post">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="floatingInput" placeholder="nama lengkap" name="name">
                                    <label for="floatingInput">nama lengkap</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        hanya berupa huruf dan tanpa spasi
                                    </div>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        name="email" id="floatingInput" placeholder="username">
                                    <label for="floatingInput">username</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating mb-3">
                                    <select name="role" id="floatingSelect"
                                        class="form-select @error('role') is-invalid @enderror" aria-placeholder="level">
                                        <option value="" selected>pilih level</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="floatingSelect">level</label>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="floatingInput" name="password" placeholder="password">
                                    <label for="floatingInput">password</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" name="password_confirmation" id="floatingInput"
                                        placeholder="konfirmasi password">
                                    <label for="floatingInput">konfirmasi password</label>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating text-end">
                                    <x-buttons.submit>
                                        Simpan
                                    </x-buttons.submit>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="table-responsive">
                <table class="table table-hover">
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
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ implode(', ', $user->getRoleNames()->toArray()) }}</td>
                                <td>{{ $user->subsidiary?->name ?? '-' }}</td>
                                <td>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editData{{ $user->id }}">
                                        <i class="bi bi-pencil-square" data-bs-toggle="tooltip" title="Edit Data"></i>
                                    </button>
                                    <button type="submit" class="btn btn-danger" form="delete-form{{ $user->id }}"
                                        data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash3-fill"></i></button>

                                    <form id="delete-form{{ $user->id }}"
                                        action="{{ route('users.destroy', ['user' => $user->id]) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                    </form>

                                    <!-- Modal -->
                                    <div class="modal fade" id="editData{{ $user->id }}" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="editDataLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="editDataLabel">Edit User
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('users.update', ['user' => $user->id]) }}"
                                                        method="post">
                                                        @method('put')
                                                        @csrf
                                                        <div class="form-floating mb-3">
                                                            <input type="text"
                                                                class="form-control @error('name') is-invalid @enderror"
                                                                id="floatingInput" value="{{ $user->name }}"
                                                                placeholder="nama lengkap" name="name">
                                                            <label for="floatingInput">nama lengkap</label>
                                                            @error('name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            <div class="form-text">
                                                                hanya berupa huruf dan tanpa spasi
                                                            </div>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="text"
                                                                class="form-control @error('email') is-invalid @enderror"
                                                                name="email" id="floatingInput"
                                                                value="{{ $user->email }}" placeholder="username">
                                                            <label for="floatingInput">username</label>
                                                            @error('email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <select name="roles" id="floatingSelect"
                                                            class="form-select @error('role') is-invalid @enderror"
                                                            aria-placeholder="level">
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->name }}"
                                                                    @selected(old('role', $user->roles->pluck('name')->first() ?? '') == $role->name)>{{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mb-3">
                                                            <div class="col">
                                                                <label for="password" class="form-label">Kata Sandi
                                                                    Baru</label>
                                                                <div class="input-group">
                                                                    <input type="password" name="password"
                                                                        id="password{{ $user->id }}"
                                                                        value="{{ old('password') }}"
                                                                        class="form-control @error('password') is-invalid @enderror">
                                                                    <button type="button"
                                                                        class="input-group-text bg-white border-start-0"
                                                                        onclick="togglePassword('password{{ $user->id }}','iconNew{{ $user->id }}')"
                                                                        tabindex="-1" data-bs-toggle="tooltip"
                                                                        title="Lihat Password">
                                                                        <i class="bi bi-eye"
                                                                            id="iconNew{{ $user->id }}"></i>
                                                                    </button>
                                                                    @error('password')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="col">
                                                                <label for="password_confirmation"
                                                                    class="form-label">Konfirmasi Kata Sandi</label>
                                                                <div class="input-group">
                                                                    <input type="password" name="password_confirmation"
                                                                        id="password_confirmation{{ $user->id }}"
                                                                        class="form-control @error('password') is-invalid @enderror">
                                                                    <button type="button"
                                                                        class="input-group-text bg-white border-start-0"
                                                                        onclick="togglePassword('password_confirmation{{ $user->id }}','iconConfirm{{ $user->id }}')"
                                                                        tabindex="-1" data-bs-toggle="tooltip"
                                                                        title="Lihat Password">
                                                                        <i class="bi bi-eye"
                                                                            id="iconConfirm{{ $user->id }}"></i>
                                                                    </button>
                                                                    @error('password_confirmation')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <button class="btn btn-success mb-2"
                                                                type="submit">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        {{ $users->links() }}
                    </tbody>
                </table>
            </div>
        @endcomponent
        <script>
            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            }
        </script>
    @endsection
