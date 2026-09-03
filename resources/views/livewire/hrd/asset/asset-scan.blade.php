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

        </div>
    </div>
</div>