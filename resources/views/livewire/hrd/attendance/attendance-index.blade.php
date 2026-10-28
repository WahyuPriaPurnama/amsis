<div class="container-fluid mt-3">
    @component('components.card')
    @slot('header')
    DATA ABSENSI
    @endslot

    <div class="button-action mb-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Tombol Tarik Data Fingerspot --}}
            <button type="button" wire:click="syncFromFingerspot" wire:loading.attr="disabled" class="btn btn-primary btn-sm">
                <span wire:loading.remove wire:target="syncFromFingerspot">
                    <i class="bi bi-cloud-arrow-down me-1"></i> Tarik Data Fingerspot
                </span>
                <span wire:loading wire:target="syncFromFingerspot">
                    <i class="spinner-border spinner-border-sm me-1"></i> Menyinkronkan...
                </span>
            </button>

            {{-- Filter Range Tanggal --}}
            <div class="d-flex align-items-center gap-1">
                <input type="date" class="form-control form-control-sm" wire:model.live="startDate" title="Tanggal Mulai" style="width: 135px;" />
                <span class="small text-muted">s/d</span>
                <input type="date" class="form-control form-control-sm" wire:model.live="endDate" title="Tanggal Selesai" style="width: 135px;" />
            </div>

            {{-- Tombol Export Excel --}}
            <button type="button" wire:click="exportExcel" wire:loading.attr="disabled" class="btn btn-success btn-sm">
                <span wire:loading.remove wire:target="exportExcel">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </span>
                <span wire:loading wire:target="exportExcel">
                    <i class="spinner-border spinner-border-sm me-1"></i> Mengunduh...
                </span>
            </button>
        </div>
        <div class="input-group input-group-sm style-search" style="max-width: 280px;">
            <input type="text" class="form-control" wire:model.live="search" placeholder="Cari Nama / PIN Karyawan..." />
            <span class="input-group-text bg-white">
                <i class="bi bi-search text-muted"></i>
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover display align-middle" style="width: 100%;">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>Nama</th>
                    <th>Plant</th>
                    <th>Waktu Scan</th>
                    <th>Verifikasi</th>
                    <th>Foto Scan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="text-center">
                    <td>
                        <div class="fw-bold text-dark">
                            {{-- Mengambil nama dari relasi attendanceEmployee --}}
                            {{ $log->attendanceEmployee->display_name ?? 'Karyawan Baru' }}
                        </div>
                    </td>
                    <td>
                        {{-- Mengambil nama subsidiary dari relasi subsidiary --}}
                        @if($log->subsidiary)
                        <span class="badge bg-primary">{{ $log->subsidiary->name }}</span>
                        @else
                        <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        {{-- Format Tanggal Ke dd-mm-yyyy HH:ii --}}
                        {{ \Carbon\Carbon::parse($log->scan_date)->format('d-m-Y H:i:s') }}
                    </td>
                    <td>
                        @if ($log->verify == 4)
                        <span class="badge bg-success">Wajah</span>
                        @elseif ($log->verify == 1 || $log->verify == 2)
                        <span class="badge bg-info">Sidik Jari</span>
                        @else
                        <span class="badge bg-secondary">{{ $log->verify ?? '-' }}</span>
                        @endif
                    </td>
                    <!-- Di dalam loop tabel <td> foto scan -->
                    <td>
                        @if($log->photo_url)
                        <button type="button"
                            wire:click="previewPhoto('{{ $log->photo_url }}')"
                            class="btn btn-sm btn-outline-primary text-decoration-none">
                            <i class="bi bi-image me-1"></i> Lihat Foto
                        </button>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>

                    <!-- ================= MODAL PREVIEW FOTO SCAN ================= -->
                    @if($selectedPhotoUrl)
                    <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);" wire:click.self="closePhotoModal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white py-2">
                                    <h6 class="modal-title mb-0">
                                        <i class="bi bi-person-bounding-box me-2"></i>Foto Scan Absensi
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white" wire:click="closePhotoModal"></button>
                                </div>
                                <div class="modal-body text-center p-3 bg-light">
                                    <img src="{{ $selectedPhotoUrl }}" class="img-fluid rounded shadow-sm" alt="Foto Scan Absensi" style="max-height: 450px; object-fit: contain;">
                                </div>
                                <div class="modal-footer py-2 justify-content-between">
                                    <a href="{{ $selectedPhotoUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                                    </a>
                                    <button type="button" wire:click="closePhotoModal" class="btn btn-sm btn-secondary">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
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