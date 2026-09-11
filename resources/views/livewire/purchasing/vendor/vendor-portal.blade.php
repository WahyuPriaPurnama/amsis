<div class="container my-4">
    <!-- Header sambutan -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-1">Portal Vendor: {{ $vendor->company_name }}</h3>
                    <p class="mb-0 text-white-50">Kelola dan perbarui informasi profil serta dokumen legalitas perusahaan Anda di sini.</p>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Form Pembaruan Profil & Dokumen -->
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">Formulir Pembaruan Data Perusahaan</div>
        <div class="card-body">
            <form wire:submit.prevent="updateProfile">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" wire:model="company_name" class="form-control @error('company_name') is-invalid @enderror">
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Login (PIC Utama)</label>
                        <input type="email" class="form-control" value="{{ $vendor->pic_email }}" disabled>
                        <small class="text-muted">Email tidak dapat diubah secara mandiri.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Perusahaan</label>
                    <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror" rows="2"></textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor NIB</label>
                        <input type="text" wire:model="nib" class="form-control @error('nib') is-invalid @enderror">
                        @error('nib') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor NPWP</label>
                        <input type="text" wire:model="npwp" class="form-control @error('npwp') is-invalid @enderror">
                        @error('npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3 text-secondary">Informasi Perbankan</h5>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" wire:model="bank_name" class="form-control @error('bank_name') is-invalid @enderror">
                        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" wire:model="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror">
                        @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" wire:model="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror">
                        @error('bank_account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3 text-secondary">Kontak Person (PIC)</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama PIC</label>
                        <input type="text" wire:model="pic_name" class="form-control @error('pic_name') is-invalid @enderror">
                        @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Telepon / WhatsApp PIC</label>
                        <input type="text" wire:model="pic_phone" class="form-control @error('pic_phone') is-invalid @enderror">
                        @error('pic_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3 text-secondary">Pembaruan Dokumen Lampiran (Opsional)</h5>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Ganti File NIB (PDF/Gambar)</label>
                        <input type="file" wire:model="nib_file" class="form-control">
                        @error('nib_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ganti File NPWP (PDF/Gambar)</label>
                        <input type="file" wire:model="npwp_file" class="form-control">
                        @error('npwp_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ganti Sertifikat Pendukung</label>
                        <input type="file" wire:model="certificate_file" class="form-control">
                        @error('certificate_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled" wire:target="updateProfile">
                        <span wire:loading wire:target="updateProfile" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>