<div>
    <!-- Nothing worth having comes easy. - Theodore Roosevelt -->
    <button {{ $attributes->merge([
        'class' => 'btn btn-success',
    ]) }}data-bs-toggle="tooltip"
        data-bs-title="Import Excel"><i class="bi bi-file-earmark-spreadsheet"></i>
        {{ $slot }}
    </button>
</div>
