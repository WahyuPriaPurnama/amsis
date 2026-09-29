<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h4 class="mb-0 fs-5 fw-bold">Form Registrasi Calon Vendor</h4>
                </div>
                <div class="card-body p-4">

                    <!-- Progress / Step Indicator -->
                    <div class="row text-center mb-4 g-2">
                        <div class="col">
                            <div class="p-2 rounded {{ $currentStep == 1 ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                                1. Profil & Legalitas
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded {{ $currentStep == 2 ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                                2. Kualitas
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded {{ $currentStep == 3 ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                                3. Rekening & PIC
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded {{ $currentStep == 4 ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                                4. Upload Dokumen
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="submit">
                        <!-- STEP 1: Profil & Legalitas -->
                        @if ($currentStep === 1)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" wire:model.blur="company_name" class="form-control @error('company_name') is-invalid @enderror" placeholder="PT. Example Jaya">
                            @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Perusahaan <span class="text-danger">*</span></label>
                            <textarea wire:model.blur="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Alamat lengkap kantor pusat..."></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIB (Nomor Induk Berusaha) <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="nib" class="form-control @error('nib') is-invalid @enderror">
                                @error('nib') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NPWP Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="npwp" class="form-control @error('npwp') is-invalid @enderror">
                                @error('npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif

                        <!-- STEP 2: Kualitas & Sertifikasi -->
                        @if ($currentStep === 2)
                        <div class="card bg-light border-0 p-3 mb-3">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" wire:model="has_halal" class="form-check-input" id="has_halal" role="switch">
                                <label class="form-check-label fw-semibold" for="has_halal">Memiliki Sertifikasi Halal</label>
                            </div>

                            <div class="form-check form-switch mb-1">
                                <input type="checkbox" wire:model="has_haccp" class="form-check-input" id="has_haccp" role="switch">
                                <label class="form-check-label fw-semibold" for="has_haccp">Memiliki Sertifikasi HACCP</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor SKP (Sertifikat Kelayakan Pengolahan) <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" wire:model.blur="skp_number" class="form-control @error('skp_number') is-invalid @enderror" placeholder="Isi jika ada">
                            @error('skp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <!-- STEP 3: Rekening & Informasi PIC -->
                        @if ($currentStep === 3)
                        <h6 class="fw-bold text-secondary mb-3">Informasi Bank</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="bank_name" class="form-control @error('bank_name') is-invalid @enderror" placeholder="BCA / Mandiri / BRI">
                                @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Nomor Rekening <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror">
                                @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Pemilik Rekening <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror">
                                @error('bank_account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-3">

                        <h6 class="fw-bold text-secondary mb-3">Informasi Person in Charge (PIC)</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama PIC <span class="text-danger">*</span></label>
                            <input type="text" wire:model.blur="pic_name" class="form-control @error('pic_name') is-invalid @enderror">
                            @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email PIC <span class="text-danger">*</span></label>
                                <input type="email" wire:model.blur="pic_email" class="form-control @error('pic_email') is-invalid @enderror" placeholder="pic@perusahaan.com">
                                @error('pic_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No. HP / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="pic_phone" class="form-control @error('pic_phone') is-invalid @enderror" placeholder="08123456789">
                                @error('pic_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif

                        <!-- STEP 4: Unggah Dokumen -->
                        @if ($currentStep === 4)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload File NIB (PDF/JPG) <span class="text-danger">*</span></label>
                            <input type="file" wire:model="nib_file" class="form-control @error('nib_file') is-invalid @enderror">
                            <div wire:loading wire:target="nib_file" class="text-info small mt-1">⏳ Sedang mengunggah file NIB...</div>
                            @error('nib_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload File NPWP (PDF/JPG) <span class="text-danger">*</span></label>
                            <input type="file" wire:model="npwp_file" class="form-control @error('npwp_file') is-invalid @enderror">
                            <div wire:loading wire:target="npwp_file" class="text-info small mt-1">⏳ Sedang mengunggah file NPWP...</div>
                            @error('npwp_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Sertifikat Pendukung <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="file" wire:model="certificate_file" class="form-control @error('certificate_file') is-invalid @enderror">
                            <div wire:loading wire:target="certificate_file" class="text-info small mt-1">⏳ Sedang mengunggah sertifikat...</div>
                            @error('certificate_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <!-- Navigasi Tombol -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            @if ($currentStep > 1)
                            <button type="button" wire:click="decreaseStep" class="btn btn-outline-secondary px-4">
                                ← Sebelumnya
                            </button>
                            @else
                            <div></div>
                            @endif

                            @if ($currentStep < 4)
                                <button type="button" wire:click="increaseStep" class="btn btn-primary px-4">
                                Selanjutnya →
                                </button>
                                @else
                                <button type="submit" class="btn btn-success px-4" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submit">Submit Pendaftaran</span>
                                    <span wire:loading wire:target="submit">Memproses...</span>
                                </button>
                                @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>