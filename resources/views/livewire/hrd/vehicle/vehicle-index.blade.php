<div class="container-fluid mt-3">

    @component('components.card')
        @slot('header')
            🚗 DATA KENDARAAN
        @endslot

        {{-- Action Bar & Real-time Search --}}
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                @can('vehicle.create')
                    <x-buttons.create :href="route('vehicles.create')" wire:navigate></x-buttons.create>
                    <x-buttons.pdf :href="route('vehicles.pdf')"></x-buttons.pdf>
                @endcan
            </div>

            {{-- Input Pencarian Real-time --}}
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="Cari kendaraan, nopol, plant...">
                </div>
            </div>
        </div>

        {{-- Tabel Data Kendaraan --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="3%">#</th>
                        <th>NAMA KENDARAAN</th>
                        <th>KATEGORI</th>
                        <th>PLANT</th>
                        <th>NOPOL</th>
                        <th>SERVICE</th>
                        <th>KM</th>
                        <th>STNK</th>
                        <th>PAJAK</th>
                        <th>KIR</th>
                        <th>ASURANSI</th>
                        <th>KONDISI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $item)
                        <tr wire:key="{{ $item->id }}">
                            <td>{{ $vehicles->firstItem() + $loop->index }}</td>
                            <td>
                                @can('vehicle.view')
                                    <a href="{{ route('vehicles.show', ['vehicle' => $item->id]) }}" 
                                       class="text-decoration-none fw-semibold"
                                       wire:navigate>
                                        {{ $item->jenis_kendaraan }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ $item->jenis_kendaraan }}</span>
                                @endcan
                            </td>
                            <td>{{ $item->kategori ?? '-' }}</td>
                            <td>{{ $item->subsidiary?->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">
                                    {{ $item->nopol ?? '-' }}
                                </span>
                            </td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ $item->stnk ?? '-' }}</td>
                            <td>{{ $item->pajak ?? '-' }}</td>
                            <td>{{ $item->kir ?? '-' }}</td>
                            <td>{{ $item->jth_tempo ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    {{ $item->kondisi ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="bi bi-car-front fs-3 d-block mb-1"></i>
                                Tidak ada data kendaraan ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Link Paginasi --}}
        <div class="mt-3">
            {{ $vehicles->links() }}
        </div>
    @endcomponent
</div>