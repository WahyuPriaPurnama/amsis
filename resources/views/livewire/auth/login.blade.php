<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            @component('components.card')
            @slot('header')
            <div class="text-center fw-bold">Selamat Datang di AMS Information System</div>
            @endslot

            <form wire:submit.prevent="authenticate">
                {{-- Input Email --}}
                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Username</label>
                    <div class="col-md-6">
                        <input type="text"
                            id="email"
                            wire:model.blur="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Username"
                            autofocus>
                        @error('email')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Input Password dengan Toggle Alpine.js --}}
                <div class="row mb-3" x-data="{ showPassword: false }">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>
                    <div class="col-md-6">
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
                </div>

                {{-- Action Buttons --}}
                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
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
                </div>
            </form>
            @endcomponent
        </div>
    </div>
</div>