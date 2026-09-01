<div class="container mt-3">
    @component('components.card')
    @slot('header')
    EDIT DATA KARYAWAN
    @endslot

    <form wire:submit.prevent="update">
        {{-- SECTION 1: ORGANISASI --}}
        <div class="row mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <h4 class="fw-semibold mb-0">Organisasi</h4>

                @if (!$canEditOrg)
                <div class="alert alert-warning py-1 px-3 mb-0 small">
                    <i class="bi bi-info-circle-fill me-1"></i> Hanya admin/leader yang dapat merubah data Organisasi
                </div>
                @endif

                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary btn-sm ms-auto" wire:navigate>
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
            </div>
            <hr>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="nip" class="form-label">NIP</label>
                <input type="text" id="nip" wire:model="nip" class="form-control @error('nip') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="nama" wire:model="nama" class="form-control @error('nama') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="subsidiary_id" class="form-label">Plant <span class="text-danger">*</span></label>
                <select class="form-select @error('subsidiary_id') is-invalid @enderror" wire:model="subsidiary_id" id="subsidiary_id" @disabled(!$canEditOrg)>
                    <option value="">Pilih Plant</option>
                    @foreach ($subsidiaries ?? [] as $subsidiary)
                    <option value="{{ $subsidiary->id }}">{{ $subsidiary->name }}</option>
                    @endforeach
                </select>
                @error('subsidiary_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="divisi" class="form-label">Divisi</label>
                <input type="text" id="divisi" wire:model="divisi" class="form-control @error('divisi') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="departemen" class="form-label">Departemen</label>
                <input type="text" id="departemen" wire:model="departemen" class="form-control @error('departemen') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="seksi" class="form-label">Seksi</label>
                <input type="text" id="seksi" wire:model="seksi" class="form-control @error('seksi') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('seksi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="posisi" class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" id="posisi" wire:model="posisi" class="form-control @error('posisi') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('posisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="tgl_masuk" class="form-label">Tanggal Masuk Kerja</label>
                <input type="date" id="tgl_masuk" wire:model="tgl_masuk" class="form-control @error('tgl_masuk') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('tgl_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="status_peg" class="form-label">Status Pegawai <span class="text-danger">*</span></label>
                <select wire:model.live="status_peg" id="status_peg" class="form-select @error('status_peg') is-invalid @enderror" @disabled(!$canEditOrg)>
                    <option value="">Pilih Status</option>
                    <option value="PKWT">PKWT</option>
                    <option value="PKWTT">PKWTT</option>
                    <option value="-">-</option>
                </select>
                @error('status_peg') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            @if ($status_peg === 'PKWT')
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <label for="awal_kontrak" class="form-label">Awal Kontrak</label>
                <input type="date" id="awal_kontrak" wire:model="awal_kontrak" class="form-control @error('awal_kontrak') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('awal_kontrak') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <label for="akhir_kontrak" class="form-label">Akhir Kontrak</label>
                <input type="date" id="akhir_kontrak" wire:model="akhir_kontrak" class="form-control @error('akhir_kontrak') is-invalid @enderror" @readonly(!$canEditOrg)>
                @error('akhir_kontrak') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @endif
        </div>

        {{-- SECTION 2: BIODATA --}}
        <div class="row mb-3 pt-3">
            <div class="col-12">
                <h4 class="fw-semibold mb-2">Biodata</h4>
                <hr>
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" id="nik" wire:model="nik" class="form-control @error('nik') is-invalid @enderror">
                @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-4 mb-3">
                <label for="tmpt_lahir" class="form-label">Tempat Lahir</label>
                <input type="text" id="tmpt_lahir" wire:model="tmpt_lahir" class="form-control @error('tmpt_lahir') is-invalid @enderror">
                @error('tmpt_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-5 mb-3">
                <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date" id="tgl_lahir" wire:model="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror">
                @error('tgl_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-4 mb-3">
                <label class="form-label d-block">Jenis Kelamin</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" wire:model="jenis_kelamin" id="laki_laki" value="L">
                    <label class="form-check-label" for="laki_laki">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" wire:model="jenis_kelamin" id="perempuan" value="P">
                    <label class="form-check-label" for="perempuan">Perempuan</label>
                </div>
                @error('jenis_kelamin') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea id="alamat" rows="3" wire:model="alamat" class="form-control @error('alamat') is-invalid @enderror"></textarea>
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-4 col-md-2 mb-3">
                <label for="no_telp" class="form-label">No. Telp / WA</label>
                <input type="text" id="no_telp" wire:model="no_telp" class="form-control @error('no_telp') is-invalid @enderror">
                @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 col-md-3 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 col-md-2 mb-3">
                <label for="pend_trkhr" class="form-label">Pendidikan</label>
                <select id="pend_trkhr" wire:model="pend_trkhr" class="form-select @error('pend_trkhr') is-invalid @enderror">
                    <option value="">Pilih</option>
                    @foreach (['SD', 'SMP', 'SMA', 'Diploma', 'Sarjana', 'Magister', 'Doktor'] as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
                @error('pend_trkhr') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 col-md-3 mb-3">
                <label for="jurusan" class="form-label">Jurusan</label>
                <input type="text" id="jurusan" wire:model="jurusan" class="form-control @error('jurusan') is-invalid @enderror">
                @error('jurusan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 col-md-2 mb-3">
                <label for="thn_lulus" class="form-label">Tahun Lulus</label>
                <input type="text" id="thn_lulus" wire:model="thn_lulus" class="form-control @error('thn_lulus') is-invalid @enderror">
                @error('thn_lulus') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-4 mb-3">
                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                <input type="text" id="nama_ibu" wire:model="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror">
                @error('nama_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col mb-3">
                <label for="npwp" class="form-label">NPWP</label>
                <input type="text" id="npwp" wire:model="npwp" class="form-control @error('npwp') is-invalid @enderror">
                @error('npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-3 mb-3">
                <label for="status" class="form-label">Status Pernikahan</label>
                <select id="status" wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="">Pilih Status</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Cerai">Cerai</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            @if ($status === 'Kawin' || $status === 'Cerai')
            <div class="col-sm-2 mb-3">
                <label for="jml_ank" class="form-label">Anak</label>
                <input type="number" id="jml_ank" wire:model="jml_ank" class="form-control @error('jml_ank') is-invalid @enderror" min="0">
                @error('jml_ank') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @endif
        </div>

        {{-- SECTION 3: KONTAK DARURAT --}}
        <div class="row mb-3 pt-3">
            <div class="col-12">
                <h4 class="fw-semibold mb-2">Kontak Darurat</h4>
                <hr>
            </div>

            <div class="col-md-4 mb-3">
                <label for="nama_kd" class="form-label">Nama Kontak Darurat</label>
                <input type="text" id="nama_kd" wire:model="nama_kd" class="form-control @error('nama_kd') is-invalid @enderror">
                @error('nama_kd') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="no_kd" class="form-label">No. Kontak Darurat</label>
                <input type="text" id="no_kd" wire:model="no_kd" class="form-control @error('no_kd') is-invalid @enderror">
                @error('no_kd') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="hubungan" class="form-label">Hubungan</label>
                <input type="text" id="hubungan" wire:model="hubungan" class="form-control @error('hubungan') is-invalid @enderror">
                @error('hubungan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- SECTION 4: LAMPIRAN --}}
        <div class="row mb-3 pt-3">
            <div class="col-12">
                <h4 class="fw-semibold mb-2">Lampiran Berkas</h4>
                <hr>
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_pp" class="form-label">Foto Profil (Ubah)</label>
                <input type="file" id="new_pp" wire:model="new_pp" class="form-control @error('new_pp') is-invalid @enderror" accept="image/*">
                @error('new_pp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->pp) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_ktp" class="form-label">KTP (Ubah PDF)</label>
                <input type="file" id="new_ktp" wire:model="new_ktp" class="form-control @error('new_ktp') is-invalid @enderror" accept="application/pdf">
                @error('new_ktp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->ktp) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_kk" class="form-label">Kartu Keluarga (Ubah PDF)</label>
                <input type="file" id="new_kk" wire:model="new_kk" class="form-control @error('new_kk') is-invalid @enderror" accept="application/pdf">
                @error('new_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->kk) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_npwp2" class="form-label">NPWP (Ubah PDF)</label>
                <input type="file" id="new_npwp2" wire:model="new_npwp2" class="form-control @error('new_npwp2') is-invalid @enderror" accept="application/pdf">
                @error('new_npwp2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->npwp2) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_bpjs_kes" class="form-label">BPJS Kesehatan (Ubah PDF)</label>
                <input type="file" id="new_bpjs_kes" wire:model="new_bpjs_kes" class="form-control @error('new_bpjs_kes') is-invalid @enderror" accept="application/pdf">
                @error('new_bpjs_kes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->bpjs_kes) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_bpjs_ket" class="form-label">BPJS Ketenagakerjaan (Ubah PDF)</label>
                <input type="file" id="new_bpjs_ket" wire:model="new_bpjs_ket" class="form-control @error('new_bpjs_ket') is-invalid @enderror" accept="application/pdf">
                @error('new_bpjs_ket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->bpjs_ket) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>

            <div class="col-md-3 mb-3">
                <label for="new_ttd" class="form-label">Tanda Tangan (Ubah Image)</label>
                <input type="file" id="new_ttd" wire:model="new_ttd" class="form-control @error('new_ttd') is-invalid @enderror" accept="image/*">
                @error('new_ttd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($employee->ttd) <div class="form-text text-success"><i class="bi bi-check-circle-fill me-1"></i> File terlampir</div> @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="bi bi-check-circle me-1"></i> Perbarui Data</span>
                <span wire:loading><span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...</span>
            </button>
        </div>
    </form>
    @endcomponent
</div>