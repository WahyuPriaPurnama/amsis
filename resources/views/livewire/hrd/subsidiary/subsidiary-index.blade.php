<div class="container mt-3">

    @component('components.card')
    @slot('header')
    🏢 DATA PERUSAHAAN
    @endslot

    {{-- Action Bar --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            @can('subsidiary.create')
            <button type="button" class="btn btn-primary btn-sm" wire:click="toggleCreateForm">
                <i class="bi bi-{{ $showCreateForm ? 'dash-circle' : 'plus-circle' }} me-1"></i>
                {{ $showCreateForm ? 'Tutup Form' : 'Tambah Perusahaan' }}
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="toggleTransferForm">
                <i class="bi bi-{{ $showTransferForm ? 'dash-circle' : 'arrow-left-right' }} me-1"></i>
                {{ $showTransferForm ? 'Batal Transfer' : 'Transfer Karyawan' }}
            </button>
            @endcan
        </div>

        {{-- Real-time Search Input --}}
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="Cari perusahaan atau alamat...">
            </div>
        </div>
    </div>

    {{-- Form Create Inline --}}
    @if ($showCreateForm)
    <div class="card bg-light border mb-4 shadow-sm">
        <div class="card-header bg-white fw-bold text-primary">
            <i class="bi bi-building-add me-1"></i> Input Data Perusahaan Baru
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
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
                        <input type="file" id="logo" wire:model="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="logo_header" class="form-label fw-semibold">Logo Header Kop Surat</label>
                        <input type="file" id="logo_header" wire:model="logo_header" class="form-control @error('logo_header') is-invalid @enderror" accept="image/*">
                        @error('logo_header') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="logo_footer" class="form-label fw-semibold">Logo Footer Kop Surat</label>
                        <input type="file" id="logo_footer" wire:model="logo_footer" class="form-control @error('logo_footer') is-invalid @enderror" accept="image/*">
                        @error('logo_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-secondary btn-sm me-1" wire:click="toggleCreateForm">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-save me-1"></i> Simpan</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Form Transfer Inline --}}
    @if ($showTransferForm)
    <div class="card bg-light border-primary border mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-arrow-left-right me-1"></i> Transfer Karyawan Antar Plant / Perusahaan
        </div>
        <div class="card-body">
            <form wire:submit.prevent="transferEmployees">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="from_subsidiary_id" class="form-label fw-semibold">Dari Plant / Perusahaan Asal <span class="text-danger">*</span></label>
                        <select id="from_subsidiary_id" wire:model="from_subsidiary_id" class="form-select @error('from_subsidiary_id') is-invalid @enderror">
                            <option value="">-- Pilih Plant Asal --</option>
                            @foreach ($allSubsidiaries as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        @error('from_subsidiary_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="to_subsidiary_id" class="form-label fw-semibold">Ke Plant / Perusahaan Tujuan <span class="text-danger">*</span></label>
                        <select id="to_subsidiary_id" wire:model="to_subsidiary_id" class="form-select @error('to_subsidiary_id') is-invalid @enderror">
                            <option value="">-- Pilih Plant Tujuan --</option>
                            @foreach ($allSubsidiaries as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        @error('to_subsidiary_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-secondary btn-sm me-1" wire:click="toggleTransferForm">Batal</button>
                    <button type="submit"
                        class="btn btn-primary btn-sm"
                        wire:confirm="Seluruh karyawan di plant asal akan dipindahkan ke plant tujuan. Lanjutkan?"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-arrow-right-circle me-1"></i> Eksekusi Transfer</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Memindahkan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Tabel Subsidiary --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Perusahaan</th>
                    <th class="text-center">Jumlah Karyawan</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subsidiaries as $subsidiary)
                <tr wire:key="{{ $subsidiary->id }}">
                    <td>{{ $subsidiaries->firstItem() + $loop->index }}</td>
                    <td>
                        @can('subsidiary.view')
                        <a href="{{ route('subsidiaries.show', $subsidiary->id) }}"
                            class="text-decoration-none fw-semibold"
                            wire:navigate>
                            {{ $subsidiary->name }}
                        </a>
                        @else
                        <span class="text-muted">{{ $subsidiary->name }}</span>
                        @endcan
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                            {{ $subsidiary->employees_count }} Orang
                        </span>
                    </td>
                    <td>{{ $subsidiary->address ?: 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-building-exclamation fs-3 d-block mb-1"></i>
                        Belum ada perusahaan terdaftar
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-3">
        {{ $subsidiaries->links() }}
    </div>
    @endcomponent
</div>