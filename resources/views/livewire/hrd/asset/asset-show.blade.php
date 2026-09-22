<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-center py-3">
            <h5 class="mb-0">Informasi Detail Aset</h5>
        </div>
        <div class="card-body p-4">

            <div class="text-center mb-4">
                @if($asset->photo)
                <div class="mb-2">
                    @if ($asset->photo)
                    <img src="{{ asset('storage/assets/photo/' . $asset->photo) }}"
                        alt="{{ $asset->name }}"
                        class="img-thumbnail"
                        style="max-height: 150px; object-fit: cover;">
                    @endif
                </div>
                <button wire:click="downloadFile('photo')" class="btn btn-sm btn-primary">
                    <i class="bi bi-download me-1"></i> Unduh / Lihat Berkas
                </button>
                @else
                <span class="text-muted">-</span>
                @endif

                <h4 class="fw-bold">{{ $asset->name }}</h4>
                <span class="badge bg-secondary fs-6 font-monospace">{{ $asset->code }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <tbody>
                        <tr>
                            <th class="w-50">Subsidiary / Plant</th>
                            <td>{{ $asset->subsidiary?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $asset->location ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kondisi Saat Ini</th>
                            <td>
                                @if($asset->condition === 'Baik')
                                <span class="badge bg-success">Baik</span>
                                @elseif($asset->condition === 'Rusak')
                                <span class="badge bg-danger">Rusak</span>
                                @else
                                <span class="badge bg-warning text-dark">{{ $asset->condition }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $asset->category ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pemilik (Departemen)</th>
                            <td>{{ $asset->owner ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah & Satuan</th>
                            <td>{{ $asset->quantity }} {{ $asset->unit }}</td>
                        </tr>

                        {{-- QR CODE ASET --}}
                        <tr>
                            <th>QR Code Aset</th>
                            <td>
                                <div class="p-2 bg-light border rounded text-center d-inline-block bg-white">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate(route('asset.scan', $asset->id)) !!}
                                </div>
                                <div class="mt-1">
                                    <a href="{{ route('asset.scan', $asset->id) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-qr-code me-1"></i> Buka Link Scan
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- LAMPIRAN --}}
                        <tr>
                            <th>Lampiran</th>
                            <td>
                                @if($asset->attachment)
                                <button wire:click="downloadFile('attachment')" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-file-earmark-text me-1"></i> Lihat Lampiran
                                </button>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h6>Deskripsi:</h6>
                <p class="text-muted">{{ $asset->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary" wire:navigate>
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <div class="gap-2 d-flex">
                    <a href="{{ route('asset.edit', $asset->id) }}" class="btn btn-warning text-dark fw-semibold" wire:navigate>
                        <i class="bi bi-pencil me-1"></i> Edit Aset
                    </a>
                    <button wire:click="deleteAsset" wire:confirm="Yakin ingin menghapus aset ini?" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>