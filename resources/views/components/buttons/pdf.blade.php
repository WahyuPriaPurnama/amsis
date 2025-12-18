<div>
    <!-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci -->
    <a {{ $attributes->merge([
        'class' => 'btn btn-danger',
    ]) }} target="_blank" data-bs-toggle="tooltip"
        data-bs-placement="top" data-bs-title="Export PDF"><i class="bi bi-filetype-pdf"></i>
        {{ $slot }}
    </a>
</div>
