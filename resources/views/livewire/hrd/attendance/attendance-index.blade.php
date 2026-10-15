<div class="container-fluid mt-3">
    @component('components.card')
    @slot('header')
    DATA ABSENSI
    @endslot

    <div class="button-action mb-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
     
        <!-- <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" wire:click="syncFromFingerspot" wire:loading.attr="disabled" class="btn btn-primary btn-sm">
                <span wire:loading.remove wire:target="syncFromFingerspot">
                    <i class="bi bi-cloud-arrow-down me-1"></i> Tarik Data Fingerspot
                </span>
                <span wire:loading wire:target="syncFromFingerspot">
                    <i class="spinner-border spinner-border-sm me-1"></i> Menyinkronkan...
                </span>
            </button>
        </div> -->

        <div class="input-group input-group-sm style-search" style="max-width: 280px;">
            <input type="text" class="form-control" wire:model.live="search" placeholder="Cari PIN Karyawan..." />
            <span class="input-group-text bg-white">
                <i class="bi bi-search text-muted"></i>
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover display align-middle" style="width: 100%;">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>PIN</th>
                    <th>Waktu Scan</th>
                    <th>Verifikasi</th>
                    <th>Foto Scan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="text-center">
                    <td>{{ $log->pin }}</td>
                    <td>{{ $log->scan_date }}</td>
                    <td>{{ $log->verify }}</td>
                    <td>
                        @if($log->photo_url)
                        <a href="{{ $log->photo_url }}" target="_blank" class="btn btn-link btn-sm text-decoration-none">Lihat Foto</a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Tidak ada data absensi ditemukan..
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
    @endcomponent
</div>