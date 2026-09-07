<div>
    <div class="container-fluid mt-3">
        @component('components.card')
        @slot('header')
        Edit Aset
        @endslot

        <form wire:submit.prevent="update">

            {{-- Baris 1: Subsidiary, Kode, Nama, Lokasi --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Subsidiary <span class="text-danger">*</span></label>
                    <select wire:model="subsidiary_id" class="form-select @error('subsidiary_id') is-invalid @enderror">
                        <option value="">-- Pilih Plant --</option>
                        @foreach ($subsidiaries as $subsidiary)
                        <option value="{{ $subsidiary->id }}">{{ $subsidiary->name }}</option>
                        @endforeach
                    </select>
                    @error('subsidiary_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Kode Aset <span class="text-danger">*</span></label>
                    <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror" placeholder="A001">
                    @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Nama Aset <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Printer Canon">
                    @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" wire:model="location" class="form-control @error('location') is-invalid @enderror" placeholder="Gudang A">
                    @error('location') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Baris 2: Jumlah, Satuan, Kondisi, Pemilik --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" wire:model="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1">
                    @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" wire:model="unit" class="form-control @error('unit') is-invalid @enderror" placeholder="Pcs / Unit">
                    @error('unit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Kondisi</label>
                    <select wire:model="condition" class="form-select @error('condition') is-invalid @enderror">
                        @foreach (['Baik', 'Rusak', 'Lainnya'] as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('condition') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Pemilik</label>
                    <select wire:model="owner" class="form-select @error('owner') is-invalid @enderror">
                        @foreach (['Umum', 'Engineering', 'QC & Lab'] as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('owner') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Baris 3: Kategori, Kode Akuntansi, Tanggal Pemakaian, Tanggal Pembelian --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Kategori</label>
                    <select wire:model="category" class="form-select @error('category') is-invalid @enderror">
                        @foreach (['Tanah & Bangunan', 'Mesin', 'Furniture & Fixture', 'Kendaraan', 'Alat Kerja', 'Fasilitas'] as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Kode Akuntansi</label>
                    <input type="text" wire:model="accounting_code" class="form-control @error('accounting_code') is-invalid @enderror">
                    @error('accounting_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Tanggal Pemakaian</label>
                    <input type="date" wire:model="usage_date" class="form-control @error('usage_date') is-invalid @enderror">
                    @error('usage_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" wire:model="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror">
                    @error('purchase_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            
            {{-- Baris 5: File Uploads dengan Handler Universal (Kompresi Gambar & Pengecekan Ukuran File 2MB) --}}
            <div class="row g-3 mb-3" x-data="{
    isProcessing: false,
    processingType: '',
    handleFile(event, wireProperty) {
        const file = event.target.files[0];
        if (!file) return;

        this.isProcessing = true;
        this.processingType = wireProperty;

        // Jika file berupa gambar, kompres dulu baru cek ukurannya
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 1200; // Batas lebar maksimal

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Kompres ke format JPEG dengan kualitas 0.8 (80%)
                    canvas.toBlob((blob) => {
                        const compressedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });

                        // VALIDASI UKURAN SETELAH DIKOMPRESI (Maksimal 2MB)
                        const maxSize = 2 * 1024 * 1024;
                        if (compressedFile.size > maxSize) {
                            alert('Ukuran foto masih terlalu besar setelah dikompresi. Silakan gunakan resolusi yang lebih rendah.');
                            event.target.value = '';
                            this.isProcessing = false;
                            this.processingType = '';
                            return;
                        }

                        // Kirim file hasil kompresi yang sudah aman ke Livewire
                        @this.upload(wireProperty, compressedFile, () => {
                            this.isProcessing = false;
                            this.processingType = '';
                        }, () => {
                            this.isProcessing = false;
                            this.processingType = '';
                        });
                    }, 'image/jpeg', 0.8);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Untuk file non-gambar (seperti PDF), tetap cek ukurannya di awal karena tidak bisa dikompres
            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar! Maksimal 2MB.');
                event.target.value = '';
                this.isProcessing = false;
                this.processingType = '';
                return;
            }

            @this.upload(wireProperty, file, () => {
                this.isProcessing = false;
                this.processingType = '';
            }, () => {
                this.isProcessing = false;
                this.processingType = '';
            });
        }
    }
}">
                {{-- Tanda Terima --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Tanda Terima</label>
                    <input type="file" @change="handleFile($event, 'new_delivery_receipt')" class="form-control @error('new_delivery_receipt') is-invalid @enderror" :disabled="isProcessing">

                    <div x-show="isProcessing && processingType === 'new_delivery_receipt'" style="display: none;" class="form-text text-primary mt-1 small">
                        <span class="spinner-border spinner-border-sm me-1"></span> Mengunggah berkas...
                    </div>

                    @error('new_delivery_receipt') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->delivery_receipt)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.delivery_receipt', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>

                {{-- Manual Book --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Manual Book</label>
                    <input type="file" @change="handleFile($event, 'new_manual_book')" class="form-control @error('new_manual_book') is-invalid @enderror" :disabled="isProcessing">

                    <div x-show="isProcessing && processingType === 'new_manual_book'" style="display: none;" class="form-text text-primary mt-1 small">
                        <span class="spinner-border spinner-border-sm me-1"></span> Mengunggah berkas...
                    </div>

                    @error('new_manual_book') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->manual_book)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.manual_book', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>

                {{-- Foto Aset (Ganti 'photo' menjadi 'new_photo') --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Foto Aset</label>
                    <input type="file" @change="handleFile($event, 'new_photo')" class="form-control @error('new_photo') is-invalid @enderror" accept="image/*" capture="environment" :disabled="isProcessing">

                    <div x-show="isProcessing && processingType === 'new_photo'" style="display: none;" class="form-text text-primary mt-1 small">
                        <span class="spinner-border spinner-border-sm me-1"></span> Mengompresi foto...
                    </div>

                    @error('new_photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->photo)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.photo', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>

                {{-- Lampiran --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Lampiran</label>
                    <input type="file" @change="handleFile($event, 'new_attachment')" class="form-control @error('new_attachment') is-invalid @enderror" :disabled="isProcessing">

                    <div x-show="isProcessing && processingType === 'new_attachment'" style="display: none;" class="form-text text-primary mt-1 small">
                        <span class="spinner-border spinner-border-sm me-1"></span> Mengunggah berkas...
                    </div>

                    @error('new_attachment') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->attachment)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.attachment', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-4">
                <label class="form-label">Deskripsi</label>
                <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="2"></textarea>
                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <hr>

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-end gap-2 align-items-center">
                <a href="{{ route('asset.index') }}" class="btn btn-secondary rounded-3 px-3" wire:navigate>
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary rounded-3 d-inline-flex align-items-center justify-content-center px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-save me-1"></i> Update</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...</span>
                </button>
            </div>
        </form>
        @endcomponent
    </div>
</div>