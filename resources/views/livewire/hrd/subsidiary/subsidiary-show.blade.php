<div class="container mt-3">

    @component('components.card')
    @slot('header')
    Data Perusahaan
    @endslot

    {{-- Action Buttons --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('subsidiaries.index') }}" class="btn btn-secondary btn-sm" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            @can('subsidiary.edit')
            <button type="button" class="btn btn-warning btn-sm text-white" wire:click="toggleEditForm">
                <i class="bi bi-pencil-square me-1"></i> {{ $isEditing ? 'Batal Edit' : 'Edit Perusahaan' }}
            </button>
            @endcan

            @can('subsidiary.delete')
            <button type="button"
                class="btn btn-danger btn-sm"
                wire:click="deleteSubsidiary"
                wire:confirm="Yakin mau hapus perusahaan {{ $subsidiary->name }}?"
                wire:loading.attr="disabled">
                <i class="bi bi-trash3-fill me-1"></i> Hapus
            </button>
            @endcan
        </div>
    </div>

    {{-- Form Edit Inline (Tampil Ketika Tombol Edit Diklik) --}}
    @if ($isEditing)
    <div class="card bg-light border mb-4 shadow-sm">
        <div class="card-header bg-white fw-bold text-warning">
            <i class="bi bi-pencil-square me-1"></i> Edit Data Perusahaan
        </div>
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="PT Example Indonesia">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tagline" class="form-label fw-semibold">Tagline</label>
                        <input type="text" id="tagline" wire:model="tagline" class="form-control @error('tagline') is-invalid @enderror" placeholder="Maju Bersama Mencerahkan">
                        @error('tagline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="npwp" class="form-label fw-semibold">NPWP</label>
                        <input type="text" id="npwp" wire:model="npwp" class="form-control @error('npwp') is-invalid @enderror" placeholder="00.000.000.0-000.000">
                        @error('npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="info@company.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="phone" class="form-label fw-semibold">Telepon</label>
                        <input type="text" id="phone" wire:model="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="031-1234567">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold">Alamat</label>
                        <input type="text" id="address" wire:model="address" class="form-control @error('address') is-invalid @enderror" placeholder="Jl. Raya No. 123, Surabaya">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="logo" class="form-label fw-semibold">Logo Utama (PNG/JPG)</label>
                        <input type="file" id="logo" wire:model="new_logo" class="form-control @error('new_logo') is-invalid @enderror" accept="image/*">
                        @error('new_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="logo_header" class="form-label fw-semibold">Logo Header Kop Surat</label>
                        <input type="file" id="logo_header" wire:model="new_logo_header" class="form-control @error('new_logo_header') is-invalid @enderror" accept="image/*">
                        @error('new_logo_header') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="logo_footer" class="form-label fw-semibold">Logo Footer Kop Surat</label>
                        <input type="file" id="logo_footer" wire:model="new_logo_footer" class="form-control @error('new_logo_footer') is-invalid @enderror" accept="image/*">
                        @error('new_logo_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-secondary btn-sm me-1" wire:click="toggleEditForm">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-save me-1"></i> Perbarui</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Company Header / Profile --}}
    <div class="row my-3 align-items-center text-center">
        <div class="col-md-3 mx-auto">
            @php
            $logoPath = $subsidiary->logo
            ? Storage::url('subsidiary/logo/' . $subsidiary->logo)
            : asset('storage/subsidiary/logo/default.png');
            @endphp
            <img class="img-thumbnail shadow-sm" src="{{ $logoPath }}" alt="Logo {{ $subsidiary->name }}" loading="lazy"
                oncontextmenu="return false" style="max-height: 150px; object-fit: contain;">
        </div>
        <div class="col-md-7 mx-auto">
            <h3 class="fw-bold">{{ $subsidiary->name }}</h3>
            <div class="text-muted">
                {{ $subsidiary->tagline ?? '-' }}
                | {{ $subsidiary->npwp ?? '-' }}
                | {{ $subsidiary->email ?? '-' }}
                | {{ $subsidiary->phone ?? '-' }}
                | {{ $subsidiary->address ?? '-' }}
            </div>
        </div>
    </div>

    <hr>

    {{-- Table Employee List --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">NO</th>
                    <th>NAMA KARYAWAN</th>
                    <th>POSISI</th>
                    <th>STATUS PEGAWAI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subsidiary->employees as $employee)
                <tr wire:key="{{ $employee->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('employees.show', $employee->id) }}" class="text-decoration-none fw-semibold" wire:navigate>
                            {{ $employee->nama }}
                        </a>
                    </td>
                    <td>{{ $employee->posisi ?? '-' }}</td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                            {{ $employee->status_peg ?? '-' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-people fs-3 d-block mb-1"></i>
                        Belum ada karyawan yang terdaftar di perusahaan ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endcomponent
</div>