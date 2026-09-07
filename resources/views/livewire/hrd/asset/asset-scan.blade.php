<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-center py-3">
            <h5 class="mb-0">Informasi Detail Aset</h5>
        </div>
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="fw-bold">{{ $asset->name }}</h4>
                <span class="badge bg-secondary fs-6 font-monospace">{{ $asset->code }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
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
                    </tbody>
                </table>
            </div>

            @if($asset->photo)
            <div class="mt-4 text-center">
                <h6>Foto Aset:</h6>
                <img src="{{ asset('storage/assets/photo/' . $asset->photo) }}" alt="Foto Aset" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
            </div>
            @endif

            <div class="mt-4">
                <h6>Deskripsi:</h6>
                <p class="text-muted">{{ $asset->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>

            {{-- TOMBOL AKSI EDIT / LOGIN --}}
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary btn-sm" wire:navigate>
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Aset
                </a>

                @auth
                    {{-- Jika sudah login dan memiliki izin edit --}}
                    @can('asset.edit')
                    <a href="{{ route('asset.edit', $asset->id) }}" class="btn btn-warning btn-sm text-dark fw-semibold" wire:navigate>
                        <i class="bi bi-pencil-square me-1"></i> Edit Aset Ini
                    </a>
                    @endcan
                @else
                    {{-- Jika belum login, arahkan ke login dengan membawa URL tujuan saat ini (intended) --}}
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Edit Aset
                    </a>
                @endauth
            </div>

        </div>
    </div>
</div>