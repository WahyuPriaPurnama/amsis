@extends('layouts.app')
@section('title', 'Input Data Karyawan')
@section('menuEmployees', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                INPUT DATA KARYAWAN
            @endslot
            <form action="{{ route('employees.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="row align-items-center mb-2 text-center text-md-start">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <h4 class="fw-semibold mb-0">Organisasi</h4>
                        </div>
                    </div>
                    <hr>

                    <div class="col-12 col-sm-6 col-md-2 mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                            class="form-control @error('nip') is-invalid @enderror" aria-describedby="nipHelp"
                            placeholder="123456789">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-2 mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                            class="form-control @error('nama') is-invalid @enderror" aria-describedby="namaHelp"
                            placeholder="sesuai KTP">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik') }}"
                            class="form-control @error('nik') is-invalid @enderror" placeholder="NIK KTP"
                            aria-describedby="nikHelp">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="subsidiary_id" class="form-label">Plant</label>
                        <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                            id="subsidiary_id">
                            <option value="" {{ old('subsidiary_id') == '' ? 'selected' : '' }}>Pilih Plant</option>
                            @foreach ($subsidiaries as $subsidiary)
                                <option
                                    value="{{ $subsidiary->id }}"{{ old('subsidiary_id') == $subsidiary->id ? 'selected' : '' }}>
                                    {{ $subsidiary->name }}</option>
                            @endforeach
                        </select>
                        @error('subsidiary_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-md-2 mb-3">
                        <label for="divisi" class="form-label">Divisi</label>
                        <input type="text" name="divisi" id="divisi"
                            class="form-control @error('divisi') is-invalid @enderror" value="{{ old('divisi') }}">
                        @error('divisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="departemen" class="form-label">Departemen</label>
                        <input type="text" name="departemen" id="departemen"
                            class="form-control @error('departemen') is-invalid @enderror" value="{{ old('departemen') }}">
                        @error('departemen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="seksi" class="form-label">Seksi</label>
                        <input type="text" name="seksi" id="seksi"
                            class="form-control @error('seksi') is-invalid @enderror" value="{{ old('seksi') }}">
                        @error('seksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="posisi" class="form-label">Posisi</label>
                        <input type="text" class="form-control @error('posisi') is-invalid @enderror"
                            value="{{ old('posisi') }}">
                        @error('posisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="tgl_masuk" class="form-label">Tanggal Masuk Kerja</label>
                        <input type="date" id="tgl_masuk" name="tgl_masuk" value="{{ old('tgl_masuk') }}"
                            class="form-control @error('tgl_masuk') is-invalid @enderror">
                        @error('tgl_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div x-data="{
                    statusPeg: '{{ old('status_peg') }}',
                    awalKontrak: '{{ old('awal_kontrak') }}',
                    akhirKontrak: '{{ old('akhir_kontrak') }}',
                    resetKontrak() {
                        if (this.statusPeg !== 'PKWT') {
                            this.awalKontrak = '';
                            this.akhirKontrak = '';
                        }
                    }
                }" x-init="resetKontrak" x-watch="statusPeg" class="row mb-3">
                    {{-- Status Pegawai --}}
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="status_peg" class="form-label">Status Pegawai</label>
                        <select name="status_peg" id="status_peg"
                            class="form-select @error('status_peg') is-invalid @enderror" x-model="statusPeg">
                            <option value="">Pilih Status</option>
                            @foreach (['PKWT', 'PKWTT', '-'] as $status)
                                <option value="{{ $status }}" @selected(old('status_peg') === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_peg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Awal Kontrak --}}
                    <div class="col mb-3" x-show="statusPeg === 'PKWT'">
                        <label for="awal_kontrak" class="form-label">Awal Kontrak</label>
                        <input type="date" id="awal_kontrak" name="awal_kontrak" x-model="awalKontrak"
                            :value="awalKontrak" class="form-control @error('awal_kontrak') is-invalid @enderror"
                            aria-describedby="kontrakHelp">
                        @error('awal_kontrak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Akhir Kontrak --}}
                    <div class="col mb-3" x-show="statusPeg === 'PKWT'">
                        <label for="akhir_kontrak" class="form-label">Akhir Kontrak</label>
                        <input type="date" id="akhir_kontrak" name="akhir_kontrak" x-model="akhirKontrak"
                            :value="akhirKontrak" class="form-control @error('akhir_kontrak') is-invalid @enderror"
                            aria-describedby="kontrakHelp">
                        @error('akhir_kontrak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3 pt-3">
                    <div class="col-12">
                        <h3 class="mb-2">Biodata</h3>
                        <hr>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="tmpt_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" id="tmpt_lahir" name="tmpt_lahir" value="{{ old('tmpt_lahir') }}"
                            class="form-control @error('tmpt_lahir') is-invalid @enderror">
                        @error('tmpt_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir" value="{{ old('tgl_lahir') }}"
                            class="form-control @error('tgl_lahir') is-invalid @enderror">
                        @error('tgl_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label d-block">Jenis Kelamin</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio"
                                name="jenis_kelamin" id="laki_laki" value="L" @checked(old('jenis_kelamin') == 'L')>
                            <label class="form-check-label" for="laki_laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio"
                                name="jenis_kelamin" id="perempuan" value="P" @checked(old('jenis_kelamin') == 'P')>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" rows="3" name="alamat">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 col-md-2 mb-3">
                        <label class="form-label" for="no_telp">No. Telp / WhatsApp</label>
                        <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp') }}"
                            class="form-control @error('no_telp') is-invalid @enderror">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-3 mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="text" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-2 mb-3">
                        <label class="form-label" for="pend_trkhr">Pendidikan Terakhir</label>
                        <select name="pend_trkhr" id="pend_trkhr"
                            class="form-select @error('pend_trkhr') is-invalid @enderror">
                            <option value="">Pilih Pendidikan</option>
                            @foreach (['SD', 'SMP', 'SMA', 'Diploma', 'Sarjana', 'Magister', 'Doktor'] as $pendidikan)
                                <option value="{{ $pendidikan }}" @selected(old('pend_trkhr') === $pendidikan)>
                                    {{ $pendidikan }}
                                </option>
                            @endforeach
                        </select>
                        @error('pend_trkhr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-3 mb-3">
                        <label class="form-label" for="jurusan">Jurusan</label>
                        <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan') }}"
                            class="form-control @error('jurusan') is-invalid @enderror">
                        @error('jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-2 mb-3">
                        <label class="form-label" for="thn_lulus">Tahun Lulus</label>
                        <input type="text" id="thn_lulus" name="thn_lulus" value="{{ old('thn_lulus') }}"
                            class="form-control @error('thn_lulus') is-invalid @enderror">
                        @error('thn_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div x-data="{ statusNikah: '{{ old('status') }}' }" class="row mb-3">
                    <div class="col-sm-4 mb-3">
                        <label class="form-label" for="nama_ibu">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}"
                            class="form-control @error('nama_ibu') is-invalid @enderror">
                        @error('nama_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="npwp">NPWP</label>
                        <input type="text" id="npwp" name="npwp" value="{{ old('npwp') }}"
                            class="form-control @error('npwp') is-invalid @enderror">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Status Pernikahan --}}
                    <div class="col-sm-3 mb-3">
                        <label class="form-label" for="status">Status Pernikahan</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                            x-model="statusNikah">
                            <option value="">Pilih</option>
                            @foreach (['Kawin', 'Belum Kawin', 'Cerai'] as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- tampil input jumlah anak jika kawin --}}
                    <template x-if="statusNikah ==='Kawin' || statusNikah==='Cerai'">
                        <div class="col-sm-1 mb-3">
                            <label class="form-label" for="jml_ank">Anak</label>
                            <input type="text" id="jml_ank" name="jml_ank" value="{{ old('jml_ank') }}"
                                class="form-control @error('jml_ank') is-invalid @enderror">
                            @error('jml_ank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </template>
                    <template x-if="statusNikah !== 'Kawin' && statusNikah !== 'Cerai'">
                        <input type="hidden" name="jml_ank" value="0">
                    </template>
                </div>
                <div class="row mb-3 pt-3">
                    <h3>Kontak Darurat</h3>
                    <hr>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="nama_kd">Nama Kontak Darurat</label>
                        <input type="text" id="nama_ibu" name="nama_kd" value="{{ old('nama_kd') }}"
                            class="form-control @error('nama_kd') is-invalid @enderror">
                        @error('nama_kd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="no_kd">No. Kontak Darurat</label>
                        <input type="text" id="no_kd" name="no_kd" value="{{ old('no_kd') }}"
                            class="form-control @error('no_kd') is-invalid @enderror">
                        @error('no_kd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="hubungan">Hubungan</label>
                        <input type="text" id="hubungan" name="hubungan" value="{{ old('hubungan') }}"
                            class="form-control @error('hubungan') is-invalid @enderror">
                        @error('hubungan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3 pt-3">
                    <h3>Lampiran</h3>
                    <hr>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="pp">Foto Profil</label>
                        <input type="file" id="pp" name="pp"
                            class="form-control @error('pp') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg">
                        @error('pp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="ktp">KTP</label>
                        <input type="file" id="ktp" name="ktp"
                            class="form-control @error('ktp') is-invalid @enderror" accept="application/pdf">
                        @error('ktp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="kk">Kartu Keluarga</label>
                        <input type="file" id="kk" name="kk"
                            class="form-control @error('kk') is-invalid @enderror" accept="application/pdf">
                        @error('kk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="npwp2">NPWP</label>
                        <input type="file" id="npwp2" name="npwp2"
                            class="form-control @error('npwp2') is-invalid @enderror" accept="application/pdf">
                        @error('npwp2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="bpjs_kes">BPJS Kesehatan</label>
                        <input type="file" id="bpjs_kes" name="bpjs_kes"
                            class="form-control @error('bpjs_kes') is-invalid @enderror" accept="application/pdf">
                        @error('bpjs_kes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="bpjs_ket">BPJS Ketenagakerjaan</label>
                        <input type="file" id="bpjs_ket" name="bpjs_ket"
                            class="form-control @error('bpjs_ket') is-invalid @enderror" accept="application/pdf">
                        @error('bpjs_ket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <x-buttons.submit>
                </x-buttons.submit>
            </form>
        @endcomponent
    </div>
@endsection
