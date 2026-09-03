<div>
    {{-- Hanya tampilkan jika state isVisible true dan ada notes --}}
    @if($isVisible && count($notes) > 0)
    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
        <h5 class="fw-bold mb-2">🔔 Release Note:</h5>
        <ul class="mb-0 ps-3">
            @foreach($notes as $note)
            {{-- Parsing tanggal dengan Carbon di blade agar aman --}}
            @php
            $noteDate = \Carbon\Carbon::parse($note['updated_at']);
            $loginDate = \Carbon\Carbon::parse($lastLoginTime);
            $isNew = $noteDate->gt($loginDate);
            @endphp

            <li class="mb-1">
                {{ $note['text'] }}

                @if($isNew)
                <span class="badge bg-success ms-1">Baru</span>
                @endif
            </li>
            @endforeach
        </ul>

        {{-- Tombol close memicu method dismiss() di Livewire, bukan bawaan Bootstrap --}}
        <button type="button" class="btn-close" wire:click="dismiss" aria-label="Close"></button>
    </div>
    @endif
</div>