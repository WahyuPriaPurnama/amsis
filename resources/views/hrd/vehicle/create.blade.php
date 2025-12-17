@extends('layouts.app')
@section('title', 'Data Kendaraan')
@section('menuVehicles', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                TAMBAH DATA KENDARAAN
            @endslot
            <form action="{{ route('vehicles.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row row-cols-1 row-cols-md-5 g-3 mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('jenis_kendaraan') is-invalid @enderror"
                                id="jenis_kendaraan" name="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}"
                                placeholder="Jenis Kendaraan">
                            <label class="form-label" for="jenis_kendaraan">Jenis Kendaraan</label>
                        </div>
                        @error('jenis_kendaraan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror">
                                <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori</option>
                                <option value="Pribadi" {{ old('kategori') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                                <option value="Kantor" {{ old('kategori') == 'Kantor' ? 'selected' : '' }}>Kantor</option>
                                <option value="Umum" {{ old('kategori') == 'Umum' ? 'selected' : '' }}>Umum</option>
                            </select>
                            <label class="form-label" for="kategori">Kategori</label>
                        </div>
                    </div>
                    @error('kategori')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="col">
                        <div class="form-floating">
                            <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                                id="subsidiary_id">
                                <option value=""{{ old('subsidiary_id') == '' ?: 'selected' }}>Pilih Plant
                                </option>
                                @foreach ($sub as $subsidiary)
                                    <option value="{{ $subsidiary->id }}"
                                        {{ old('subsidiary_id') == $subsidiary->id ? 'selected' : '' }}>
                                        {{ $subsidiary->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label" for="subsidiary_id">Plant</label>
                        </div>
                        @error('subsidiary_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('tgl_perolehan') is-invalid @enderror"
                                name="tgl_perolehan" value="{{ old('tgl_perolehan') }}" id="tgl_perolehan"
                                placeholder="Tanggal Perolehan">
                            <label class="form-label" for="tgl_perolehan">Tanggal Perolehan</label>
                        </div>
                        @error('tgl_perolehan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('pengguna') is-invalid @enderror" name="pengguna"
                                value="{{ old('pengguna') }}" id="pengguna" placeholder="Pengguna Kendaraan">
                            <label class="form-label" for="pengguna">Pengguna</label>
                        </div>
                        @error('pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('nama_warna') is-invalid @enderror"
                                name="nama_warna" value="{{ old('nama_warna') }}" id="nama_warna"
                                placeholder="Warna Kendaraan">
                            <label class="form-label" for="nama_warna">Warna</label>
                        </div>
                        @error('nama_warna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="color" class="form-control @error('warna') is-invalid @enderror" name="warna"
                                value="{{ old('warna') }}" id="warna">
                            <label class="form-label" for="warna">Palet</label>
                        </div>
                        @error('warna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" name="tahun"
                                value="{{ old('tahun') }}" id="tahun" placeholder="Tahun Produksi">
                            <label class="form-label" for="tahun">Tahun Produksi</label>
                        </div>
                        @error('tahun')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('atas_nama') is-invalid @enderror" name="atas_nama"
                                value="{{ old('atas_nama') }}" id="atas_nama" placeholder="Atas Nama">
                            <label class="form-label" for="atas_nama">Atas Nama</label>
                        </div>
                        @error('atas_nama')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('nopol') is-invalid @enderror" name="nopol"
                                value="{{ old('nopol') }}" id="nopol" placeholder="N 4441 QU">
                            <label class="form-label" for="nopol">Nopol</label>
                        </div>
                        @error('nopol')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('tgl_service') is-invalid @enderror"
                                name="tgl_service" value="{{ old('tgl_service') }}" id="tgl_service"
                                placeholder="Tanggal Service">
                            <label class="form-label" for="tgl_service">Tanggal Service</label>
                        </div>
                        @error('tgl_service')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('km_akhir') is-invalid @enderror"
                                value="{{ old('km_akhir') }}" placeholder="kilometer" name="km_akhir" id="km_akhir">
                            <label class="form-label" for="km_akhir">Kilometer Akhir</label>
                        </div>
                        @error('km_akhir')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_rangka') is-invalid @enderror"
                                name="no_rangka" value="{{ old('no_rangka') }}" id="no_rangka" placeholder="No. Rangka">
                            <label class="form-label" for="no_rangka">No. Rangka</label>
                        </div>
                        @error('no_rangka')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_bpkb') is-invalid @enderror" name="no_bpkb"
                                value="{{ old('no_bpkb') }}" id="no_bpkb" placeholder="No. BPKB">
                            <label class="form-label" for="no_bpkb">No. BPKB</label>
                        </div>
                        @error('no_bpkb')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_mesin') is-invalid @enderror"
                                name="no_mesin" value="{{ old('no_mesin') }}" id="no_mesin" placeholder="No. Mesin">
                            <label class="form-label" for="no_mesin">No. Mesin</label>
                        </div>
                        @error('no_mesin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('stnk') is-invalid @enderror" name="stnk"
                                value="{{ old('stnk') }}" id="stnk" placeholder="masa berlaku STNK">
                            <label class="form-label" for="stnk">STNK</label>
                        </div>
                        @error('stnk')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('pajak') is-invalid @enderror" name="pajak"
                                value="{{ old('pajak') }}" id="pajak" placeholder="masa berlaku Pajak">
                            <label class="form-label" for="pajak">Pajak</label>
                        </div>
                        @error('pajak')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('kir') is-invalid @enderror" name="kir"
                                value="{{ old('kir') }}" id="kir" placeholder="masa berlaku KIR">
                            <label class="form-label" for="kir">KIR</label>
                        </div>
                        @error('kir')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('j_asuransi') is-invalid @enderror"
                                name="j_asuransi" value="{{ old('j_asuransi') }}" id="j_asuransi"
                                placeholder="Jenis Asuransi">
                            <label class="form-label" for="j_asuransi">Jenis Asuransi</label>
                        </div>
                        @error('j_asuransi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('p_asuransi') is-invalid @enderror"
                                name="p_asuransi" value="{{ old('p_asuransi') }}" id="p_asuransi"
                                placeholder="Perusahaan Asuransi">
                            <label class="form-label" for="p_asuransi">Perusahaan Asuransi</label>
                        </div>
                        @error('p_asuransi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_asuransi') is-invalid @enderror"
                                name="no_asuransi" value="{{ old('no_asuransi') }}" id="no_asuransi"
                                placeholder="No. Asuransi">
                            <label class="form-label" for="no_asuransi">No. Asuransi</label>
                        </div>
                        @error('no_asuransi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('jth_tempo') is-invalid @enderror"
                                name="jth_tempo" value="{{ old('jth_tempo') }}" id="jth_tempo" placeholder="Jatuh Tempo">
                            <label class="form-label" for="jth_tempo">Jatuh Tempo</label>
                        </div>
                        @error('jth_tempo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <select class="form-select @error('kondisi') is-invalid @enderror" name="kondisi"
                                id="kondisi">
                                <option selected value=""{{ old('kondisi') ? '' : 'selected' }}>Pilih Kondisi</option>
                                <option value="Baik"{{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Kurang Baik"{{ old('kondisi') == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik
                                </option>
                            </select>
                            <label class="form-label" for="kondisi">Kondisi</label>
                        </div>
                        @error('kondisi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" name="keterangan"
                                class="form-control @error('keterangan') is-invalid @enderror" id="keterangan"
                                placeholder="Keterangan">
                            <label class="form-label" for="keterangan">Keterangan</label>
                        </div>
                        @error('keterangan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <h3>Lampiran</h3>
                <hr>
                <div class="row row-cols-1 row-cols-md-5 g-3 mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="foto" id="foto"
                                class="form-control @error('foto') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg">
                            <label class="form-label" for="foto">Foto Kendaraan</label>
                        </div>
                        @error('foto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_stnk" id="f_stnk"
                                class="form-control @error('f_stnk') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label class="form-label" for="f_stnk">STNK</label>
                        </div>
                        @error('stnk')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_pajak" id="f_pajak"
                                class="form-control @error('f_pajak') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label class="form-label" for="f_pajak">Pajak</label>
                        </div>
                        @error('f_pajak')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_kir" id="f_kir"
                                class="form-control @error('f_kir') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label class="form-label" for="f_kir">KIR</label>
                        </div>
                        @error('f_kir')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="qr" id="qr"
                                class="form-control @error('qr') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label class="form-label" for="qr">QR Code BBM Subsidi</label>
                        </div>
                        @error('qr')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_polis" id="polis"
                                class="form-control @error('polis') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label class="form-label" for="polis">Polis Asuransi</label>
                        </div>
                        @error('polis')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_service" id="f_service"
                                class="form-control @error('f_service') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label class="form-label" for="f_service">Bukti Service</label>
                        </div>
                    </div>
                </div>
                <x-buttons.submit></x-buttons.submit>
            </form>
        @endcomponent
    </div>
@endsection
