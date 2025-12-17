@extends('layouts.app')
@section('title', 'Data Kendaraan')
@section('menuVehicles', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                EDIT {{ $vehicle->jenis_kendaraan }}
            @endslot
            <form action="{{ route('vehicles.update', ['vehicle' => $vehicle->id]) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row row-cols-1 row-cols-md-5 g-3 mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" id="jenis_kendaraan" name="jenis_kendaraan"
                                value="{{ old('jenis_kendaraan') ?? $vehicle->jenis_kendaraan }}"
                                class="form-control @error('jenis_kendaraan') is-invalid @enderror" id="jenis_kendaraan">
                            <label class="form-label" for="jenis_kendaraan">Jenis Kendaraan</label>
                        </div>
                        @error('jenis_kendaraan')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror">
                                <option value="" selected>Pilih Kategori</option>
                                <option value="Pribadi" @selected($vehicle->kategori == 'Pribadi')>Pribadi</option>
                                <option value="Kantor" @selected($vehicle->kategori == 'Kantor')>Kantor</option>
                                <option value="Umum" @selected($vehicle->kategori == 'Umum')>Umum</option>
                            </select>
                            <label for="kategori" class="form-label">Kategori</label>
                        </div>
                        @error('kategori')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <select class="form-select @error('subsidiary_id') is-invalid @enderror" name="subsidiary_id"
                                id="subsidiary_id">
                                @foreach ($sub as $subsidiary)
                                    <option value="{{ $subsidiary->id }}" @selected($subsidiary->id == $vehicle->subsidiary_id)>{{ $subsidiary->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label" for="subsidiary_id">Plant</label>
                        </div>
                        @error('subsidiary_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" id="tgl_perolehan"
                                class="form-control @error('tgl_perolehan') is-invalid @enderror" name="tgl_perolehan"
                                value="{{ old('tgl_perolehan') ?? $vehicle->tgl_perolehan }}">
                            <label class="form-label" for="tgl_perolehan">Tanggal Perolehan</label>
                        </div>
                        @error('tgl_perolehan')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('pengguna') is-invalid @enderror" name="pengguna"
                                value="{{ old('pengguna') ?? $vehicle->pengguna }}" id="pengguna">
                            <label class="form-label" for="pengguna">Pengguna</label>
                        </div>
                        @error('pengguna')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('nama_warna') is-invalid @enderror"
                                name="nama_warna" value="{{ old('nama_warna') ?? $vehicle->nama_warna }}" id="nama_warna">
                            <label class="form-label" for="nama_warna">Warna</label>
                        </div>
                        @error('nama_warna')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="color" class="form-control @error('warna') is-invalid @enderror" name="warna"
                                value="{{ old('warna') ?? $vehicle->warna }}" id="warna">
                            <label class="form-label" for="warna">Palet</label>
                        </div>
                        @error('warna')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" name="tahun"
                                placeholder="2022" value="{{ old('tahun') ?? $vehicle->tahun }}" id="tahun">
                            <label class="form-label" for="tahun">Tahun Produksi</label>
                        </div>
                        @error('tahun')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('atas_nama') is-invalid @enderror" name="atas_nama"
                                value="{{ old('atas_nama') ?? $vehicle->atas_nama }}" id="atas_nama">
                            <label class="form-label" for="atas_nama">Atas Nama</label>
                        </div>
                        @error('atas_nama')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('nopol') is-invalid @enderror" name="nopol"
                                value="{{ old('nopol') ?? $vehicle->nopol }}" id="nopol">
                            <label class="form-label" for="nopol">Nopol</label>
                        </div>
                        @error('nopol')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" value="{{ old('tgl_service')?? $vehicle->tgl_service }}" name="tgl_service"
                                class="form-control @error('tgl_service') is-invalid @enderror" id="tgl_service">
                            <label for="tgl_service" class="form-label">Tgl Service</label>
                        </div>
                        @error('tgl_service')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('km_akhir') is-invalid @enderror"
                                name="km_akhir" value="{{ old('km_akhir') ?? $vehicle->km_akhir }}" id="km_akhir">
                            <label class="form-label" for="km_akhir">KM</label>
                        </div>
                        @error('km_akhir')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_rangka') is-invalid @enderror"
                                name="no_rangka" value="{{ old('no_rangka') ?? $vehicle->no_rangka }}" id="no_rangka">
                            <label class="form-label" for="no_rangka">No. Rangka</label>
                        </div>
                        @error('no_rangka')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_bpkb') is-invalid @enderror" name="no_bpkb"
                                value="{{ old('no_bpkb') ?? $vehicle->no_bpkb }}" id="">
                            <label class="form-label" for="no_bpkb">No. BPKB</label>
                        </div>
                        @error('no_bpkb')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_mesin') is-invalid @enderror"
                                name="no_mesin" value="{{ old('no_mesin') ?? $vehicle->no_mesin }}" id="no_mesin">
                            <label class="form-label" for="no_mesin">No. Mesin</label>
                        </div>
                        @error('no_mesin')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('stnk') is-invalid @enderror" name="stnk"
                                value="{{ old('stnk') ?? $vehicle->stnk }}" id="stnk">
                            <label class="form-label" for="stnk">STNK</label>
                        </div>
                        @error('stnk')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('pajak') is-invalid @enderror" name="pajak"
                                value="{{ old('pajak') ?? $vehicle->pajak }}" id="pajak">
                            <label class="form-label" for="pajak">Pajak</label>
                        </div>
                        @error('pajak')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('kir') is-invalid @enderror" name="kir"
                                value="{{ old('kir') ?? $vehicle->kir }}" id="kir">
                            <label class="form-label" for="kir">KIR</label>
                        </div>
                        @error('kir')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('j_asuransi') is-invalid @enderror"
                                name="j_asuransi" value="{{ old('j_asuransi') ?? $vehicle->j_asuransi }}" id="j_asuransi">
                            <label class="form-label" for="j_asuransi">Jenis Asuransi</label>
                        </div>
                        @error('j_asuransi')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('p_asuransi') is-invalid @enderror"
                                name="p_asuransi" value="{{ old('p_asuransi') ?? $vehicle->p_asuransi }}" id="p_asuransi">
                            <label class="form-label" for="p_asuransi">Perusahaan Asuransi</label>
                        </div>
                        @error('p_asuransi')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('no_asuransi') is-invalid @enderror"
                                name="no_asuransi" value="{{ old('no_asuransi') ?? $vehicle->no_asuransi }}"
                                id="no_asuransi">
                            <label class="form-label" for="no_asuransi">No. Asuransi</label>
                        </div>
                        @error('no_asuransi')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control @error('jth_tempo') is-invalid @enderror"
                                name="jth_tempo" value="{{ old('jth_tempo') ?? $vehicle->jth_tempo }}" id="jth_tempo">
                            <label class="form-label" for="jth_tempo">Jatuh Tempo</label>
                        </div>
                        @error('jth_tempo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <select class="form-select @error('kondisi') is-invalid @enderror" name="kondisi"
                                id="kondisi">
                                <option value="Baik" @selected($vehicle->kondisi == 'Baik')>Baik</option>
                                <option value="Kurang Baik" @selected($vehicle->kondisi == 'Kurang Baik')>Kurang Baik</option>
                            </select>
                            <label class="form-label" for="kondisi">Kondisi</label>
                        </div>
                        @error('kondisi')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" name="keterangan" value="{{ old('keterangan') ?? $vehicle->keterangan }}"
                                class="form-control @error('keterangan') is-invalid @enderror" id="keterangan">
                            <label class="form-label" for="keterangan">Keterangan</label>
                        </div>
                        @error('keterangan')
                            <div class="text-danger">{{ $message }}</div>
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
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="foto" class="form-label">Foto Kendaraan</label>
                        </div>
                        @error('foto')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_stnk" id=""
                                class="form-control @error('f_stnk') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="f_stnk" class="form-label">STNK</label>
                        </div>
                        @error('stnk')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_pajak" id="f_pajak"
                                class="form-control @error('f_pajak') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="f_pajak" class="form-label">Pajak</label>
                        </div>
                        @error('f_pajak')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_kir" id="f_kir"
                                class="form-control @error('f_kir') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="f_kir" class="form-label">KIR</label>
                        </div>
                        @error('f_kir')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="qr" id="qr"
                                class="form-control @error('qr') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="qr" class="form-label">QR Code BBM Subsidi</label>
                        </div>
                        @error('qr')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_polis" id="polis"
                                class="form-control @error('polis') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="polis" class="form-label">Polis Asuransi</label>
                        </div>
                        @error('polis')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="file" name="f_service" id="f_service"
                                class="form-control @error('f_service') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <label for="f_service" class="form-label">Bukti Service</label>
                        </div>
                    </div>
                </div>
                <x-buttons.submit></x-buttons.submit>
            </form>
        @endcomponent
    </div>
@endsection
