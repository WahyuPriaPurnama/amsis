@component('components.card')
    @slot('header')
        <div class="text-center">Selamat Datang di AMS Information System</div>
    @endslot

    <form wire:submit.prevent="login">
        <div class="row mb-3">
            <label for="email" class="col-md-4 col-form-label text-md-end">Username</label>
            <div class="col-md-6">
                <input type="text" wire:model.lazy="email" class="form-control @error('email') is-invalid @enderror"
                    autofocus>
                @error('email')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <x-form.password />

        <div class="row mb-3">
            <div class="col-md-6 offset-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
            </div>
        </div>

        <div class="row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-success" wire:loading.attr="disabled" data-bs-toggle="tooltip"
                    title="klik untuk login">
                    <span wire:loading wire:target="login" class="spinner-border spinner-border-sm me-1"></span>
                    Login
                </button>

                <a href="https://wa.me/6285745334330?text=Halo%20Admin,%20saya%20lupa%20password" class="btn btn-link"
                    target="_blank">
                    Lupa Password?
                </a>
            </div>
        </div>
    </form>
@endcomponent
