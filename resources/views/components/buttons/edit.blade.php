<div>
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
    <a {{ $attributes->merge([
        'class' => 'btn btn-warning',
    ]) }} data-bs-toggle="tooltip" data-bs-title="Edit Data"><i class="bi bi-pencil-square" wire:navigate></i>
        {{ $slot }}
    </a>
</div>