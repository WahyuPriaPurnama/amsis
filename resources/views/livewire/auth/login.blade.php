{{-- Wrapper Background: Mengisi penuh sisa area antara Header dan Footer tanpa gap --}}
<div class="flex-grow-1 w-100 position-relative d-flex align-items-center py-4 py-md-0 overflow-hidden"
    style="background: url('https://images.unsplash.com/photo-1602154663343-89fe0bf541ab?auto=format&fit=crop&w=1920&q=80') no-repeat center center / cover;">

    {{-- Overlay Gradasi Hitam --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(90deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.75) 100%);"></div>

    {{-- Container Form --}}
    <div class="container position-relative z-1">
        <div class="row justify-content-center justify-content-md-end">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4 me-md-4">
                @component('components.card')
                @slot('header')
                <div class="text-center fw-bold">Selamat Datang di AMS Information System</div>
                @endslot

                <form wire:submit.prevent="authenticate">
                    {{-- Input Username --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Username</label>
                        <input type="text"
                            id="email"
                            wire:model.blur="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Masukkan username"
                            autofocus>
                        @error('email')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Input Password dengan Toggle Alpine.js --}}
                    <div class="mb-3" x-data="{ showPassword: false }">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input :type="showPassword ? 'text' : 'password'"
                                id="password"
                                wire:model.blur="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="••••••••">
                            <button type="button"
                                class="btn btn-outline-secondary"
                                @click="showPassword = !showPassword"
                                tabindex="-1">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Action Button --}}
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit"
                            class="btn btn-success"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...
                            </span>
                        </button>
                    </div>
                </form>
                @endcomponent
            </div>
        </div>
    </div>
</div>