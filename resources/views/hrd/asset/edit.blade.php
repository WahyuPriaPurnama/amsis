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

            {{-- Baris 4: Nilai & Masa Manfaat --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Nilai Pembelian</label>
                    <input type="number" step="0.01" wire:model="purchase_value" class="form-control @error('purchase_value') is-invalid @enderror">
                    @error('purchase_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Nilai Penyusutan</label>
                    <input type="number" step="0.01" wire:model="depreciation_value" class="form-control @error('depreciation_value') is-invalid @enderror">
                    @error('depreciation_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Total Nilai</label>
                    <input type="number" step="0.01" wire:model="total_value" class="form-control @error('total_value') is-invalid @enderror">
                    @error('total_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Masa Manfaat (bulan)</label>
                    <input type="number" wire:model="useful_life" class="form-control @error('useful_life') is-invalid @enderror">
                    @error('useful_life') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Baris 5: File Uploads --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Tanda Terima</label>
                    <input type="file" wire:model="new_delivery_receipt" class="form-control @error('new_delivery_receipt') is-invalid @enderror">
                    @error('new_delivery_receipt') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->delivery_receipt)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.delivery_receipt', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Manual Book</label>
                    <input type="file" wire:model="new_manual_book" class="form-control @error('new_manual_book') is-invalid @enderror">
                    @error('new_manual_book') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->manual_book)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.manual_book', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Foto Aset</label>
                    <input type="file" wire:model="new_photo" class="form-control @error('new_photo') is-invalid @enderror" accept="image/*">
                    @error('new_photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if ($asset->photo)
                    <small class="d-block mt-1 text-muted">
                        File saat ini: <a href="{{ route('asset.photo', $asset->id) }}" target="_blank">Lihat Berkas</a>
                    </small>
                    @endif
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Lampiran</label>
                    <input type="file" wire:model="new_attachment" class="form-control @error('new_attachment') is-invalid @enderror">
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