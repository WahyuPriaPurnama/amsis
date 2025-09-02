<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
    <a {{ $attributes->merge([
        'class' => 'btn btn-success',
    ]) }}data-bs-toggle="tooltip" data-bs-title="Export Excel"><i class="bi bi-file-earmark-spreadsheet"></i>
        {{ $slot }}
    </a>
</div>
