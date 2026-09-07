<div>
    <div class="container mt-3">

        @component('components.card')
        @slot('header')
        Detail Aset
        @endslot

        {{-- Baris 1: Subsidiary, Kode Aset, Nama Aset, Lokasi --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3"><strong>Subsidiary:</strong><br>{{ $asset->subsidiary?->name ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Kode Aset:</strong><br>{{ $asset->code ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Nama Aset:</strong><br>{{ $asset->name ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Lokasi:</strong><br>{{ $asset->location ?? '-' }}</div>
        </div>

        {{-- Baris 2: Jumlah, Satuan, Kondisi, Pemilik --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3"><strong>Jumlah:</strong><br>{{ $asset->quantity ?? 0 }}</div>
            <div class="col-12 col-md-3"><strong>Satuan:</strong><br>{{ $asset->unit ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Kondisi:</strong><br>{{ $asset->condition ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Pemilik:</strong><br>{{ $asset->owner ?? '-' }}</div>
        </div>

        {{-- Baris 3: Kategori, Kode Akuntansi, Tanggal Pemakaian, Tanggal Pembelian --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3"><strong>Kategori:</strong><br>{{ $asset->category ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Kode Akuntansi:</strong><br>{{ $asset->accounting_code ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Tanggal Pemakaian:</strong><br>{{ $asset->usage_date ?? '-' }}</div>
            <div class="col-12 col-md-3"><strong>Tanggal Pembelian:</strong><br>{{ $asset->purchase_date ?? '-' }}</div>
        </div>

        {{-- Baris 5: File Uploads --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3">
                <strong>Tanda Terima:</strong><br>
                @if ($asset->delivery_receipt)
                <a href="{{ route('asset.delivery_receipt', $asset->id) }}" target="_blank" class="btn btn-sm btn-primary mt-1">
                    <i class="bi bi-file-earmark-text me-1"></i> Lihat
                </a>
                @else
                <span class="text-muted">-</span>
                @endif
            </div>
            <div class="col-12 col-md-3">
                <strong>Manual Book:</strong><br>
                @if ($asset->manual_book)
                <a href="{{ route('asset.manual_book', $asset->id) }}" target="_blank" class="btn btn-sm btn-primary mt-1">
                    <i class="bi bi-file-earmark-text me-1"></i> Lihat
                </a>
                @else
                <span class="text-muted">-</span>
                @endif
            </div>
            <div class="col-12 col-md-3">
                <strong>Foto Aset:</strong><br>
                @if ($asset->photo)
                <a href="{{ route('asset.photo', $asset->id) }}" target="_blank" class="btn btn-sm btn-primary mt-1">
                    <i class="bi bi-image me-1"></i> Lihat
                </a>
                @else
                <span class="text-muted">-</span>
                @endif
            </div>
            <div class="col-12 col-md-3">
                <strong>Lampiran:</strong><br>
                @if ($asset->attachment)
                <a href="{{ route('asset.attachment', $asset->id) }}" target="_blank" class="btn btn-sm btn-primary mt-1">
                    <i class="bi bi-paperclip me-1"></i> Lihat
                </a>
                @else
                <span class="text-muted">-</span>
                @endif
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <strong>Deskripsi:</strong><br>
            <p class="text-secondary mb-0">{{ $asset->description ?: '-' }}</p>
        </div>

        {{-- Kotak QR Code Aset --}}
        <div class="card bg-light border-0 mb-4" id="print-qr-section">
            <div class="card-body text-center py-4">
                {{-- Judul ini akan hilang saat diprint karena ada class no-print --}}
                <h6 class="fw-bold mb-3 no-print"><i class="bi bi-qr-code me-1"></i> QR Code & Label Aset</h6>

                {{-- Area ini yang akan tercetak --}}
                <div class="bg-white p-3 d-inline-block rounded shadow-sm mb-2">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(130)->generate(route('asset.scan', $asset)) !!}
                </div>

                <div class="text-dark font-monospace fw-bold mt-1">{{ $asset->code }}</div>
                <div class="text-secondary small">{{ $asset->name }}</div>

                {{-- Tombol cetak ini akan hilang saat diprint karena ada class no-print --}}
                <div class="mt-3 no-print">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i> Cetak Label Ini
                    </button>
                </div>
            </div>
        </div>
        <hr>

        {{-- Tombol Navigasi & Aksi --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('asset.index') }}" class="btn btn-secondary btn-sm rounded-3 d-inline-flex align-items-center justify-content-center px-3 py-2" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @can('asset.delete')
                <button type="button"
                    class="btn btn-danger btn-sm rounded-3 d-inline-flex align-items-center justify-content-center px-3 py-2"
                    wire:click="deleteAsset"
                    wire:confirm="Yakin ingin menghapus data aset {{ $asset->name }} ({{ $asset->code }})?"
                    wire:loading.attr="disabled">
                    <i class="bi bi-trash-fill me-1"></i> Hapus
                </button>
                @endcan

                @can('asset.edit')
                <a href="{{ route('asset.edit', $asset->id) }}" class="btn btn-warning btn-sm rounded-3 text-white d-inline-flex align-items-center justify-content-center px-3 py-2" wire:navigate>
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @endcan
            </div>
        </div>
        @endcomponent
    </div>
</div>
<style>
    @media print {

        /* 1. Sembunyikan SEMUA elemen di halaman */
        body * {
            visibility: hidden;
        }

        /* 2. Tampilkan HANYA area QR Code beserta isi di dalamnya */
        #print-qr-section,
        #print-qr-section * {
            visibility: visible;
        }

        /* 3. Posisikan QR Code di pojok kiri atas kertas/label */
        #print-qr-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            border: none !important;
            box-shadow: none !important;
            background-color: transparent !important;
        }

        /* 4. Sembunyikan elemen tertentu di dalam kotak QR saat diprint (seperti tombol cetak & judul card) */
        .no-print {
            display: none !important;
        }
    }
</style>