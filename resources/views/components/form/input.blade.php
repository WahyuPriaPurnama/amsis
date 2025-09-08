@props(['label', 'name', 'value' => '', 'type' => 'text', 'required' => false])

<div class="col">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}"
        class="form-control @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }}>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
