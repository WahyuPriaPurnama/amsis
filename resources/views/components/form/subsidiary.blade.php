@props(['subsidiary', 'isEdit'])

<form action="{{ $isEdit ? route('subsidiaries.update', $subsidiary->id) : route('subsidiaries.store') }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row mb-3">
        <x-form.input label="Nama" name="name" :value="old('name', $subsidiary->name)" required />
        <x-form.input label="Tagline" name="tagline" :value="old('tagline', $subsidiary->tagline)" />
    </div>

    <div class="row mb-3">
        <x-form.input label="NPWP" name="npwp" :value="old('npwp', $subsidiary->npwp)" />
        <x-form.input label="Email" name="email" type="email" :value="old('email', $subsidiary->email)" />
        <x-form.input label="Phone" name="phone" :value="old('phone', $subsidiary->phone)" />
    </div>

    <div class="row mb-3">
        <div class="col-4">
            <x-form.textarea label="Alamat" name="address" :value="old('address', $subsidiary->address)" />
        </div>
        <div class="col-8 d-flex gap-3">
            <x-form.file label="Logo" name="logo" accept="image/png,image/jpeg,image/jpg" />
            <x-form.file label="Header" name="kop_header" accept="image/png,image/jpeg,image/jpg" />
            <x-form.file label="Footer" name="kop_footer" accept="image/png,image/jpeg,image/jpg" />
            <div class="col">
                @if ($isEdit && $subsidiary->logo)
                <small class="text-muted">Logo lama: {{ $subsidiary->logo }}</small>
                @endif
                
                @if ($isEdit && $subsidiary->kop_header)
                <small class="text-muted">Header lama: {{ $subsidiary->kop_header }}</small>
                @endif
                
                @if ($isEdit && $subsidiary->kop_footer)
                <small class="text-muted">Footer lama: {{ $subsidiary->kop_footer }}</small>
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mb-2">
        {{ $isEdit ? 'Update' : 'Daftar' }}
    </button>
</form>
