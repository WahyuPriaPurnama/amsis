@component('mail::message')
    # Mesin Berhenti

    Mesin dengan ID: **{{ $data['device_id'] ?? 'Tidak diketahui' }}** telah berhenti.

    - RPM: {{ $data['rpm'] }}
    - Counter: {{ $data['counter'] }}
    - Lokasi: {{ $data['location'] ?? 'Tidak diketahui' }}

    @component('mail::panel')
        Segera lakukan pengecekan lapangan.
    @endcomponent

    Terima kasih,<br>
    Sistem Monitoring
@endcomponent
