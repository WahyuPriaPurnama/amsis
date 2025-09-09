<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
    <a {{ $attributes->merge([
        'class' => 'btn btn-success',
    ]) }} target="_blank" data-bs-toggle="tooltip"
        data-bs-placement="top" data-bs-title="Simpan"><i class="bi bi-floppy-fill"></i>
        {{ $slot }}
    </a>

</div>
