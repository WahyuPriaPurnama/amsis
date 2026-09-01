<div>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @component('components.card')
                @slot('header')
                <i class="bi bi-key-fill me-2"></i>Ganti Password Saya
                @endslot

                {{-- Notifikasi Sukses / Gagal --}}
                @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form wire:submit="updateOwnPassword">
                    {{-- Kata Sandi Lama --}}
                    <div class="mb-3" x-data="{ show: false }">
                        <label for="current_password" class="form-label fw-semibold">Kata Sandi Lama</label>
                        <div class="input-group">
                            <input id="current_password"
                                :type="show ? 'text' : 'password'"
                                wire:model="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="Masukkan kata sandi lama">
                            <button type="button"
                                class="btn btn-outline-secondary"
                                @click="show = !show"
                                tabindex="-1"
                                title="Lihat Password">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kata Sandi Baru --}}
                    <div class="mb-3" x-data="{ show: false }">
                        <label for="new_password" class="form-label fw-semibold">Kata Sandi Baru</label>
                        <div class="input-group">
                            <input id="new_password"
                                :type="show ? 'text' : 'password'"
                                wire:model="new_password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                placeholder="Masukkan kata sandi baru">
                            <button type="button"
                                class="btn btn-outline-secondary"
                                @click="show = !show"
                                tabindex="-1"
                                title="Lihat Password">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        @error('new_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi Baru --}}
                    <div class="mb-4" x-data="{ show: false }">
                        <label for="new_password_confirmation" class="form-label fw-semibold">Konfirmasi Kata Sandi Baru</label>
                        <div class="input-group">
                            <input id="new_password_confirmation"
                                :type="show ? 'text' : 'password'"
                                wire:model="new_password_confirmation"
                                class="form-control"
                                placeholder="Ulangi kata sandi baru">
                            <button type="button"
                                class="btn btn-outline-secondary"
                                @click="show = !show"
                                tabindex="-1"
                                title="Lihat Password">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4" wire:target="updateOwnPassword" wire:loading.attr="disabled">
                            <span wire:target="updateOwnPassword" wire:loading.remove>
                                <i class="bi bi-save me-1"></i> Simpan Password
                            </span>
                            <span wire:target="updateOwnPassword" wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
                @endcomponent
            </div>
        </div>
    </div>
</div>