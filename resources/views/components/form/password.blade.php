@props([
    'id' => 'password',
    'label' => 'Password',
    'name' => 'password',
    'error' => 'password',
])

<div x-data="{ visible: false }" class="row mb-3">
    <label for="{{ $id }}" class="col-md-4 col-form-label text-md-end">{{ $label }}</label>
    <div class="col-md-6">
        <div class="input-group">
            <input :type="visible ? 'text' : 'password'" name="{{ $name }}" id="{{ $id }}"
                class="form-control @error($error) is-invalid @enderror" value="{{ old($name) }}">

            <button type="button" class="input-group-text bg-white border-start-0" @click="visible = !visible"
                tabindex="-1" data-bs-toggle="tooltip" title="Lihat Password">
                <i class="bi" :class="visible ? 'bi-eye-slash' : 'bi-eye'"></i>
            </button>
        </div>
        @error($error)
            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
