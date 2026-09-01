<div>
    <div class="container mt-3">
        {{-- Flash Notification --}}
        @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

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

        {{-- Baris 4: Nilai Pembelian, Penyusutan, Total Nilai, Masa Manfaat --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3">
                <strong>Nilai Pembelian:</strong><br>
                Rp {{ number_format($asset->purchase_value ?? 0, 2, ',', '.') }}
            </div>
            <div class="col-12 col-md-3">
                <strong>Nilai Penyusutan:</strong><br>
                Rp {{ number_format($asset->depreciation_value ?? 0, 2, ',', '.') }}
            </div>
            <div class="col-12 col-md-3">
                <strong>Total Nilai:</strong><br>
                Rp {{ number_format($asset->total_value ?? 0, 2, ',', '.') }}
            </div>
            <div class="col-12 col-md-3">
                <strong>Masa Manfaat:</strong><br>
                {{ $asset->useful_life ?? 0 }} bulan
            </div>
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