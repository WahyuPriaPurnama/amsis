@extends('layouts.app')
@section('title', 'Ganti Password')
@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6"> {{-- Ubah ukuran sesuai kebutuhan --}}
                @component('components.card')
                    @slot('header')
                        Ganti Password
                    @endslot
                    <form method="POST" action="{{ route('password.update2') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="col">
                                <label for="current_password" class="col col-form-label text-md-end">Kata Sandi Lama</label>
                                <div class="input-group">
                                    <input id="current_password" type="password" value="{{ old('current_password') }}"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        name="current_password" autocomplete="current-password">
                                    <button type="button" class="input-group-text bg-white border-start-0"
                                        onclick="togglePassword('current_password','iconCurrent')" tabindex="-1"
                                        data-bs-toggle="tooltip" title="Lihat Password">
                                        <i class="bi bi-eye" id="iconCurrent"></i>
                                    </button>
                                    @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col">
                                <label for="password" class="form-label">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" value="{{ old('password') }}"
                                        class="form-control @error('password') is-invalid @enderror">
                                    <button type="button" class="input-group-text bg-white border-start-0"
                                        onclick="togglePassword('password','iconNew')" tabindex="-1" data-bs-toggle="tooltip"
                                        title="Lihat Password">
                                        <i class="bi bi-eye" id="iconNew"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control @error('password') is-invalid @enderror">
                                    <button type="button" class="input-group-text bg-white border-start-0"
                                        onclick="togglePassword('password_confirmation','iconConfirm')" tabindex="-1"
                                        data-bs-toggle="tooltip" title="Lihat Password">
                                        <i class="bi bi-eye" id="iconConfirm"></i>
                                    </button>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="simpan password">Simpan</button>
                        </div>
                    </form>
                @endcomponent
            </div>
        </div>
    </div>
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
