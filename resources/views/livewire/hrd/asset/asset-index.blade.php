<div>
    <div class="container-fluid mt-3">
        @component('components.card')
        @slot('header')
        LIST ASSET
        @endslot

        <div class="button-action mb-3 d-flex flex-wrap justify-content-between gap-2 align-items-center">
            <!-- Tombol Aksi Tambah & Ekspor -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @can('asset.create')
                <x-buttons.create href="{{ route('asset.create') }}" wire:navigate />
                @endcan

                <!-- Menggunakan Variabel Blade Biasa (Bukan $this->) -->
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
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th width="5%">No.</th>
                        <th>Plant</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kondisi</th>
                        <th>Lokasi</th>
                        <th>Editor</th>
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
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border">
                                {{ $asset->condition ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $asset->location ?? '-' }}</td>
                        <td>{{ $asset->user?->name ?? '-' }}</td>
                        <td class="text-center">{{ $asset->created_at?->format('d-m-Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
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
</div>