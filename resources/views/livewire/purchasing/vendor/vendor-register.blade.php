<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Form Registrasi Calon Vendor</h4>
                </div>
                <div class="card-body">

                    <!-- Indikator Step -->
                    <div class="row text-center mb-4 fs-6 fw-bold">
                        <div class="col text-{{ $currentStep == 1 ? 'primary' : 'muted' }}">1. Profil & Legalitas</div>
                        <div class="col text-{{ $currentStep == 2 ? 'primary' : 'muted' }}">2. Kualitas</div>
                        <div class="col text-{{ $currentStep == 3 ? 'primary' : 'muted' }}">3. Rekening & PIC</div>
                        <div class="col text-{{ $currentStep == 4 ? 'primary' : 'muted' }}">4. Upload Dokumen</div>
                    </div>

                    <form wire:submit.prevent="submit">
                        <!-- STEP 1 -->
                        @if ($currentStep == 1)
                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" wire:model="company_name" class="form-control @error('company_name') is-invalid @enderror">
                            @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Perusahaan</label>
                            <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror" rows="3"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIB</label>
                            <input type="text" wire:model="nib" class="form-control @error('nib') is-invalid @enderror">
                            @error('nib') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" wire:model="npwp" class="form-control @error('npwp') is-invalid @enderror">
                            @error('npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <!-- STEP 2 -->
                        @if ($currentStep == 2)
                        <div class="mb-3 form-check">
                            <input type="checkbox" wire:model="has_halal" class="form-check-input" id="has_halal">
                            <label class="form-check-label" for="has_halal">Memiliki Sertifikasi Halal</label>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" wire:model="has_haccp" class="form-check-input" id="has_haccp">
                            <label class="form-check-label" for="has_haccp">Memiliki Sertifikasi HACCP</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor SKP (Opsional)</label>
                            <input type="text" wire:model="skp_number" class="form-control">
                        </div>
                        @endif

                        <!-- STEP 3 -->
                        @if ($currentStep == 3)
                        <div class="mb-3">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" wire:model="bank_name" class="form-control @error('bank_name') is-invalid @enderror">
                            @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" wire:model="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror">
                            @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Pemilik Rekening</label>
                            <input type="text" wire:model="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror">
                            @error('bank_account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama PIC</label>
                            <input type="text" wire:model="pic_name" class="form-control @error('pic_name') is-invalid @enderror">
                            @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email PIC</label>
                            <input type="email" wire:model="pic_email" class="form-control @error('pic_email') is-invalid @enderror">
                            @error('pic_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP / WhatsApp PIC</label>
                            <input type="text" wire:model="pic_phone" class="form-control @error('pic_phone') is-invalid @enderror">
                            @error('pic_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <!-- STEP 4 -->
                        @if ($currentStep == 4)
                        <div class="mb-3">
                            <label class="form-label">Upload File NIB (PDF/JPG)</label>
                            <input type="file" wire:model="nib_file" class="form-control @error('nib_file') is-invalid @enderror">

                            <!-- Indikator loading saat file sedang di-upload Livewire -->
                            <div wire:loading wire:target="nib_file" class="text-info small mt-1">Sedang mengunggah NIB...</div>

                            @error('nib_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload File NPWP (PDF/JPG)</label>
                            <input type="file" wire:model="npwp_file" class="form-control @error('npwp_file') is-invalid @enderror">

                            <div wire:loading wire:target="npwp_file" class="text-info small mt-1">Sedang mengunggah NPWP...</div>

                            @error('npwp_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Sertifikat Pendukung (Opsional)</label>
                            <input type="file" wire:model="certificate_file" class="form-control @error('certificate_file') is-invalid @enderror">

                            <div wire:loading wire:target="certificate_file" class="text-info small mt-1">Sedang mengunggah sertifikat...</div>

                            @error('certificate_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <!-- Tombol Navigasi -->
                        <div class="d-flex justify-content-between mt-4">
                            @if ($currentStep > 1)
                            <button type="button" wire:click="decreaseStep" class="btn btn-secondary">Sebelumnya</button>
                            @else
                            <div></div>
                            @endif

                            @if ($currentStep < 4)
                                <button type="button" wire:click="increaseStep" class="btn btn-primary">Selanjutnya</button>
                                @else
                                <button type="submit" class="btn btn-success">Submit Pendaftaran</button>
                                @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>