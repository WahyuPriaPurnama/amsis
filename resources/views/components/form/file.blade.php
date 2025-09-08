@props(['label', 'name', 'accept' => ''])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input type="file" name="{{ $name }}" id="{{ $name }}"
        class="form-control @error($name) is-invalid @enderror" accept="{{ $accept }}">
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
