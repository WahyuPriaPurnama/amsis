<div class="container-fluid mt-3">

    @component('components.card')
    @slot('header')
    LIST ASSET
    @endslot

    {{-- Kotak Ringkasan Statistik dengan Tombol Collapse --}}
    <div class="card border-0 shadow-sm bg-light mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold small text-muted text-uppercase">
                        <i class="bi bi-pie-chart-fill me-1"></i> Ringkasan Statistik Aset
                    </span>
                    <span class="badge bg-primary">Total Item: {{ $totalItemsCount }}</span>
                    <span class="badge bg-success">Total Qty: {{ $totalQuantityCount }} Pcs</span> {{-- <-- Badge Total Qty Keseluruhan --}}
                    <span class="badge bg-secondary">{{ count($assetBreakdown) }} Jenis Barang</span>
                </div>

                {{-- Tombol Pemicu Collapse --}}
                <button class="btn btn-outline-secondary btn-sm px-3 py-1 shadow-sm d-inline-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAssetDetail" aria-expanded="false" aria-controls="collapseAssetDetail">
                    <i class="bi bi-chevron-down me-1"></i> Rincian Detail
                </button>
            </div>

            {{-- Area yang bisa disembunyikan/ditampilkan --}}
            <div class="collapse mt-3 pt-3 border-top" id="collapseAssetDetail">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($assetBreakdown as $item)
                    <div class="bg-white border rounded px-2.5 py-1 shadow-sm small">
                        <span class="fw-semibold text-dark">{{ $item->name }}</span>:
                        <span class="badge bg-light text-dark border">{{ $item->total }} Baris</span>
                        <span class="badge bg-success-subtle text-success border fw-bold">{{ $item->total_qty ?? 0 }} Pcs</span> {{-- <-- Qty per Jenis Barang --}}
                    </div>
                    @empty
                    <span class="text-muted small">Belum ada data aset untuk ditampilkan.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="button-action mb-3 d-flex flex-wrap justify-content-between gap-2 align-items-center">
        <!-- Tombol Aksi Tambah & Ekspor -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @can('asset.create')
            <x-buttons.create href="{{ route('asset.create') }}" wire:navigate />
            @endcan

            <x-buttons.pdf href="{{ route('asset.export_pdf', ['subsidiary_id' => $subsidiary_id, 'search' => $search]) }}" />
            <x-buttons.excel href="{{ route('asset.export_excel', ['subsidiary_id' => $subsidiary_id, 'search' => $search]) }}" />
        </div>

        <!-- Form Filter & Pencarian Reaktif -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <select wire:model.live="subsidiary_id" class="form-select" style="min-width: 180px;">
                <option value="">-- Semua Plant --</option>
                @foreach ($allSubsidiaries as $sub)
                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                @endforeach
            </select>

            <div class="input-group" style="min-width: 250px;">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    placeholder="Cari nama atau kode ...">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Tabel Data Aset -->
    <div class="table-responsive">
        <table class="table table-hover display align-middle" style="width: 100%;">
            <thead class="table-dark">
                <tr class="text-center">
                    <th width="5%">No.</th>
                    <th>Plant</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Qty</th> {{-- <-- Tambahkan Kolom Qty di Tabel jika diperlukan --}}
                    <th>Kondisi</th>
                    <th>Lokasi</th> 
                    <th>Tgl Input</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td class="text-center">{{ $assets->firstItem() + $loop->index }}</td>
                    <td>{{ $asset->subsidiary?->name ?? '-' }}</td>
                    <td class="text-center">
                        @can('asset.view')
                        <a href="{{ route('asset.show', $asset->id) }}" class="text-decoration-none fw-semibold" wire:navigate>
                            {{ $asset->code }}
                        </a>
                        @else
                        {{ $asset->code }}
                        @endcan
                    </td>
                    <td>{{ $asset->name }}</td>
                    <td class="text-center fw-bold">{{ $asset->quantity ?? 0 }}</td> {{-- <-- Data Qty per baris --}}
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary border">
                            {{ $asset->condition ?? '-' }}
                        </span>
                    </td>
                    <td>{{ $asset->location ?? '-' }}</td>
                    <td class="text-center">{{ $asset->created_at?->format('d-m-Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                        Tidak ada data aset ditemukan..
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi Livewire -->
    <div class="mt-3">
        {{ $assets->links() }}
    </div>
    @endcomponent
</div>