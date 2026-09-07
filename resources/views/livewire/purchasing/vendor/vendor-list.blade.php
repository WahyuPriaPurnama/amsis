<div>
    <div class="container-fluid mt-3">
        <x-card>
            <x-slot:header>
                Daftar Vendor Disetujui
            </x-slot:header>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover display align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Perusahaan</th>
                                <th>NIB / NPWP</th>
                                <th>PIC & Kontak</th>
                                <th>Akun User Email</th>
                                <th>Tanggal Disetujui</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vendors as $index => $vendor)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $vendor->company_name }}</strong><br><small class="text-muted">{{ $vendor->address }}</small></td>
                                <td>NIB: {{ $vendor->nib }}<br>NPWP: {{ $vendor->npwp }}</td>
                                <td>{{ $vendor->pic_name }}<br>{{ $vendor->pic_phone }}</td>
                                <td>{{ $vendor->pic_email }}</td>
                                <td>{{ $vendor->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button wire:click="showDetail({{ $vendor->id }})" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada vendor yang disetujui.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-card>
        <!-- Tambahkan Modal Detail di bagian bawah file view -->
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
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                File NIB
                                <a href="{{ Storage::url($detailVendor->nib_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                File NPWP
                                <a href="{{ Storage::url($detailVendor->npwp_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
                            </li>
                            @if($detailVendor->certificate_file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sertifikat Pendukung
                                <a href="{{ Storage::url($detailVendor->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Dokumen</a>
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
    </div>
</div>