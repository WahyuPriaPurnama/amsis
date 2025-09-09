<div>
    <!-- The best way to take care of the future is to take care of the present moment. - Thich Nhat Hanh -->
    <a {{ $attributes->merge([
        'class' => 'btn btn-primary',
    ]) }} data-bs-toggle="tooltip"
        data-bs-title="Tambah Data"><i class="bi bi-file-earmark-plus-fill"></i>
        {{ $slot }}
    </a>
</div>
