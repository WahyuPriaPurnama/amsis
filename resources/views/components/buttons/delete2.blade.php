<div>
    <form action="{{ $attributes->get('href') }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" {{ $attributes->merge(['class' => 'btn btn-danger']) }} data-bs-toggle="tooltip"
            data-bs-title="Delete Data" onclick="return confirm('Yakin ingin menghapus data ini?')">
            <i class="bi bi-trash3-fill"></i> {{ $slot }}
        </button>
    </form>
</div>
