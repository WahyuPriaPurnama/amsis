<div>
    <div class="container-fluid mt-3">
        <x-card>
            <x-slot:header>
                Review Pengajuan Vendor
            </x-slot:header>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover display align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Perusahaan</th>
                                <th>NIB / NPWP</th>
                                <th>Nama PIC</th>
                                <th>Kontak PIC</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vendors as $index => $vendor)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $vendor->company_name }}</strong><br><small class="text-muted">{{ $vendor->address }}</small></td>
                                <td>NIB: {{ $vendor->nib }}<br>NPWP: {{ $vendor->npwp }}</td>
                                <td>{{ $vendor->pic_name }}</td>
                                <td>{{ $vendor->pic_email }}<br>{{ $vendor->pic_phone }}</td>
                                <td>{{ $vendor->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button wire:click="openReviewModal({{ $vendor->id }})" class="btn btn-sm btn-primary">
                                        Review Berkas
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada pengajuan vendor yang pending.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
    </div>
    </x-card>
</div>

<!-- MODAL REVIEW DETAIL & KEPUTUSAN -->
@if($isModalOpen && $selectedVendor)
<div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Berkas: {{ $selectedVendor->company_name }}</h5>
                <button type="button" wire:click="closeModal" class="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Alamat:</strong> {{ $selectedVendor->address }}</p>
                        <p><strong>NIB:</strong> {{ $selectedVendor->nib }}</p>
                        <p><strong>NPWP:</strong> {{ $selectedVendor->npwp }}</p>
                        <p><strong>Sertifikasi:</strong>
                            Halal: {{ $selectedVendor->has_halal ? 'Ya' : 'Tidak' }} |
                            HACCP: {{ $selectedVendor->has_haccp ? 'Ya' : 'Tidak' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Bank:</strong> {{ $selectedVendor->bank_name }} ({{ $selectedVendor->bank_account_number }}) a.n {{ $selectedVendor->bank_account_holder }}</p>
                        <p><strong>PIC:</strong> {{ $selectedVendor->pic_name }}</p>
                        <p><strong>Email PIC:</strong> {{ $selectedVendor->pic_email }}</p>
                        <p><strong>Telepon PIC:</strong> {{ $selectedVendor->pic_phone }}</p>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold mb-3">Dokumen Lampiran:</h6>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        File NIB
                        <a href="{{ Storage::url($selectedVendor->nib_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        File NPWP
                        <a href="{{ Storage::url($selectedVendor->npwp_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                    </li>
                    @if($selectedVendor->certificate_file)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Sertifikat Pendukung
                        <a href="{{ Storage::url($selectedVendor->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                    </li>
                    @endif
                </ul>

                <!-- Input Alasan Penolakan jika tombol tolak diklik -->
                @if($actionType == 'reject')
                <div class="mb-3 bg-light p-3 border rounded">
                    <label class="form-label text-danger fw-bold">Alasan Penolakan / Perbaikan Berkas:</label>
                    <textarea wire:model="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" rows="3" placeholder="Tuliskan bagian dokumen yang salah atau perlu diperbaiki..."></textarea>
                    @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    <div class="mt-2 text-end">
                        <button type="button" wire:click="rejectVendor" class="btn btn-danger btn-sm">Kirim Penolakan</button>
                        <button type="button" wire:click="$set('actionType', '')" class="btn btn-secondary btn-sm">Batal</button>
                    </div>
                </div>
                @endif
            </div>

            <div class="modal-footer">
                @if($actionType != 'reject')
                <button type="button" wire:click="$set('actionType', 'reject')" class="btn btn-danger">Tolak (Tidak)</button>
                <button type="button" wire:click="approveVendor({{ $selectedVendor->id }})" class="btn btn-success">Setujui (Ya)</button>
                @endif
                <button type="button" wire:click="closeModal" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
</div>