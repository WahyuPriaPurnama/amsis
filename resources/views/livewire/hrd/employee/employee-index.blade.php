<div class="container-fluid mt-3">
    @component('components.card')
    @slot('header')
    🧑‍💼 DATA KARYAWAN
    @endslot

    <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between align-items-center">
        @unless (auth()->user()->hasRole('employee'))
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <x-buttons.create href="{{ route('employees.create') }}" wire:navigate />
            <x-buttons.excel href="{{ route('employees.excel') }}">Export</x-buttons.excel>

            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal" title="Import Excel">
                <i class="bi bi-file-earmark-excel me-1"></i> Import
            </button>

            <x-buttons.pdf href="{{ route('employees.pdf') }}" />
        </div>

        {{-- Live Search Input --}}
        <div class="input-group" style="max-width: 320px;">
            <input type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="Cari nama, NIP atau NIK...">
            <button class="btn btn-primary" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>
        @endunless
    </div>

    {{-- Table Data --}}
    <div class="table-responsive">
        <table class="table table-hover display align-middle" style="width: 100%;">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>PERUSAHAAN</th>
                    <th>NIP</th>
                    <th>NAMA</th>
                    <th>JABATAN</th>
                    <th>SEKSI</th>
                    <th>DEPARTEMEN</th>
                    <th>STATUS</th>
                    <th>SISA KONTRAK</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                <tr wire:key="emp-row-{{ $employee->id }}">
                    <td>{{ $employees->firstItem() + $loop->index }}</td>
                    <td>{{ $employee->subsidiary?->name ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $employee->nip }}</span></td>
                    <td>
                        <a href="{{ route('employees.show', $employee->id) }}"
                            class="text-decoration-none fw-semibold"
                            wire:navigate
                            title="Klik untuk lihat detail">
                            {{ $employee->nama }}
                        </a>
                    </td>
                    <td>{{ $employee->posisi }}</td>
                    <td>{{ $employee->seksi }}</td>
                    <td>{{ $employee->departemen }}</td>
                    <td>
                        <span class="badge {{ $employee->status_peg == 'PKWT' ? 'bg-info text-dark' : 'bg-primary' }}">
                            {{ $employee->status_peg }}
                        </span>
                    </td>
                    <td>
                        @if ($employee->status_peg == 'PKWT' && $employee->akhir_kontrak)
                        @php
                        $akhirKontrak = \Carbon\Carbon::parse($employee->akhir_kontrak)->startOfDay();
                        $days = (int) floor(\Carbon\Carbon::now()->startOfDay()->diffInDays($akhirKontrak, false));
                        @endphp

                        @if ($days < 0)
                            <span class="badge bg-danger">{{ abs($days) }} hari yang lalu</span>
                            @elseif ($days === 0)
                            <span class="badge bg-warning text-dark">Hari ini</span>
                            @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                {{ $days }} hari
                            </span>
                            @endif
                            @else
                            <span class="text-muted">-</span>
                            @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Data karyawan tidak ditemukan...</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>#</th>
                    <th>PERUSAHAAN</th>
                    <th>NIP</th>
                    <th>NAMA</th>
                    <th>JABATAN</th>
                    <th>SEKSI</th>
                    <th>DEPARTEMEN</th>
                    <th>STATUS</th>
                    <th>SISA KONTRAK</th>
                </tr>
            </tfoot>
        </table>
        {{ $employees->links() }}
    </div>
    @endcomponent

    {{-- Modal Import (Tetap menggunakan controller import konvensional) --}}
    <div wire:ignore.self class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel"><i class="bi bi-file-earmark-excel me-2"></i>Import Data Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih file Excel (.xlsx)</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>