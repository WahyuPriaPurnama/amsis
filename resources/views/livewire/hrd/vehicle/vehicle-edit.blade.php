<div class="container mt-3">
    @component('components.card')
    @slot('header')
    ✏️ EDIT DATA KENDARAAN: {{ $vehicle->jenis_kendaraan }} ({{ $vehicle->nopol }})
    @endslot

    <form wire:submit.prevent="update">
        <div class="row row-cols-1 row-cols-md-5 g-3 mb-3">
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('jenis_kendaraan') is-invalid @enderror"
                        id="jenis_kendaraan" wire:model="jenis_kendaraan" placeholder="Jenis Kendaraan">
                    <label for="jenis_kendaraan">Jenis Kendaraan <span class="text-danger">*</span></label>
                </div>
                @error('jenis_kendaraan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <select wire:model="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror">
                        <option value="">Pilih Kategori</option>
                        <option value="Pribadi">Pribadi</option>
                        <option value="Kantor">Kantor</option>
                        <option value="Umum">Umum</option>
                    </select>
                    <label for="kategori">Kategori <span class="text-danger">*</span></label>
                </div>
                @error('kategori') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <select class="form-select @error('subsidiary_id') is-invalid @enderror" wire:model="subsidiary_id" id="subsidiary_id">
                        <option value="">Pilih Plant</option>
                        @foreach ($subsidiaries as $subsidiary)
                        <option value="{{ $subsidiary->id }}">{{ $subsidiary->name }}</option>
                        @endforeach
                    </select>
                    <label for="subsidiary_id">Plant <span class="text-danger">*</span></label>
                </div>
                @error('subsidiary_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="date" class="form-control @error('tgl_perolehan') is-invalid @enderror"
                        wire:model="tgl_perolehan" id="tgl_perolehan">
                    <label for="tgl_perolehan">Tanggal Perolehan</label>
                </div>
                @error('tgl_perolehan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('pengguna') is-invalid @enderror" wire:model="pengguna" id="pengguna">
                    <label for="pengguna">Pengguna</label>
                </div>
                @error('pengguna') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('nama_warna') is-invalid @enderror" wire:model="nama_warna" id="nama_warna">
                    <label for="nama_warna">Warna</label>
                </div>
                @error('nama_warna') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="color" class="form-control @error('warna') is-invalid @enderror" wire:model="warna" id="warna">
                    <label for="warna">Palet</label>
                </div>
                @error('warna') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="number" class="form-control @error('tahun') is-invalid @enderror" wire:model="tahun" id="tahun">
                    <label for="tahun">Tahun Produksi</label>
                </div>
                @error('tahun') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('atas_nama') is-invalid @enderror" wire:model="atas_nama" id="atas_nama">
                    <label for="atas_nama">Atas Nama</label>
                </div>
                @error('atas_nama') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('nopol') is-invalid @enderror" wire:model="nopol" id="nopol">
                    <label for="nopol">Nopol <span class="text-danger">*</span></label>
                </div>
                @error('nopol') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="date" class="form-control @error('tgl_service') is-invalid @enderror" wire:model="tgl_service" id="tgl_service">
                    <label for="tgl_service">Tanggal Service</label>
                </div>
                @error('tgl_service') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="number" class="form-control @error('km_akhir') is-invalid @enderror" wire:model="km_akhir" id="km_akhir">
                    <label for="km_akhir">Kilometer Akhir</label>
                </div>
                @error('km_akhir') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('no_rangka') is-invalid @enderror" wire:model="no_rangka" id="no_rangka">
                    <label for="no_rangka">No. Rangka</label>
                </div>
                @error('no_rangka') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('no_bpkb') is-invalid @enderror" wire:model="no_bpkb" id="no_bpkb">
                    <label for="no_bpkb">No. BPKB</label>
                </div>
                @error('no_bpkb') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('no_mesin') is-invalid @enderror" wire:model="no_mesin" id="no_mesin">
                    <label for="no_mesin">No. Mesin</label>
                </div>
                @error('no_mesin') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="date" class="form-control @error('stnk') is-invalid @enderror" wire:model="stnk" id="stnk">
                    <label for="stnk">STNK</label>
                </div>
                @error('stnk') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="date" class="form-control @error('pajak') is-invalid @enderror" wire:model="pajak" id="pajak">
                    <label for="pajak">Pajak</label>
                </div>
                @error('pajak') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="date" class="form-control @error('kir') is-invalid @enderror" wire:model="kir" id="kir">
                    <label for="kir">KIR</label>
                </div>
                @error('kir') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('j_asuransi') is-invalid @enderror" wire:model="j_asuransi" id="j_asuransi">
                    <label for="j_asuransi">Jenis Asuransi</label>
                </div>
                @error('j_asuransi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('p_asuransi') is-invalid @enderror" wire:model="p_asuransi" id="p_asuransi">
                    <label for="p_asuransi">Perusahaan Asuransi</label>
                </div>
                @error('p_asuransi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control @error('no_asuransi') is-invalid @enderror" wire:model="no_asuransi" id="no_asuransi">
                    <label for="no_asuransi">No. Asuransi</label>
                </div>
                @error('no_asuransi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="date" class="form-control @error('jth_tempo') is-invalid @enderror" wire:model="jth_tempo" id="jth_tempo">
                    <label for="jth_tempo">Jatuh Tempo</label>
                </div>
                @error('jth_tempo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <select class="form-select @error('kondisi') is-invalid @enderror" wire:model="kondisi" id="kondisi">
                        <option value="">Pilih Kondisi</option>
                        <option value="Baik">Baik</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                    </select>
                    <label for="kondisi">Kondisi</label>
                </div>
                @error('kondisi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <div class="form-floating">
                    <input type="text" wire:model="keterangan" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan">
                    <label for="keterangan">Keterangan</label>
                </div>
                @error('keterangan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>

        <h5 class="fw-bold mt-4">Ganti Lampiran Berkas (Kosongkan jika tidak diubah)</h5>
        <hr>

        <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
            <div class="col">
                <label for="new_foto" class="form-label fw-semibold">Foto Kendaraan</label>
                <input type="file" wire:model="new_foto" id="new_foto" class="form-control @error('new_foto') is-invalid @enderror" accept="image/*">
                @error('new_foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <label for="new_f_stnk" class="form-label fw-semibold">STNK</label>
                <input type="file" wire:model="new_f_stnk" id="new_f_stnk" class="form-control @error('new_f_stnk') is-invalid @enderror">
                @error('new_f_stnk') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <label for="new_f_pajak" class="form-label fw-semibold">Pajak</label>
                <input type="file" wire:model="new_f_pajak" id="new_f_pajak" class="form-control @error('new_f_pajak') is-invalid @enderror">
                @error('new_f_pajak') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <label for="new_f_kir" class="form-label fw-semibold">KIR</label>
                <input type="file" wire:model="new_f_kir" id="new_f_kir" class="form-control @error('new_f_kir') is-invalid @enderror">
                @error('new_f_kir') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <label for="new_qr" class="form-label fw-semibold">QR Code BBM Subsidi</label>
                <input type="file" wire:model="new_qr" id="new_qr" class="form-control @error('new_qr') is-invalid @enderror">
                @error('new_qr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <label for="new_f_polis" class="form-label fw-semibold">Polis Asuransi</label>
                <input type="file" wire:model="new_f_polis" id="new_f_polis" class="form-control @error('new_f_polis') is-invalid @enderror">
                @error('new_f_polis') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col">
                <label for="new_f_service" class="form-label fw-semibold">Bukti Service</label>
                <input type="file" wire:model="new_f_service" id="new_f_service" class="form-control @error('new_f_service') is-invalid @enderror">
                @error('new_f_service') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-secondary" wire:navigate>Batal</a>
            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="bi bi-save me-1"></i> Perbarui Data</span>
                <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Memproses...</span>
            </button>
        </div>
    </form>
    @endcomponent
</div>