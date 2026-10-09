<div>
    <div class="container-fluid mt-3">
        <!-- Alert Message Flash -->
        @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <x-card>
            <x-slot:header>
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Vendor</h5>
                </div>
            </x-slot:header>

            <div class="card-body">
                <!-- Filter & Search Bar (Refactored Grid) -->
                <div class="row mb-3 g-3 align-items-center">
                    <!-- Kolom Pencarian -->
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari nama vendor, PIC, atau nomor kontrak..." wire:model.live.debounce.300ms="search">
                        </div>
                    </div>

                    <!-- Dropdown Filter Status Vendor -->
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="all">Semua Status Vendor</option>
                            <option value="approved">Aktif</option>
                            <option value="inactive">Non-Aktif</option>
                            <option value="blacklisted">Blacklist</option>
                        </select>
                    </div>

                    <!-- Toggle Filter Kontrak Habis (<= 30 Hari) -->
                    <div class="col-md-4 d-flex justify-content-md-end align-items-center">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="filterExpiring" wire:model.live="filterExpiringSoon">
                            <label class="form-check-label fw-bold text-warning ms-1" for="filterExpiring">
                                ⚠️ Kontrak Habis (≤ 30 Hari)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tabel Vendor -->
                <div class="table-responsive">
                    <table class="table table-hover display align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Perusahaan</th>
                                <th>No. Kontrak</th>
                                <th>Masa Kontrak</th>
                                <th>Status Kontrak</th>
                                <th>Status Vendor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vendors as $index => $vendor)
                            <tr>
                                <td>{{ $vendors->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $vendor->company_name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($vendor->address, 40) }}</small>
                                </td>
                                <td>
                                    @if($vendor->contract_number)
                                    <span class="badge bg-light text-dark border">{{ $vendor->contract_number }}</span>
                                    @else
                                    <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vendor->contract_start_date && $vendor->contract_end_date)
                                    <small>{{ $vendor->contract_start_date->format('d/m/Y') }} - {{ $vendor->contract_end_date->format('d/m/Y') }}</small>
                                    @else
                                    <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Status Kontrak (Otomatis Terblokir jika Blacklist) --}}
                                    @if($vendor->status === 'blacklisted')
                                    <span class="badge bg-dark text-white border border-danger">
                                        <i class="bi bi-slash-circle me-1"></i> Terblokir / Batal
                                    </span>
                                    @elseif($vendor->contract_end_date)
                                    @if($vendor->days_remaining < 0)
                                        <span class="badge bg-danger">Habis ({{ abs($vendor->days_remaining) }} Hari Lalu)</span>
                                        @elseif($vendor->days_remaining <= 30)
                                            <span class="badge bg-warning text-dark">Habis {{ $vendor->days_remaining }} Hari Lagi!</span>
                                            @else
                                            <span class="badge bg-success">Aktif</span>
                                            @endif
                                            @else
                                            <span class="badge bg-secondary">Belum Ada Kontrak</span>
                                            @endif
                                </td>
                                <td>
                                    {{-- Status Entitas Vendor --}}
                                    @if($vendor->status === 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                    @elseif($vendor->status === 'inactive')
                                    <span class="badge bg-secondary"><i class="bi bi-dash-circle me-1"></i>Non-Aktif</span>
                                    @elseif($vendor->status === 'blacklisted')
                                    <span class="badge bg-dark text-white"><i class="bi bi-x-circle me-1"></i>Blacklist</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="showDetail({{ $vendor->id }})" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                        <button wire:click="openContractModal({{ $vendor->id }})" class="btn btn-sm btn-primary" title="Kelola Kontrak">
                                            <i class="bi bi-file-earmark-text"></i> Kontrak
                                        </button>

                                        <!-- Dropdown Menu Ubah Status Vendor -->
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDropStatus{{ $vendor->id }}" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDropStatus{{ $vendor->id }}">
                                                @if($vendor->status !== 'approved')
                                                <li>
                                                    <a class="dropdown-item text-success small" href="#"
                                                        wire:click.prevent="setVendorStatus({{ $vendor->id }}, 'approved')"
                                                        wire:confirm="Aktifkan vendor {{ $vendor->company_name }}?">
                                                        <i class="bi bi-check-circle me-1"></i> Set Aktif
                                                    </a>
                                                </li>
                                                @endif

                                                @if($vendor->status !== 'inactive')
                                                <li>
                                                    <a class="dropdown-item text-warning small" href="#"
                                                        wire:click.prevent="setVendorStatus({{ $vendor->id }}, 'inactive')"
                                                        wire:confirm="Non-aktifkan vendor {{ $vendor->company_name }}?">
                                                        <i class="bi bi-pause-circle me-1"></i> Set Non-Aktif
                                                    </a>
                                                </li>
                                                @endif

                                                @if($vendor->status !== 'blacklisted')
                                                <li>
                                                    <a class="dropdown-item text-danger small" href="#"
                                                        wire:click.prevent="setVendorStatus({{ $vendor->id }}, 'blacklisted')"
                                                        wire:confirm="⚠️ YAKIN ingin memasukkan vendor {{ $vendor->company_name }} ke daftar BLACKLIST?">
                                                        <i class="bi bi-slash-circle me-1"></i> Set Blacklist
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data vendor.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Link Pagination -->
                <div class="mt-3">
                    {{ $vendors->links() }}
                </div>
            </div>
        </x-card>

        <!-- ================= MODAL DETAIL VENDOR ================= -->
        @if($isDetailModalOpen && $detailVendor)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Detail Vendor: {{ $detailVendor->company_name }}</h5>
                        <button type="button" wire:click="closeDetailModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Alamat:</strong> {{ $detailVendor->address }}</p>
                                <p><strong>NIB:</strong> {{ $detailVendor->nib }}</p>
                                <p><strong>NPWP:</strong> {{ $detailVendor->npwp }}</p>
                                <p><strong>Sertifikasi:</strong>
                                    Halal: {{ $detailVendor->has_halal ? 'Ya' : 'Tidak' }} |
                                    HACCP: {{ $detailVendor->has_haccp ? 'Ya' : 'Tidak' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Bank:</strong> {{ $detailVendor->bank_name }} ({{ $detailVendor->bank_account_number }})</p>
                                <p><strong>Pemilik Rekening:</strong> {{ $detailVendor->bank_account_holder }}</p>
                                <p><strong>PIC:</strong> {{ $detailVendor->pic_name }} ({{ $detailVendor->pic_phone }})</p>
                                <p><strong>Email PIC:</strong> {{ $detailVendor->pic_email }}</p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Dokumen Terlampir:</h6>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                File NIB
                                @if($detailVendor->nib_file)
                                <a href="{{ Storage::url('vendor-documents/' . $detailVendor->nib_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                                @else
                                <span class="text-muted small">Tidak ada</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                File NPWP
                                @if($detailVendor->npwp_file)
                                <a href="{{ Storage::url('vendor-documents/' . $detailVendor->npwp_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                                @else
                                <span class="text-muted small">Tidak ada</span>
                                @endif
                            </li>
                            @if($detailVendor->certificate_file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sertifikat Pendukung
                                <a href="{{ Storage::url('vendor-documents/' . $detailVendor->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                            </li>
                            @endif
                            @if($detailVendor->contract_file)
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                <strong>Berkas Perjanjian Kontrak</strong>
                                <a href="{{ Storage::url('vendor-contracts/' . $detailVendor->contract_file) }}" target="_blank" class="btn btn-sm btn-primary">Lihat Kontrak</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeDetailModal" class="btn btn-secondary">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- ================= MODAL KELOLA KONTRAK (ADMIN) ================= -->
        @if($isContractModalOpen && $selectedVendorForContract)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Kelola Kontrak: {{ $selectedVendorForContract->company_name }}</h5>
                        <button type="button" wire:click="closeContractModal" class="btn-close btn-close-white"></button>
                    </div>
                    <form wire:submit.prevent="saveContract">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nomor Kontrak Kerja Sama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contract_number') is-invalid @enderror" wire:model="contract_number" placeholder="Contoh: KTR/AMS/2026/001">
                                @error('contract_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('contract_start_date') is-invalid @enderror" wire:model="contract_start_date">
                                    @error('contract_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('contract_end_date') is-invalid @enderror" wire:model="contract_end_date">
                                    @error('contract_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Unggah File Kontrak (PDF/JPG)</label>
                                <input type="file" class="form-control @error('contract_file') is-invalid @enderror" wire:model="contract_file">
                                <div wire:loading wire:target="contract_file" class="text-info small mt-1">⏳ Sedang mengunggah berkas kontrak...</div>
                                @error('contract_file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                @if($selectedVendorForContract->contract_file)
                                <div class="mt-2 small text-muted">
                                    Berkas saat ini:
                                    <a href="{{ Storage::url('vendor-contracts/' . $selectedVendorForContract->contract_file) }}" target="_blank">
                                        {{ $selectedVendorForContract->contract_file }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeContractModal" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">Simpan Kontrak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>