@php
    // Sederhanakan logika: Jika admin (Super/Holding), mereka BOLEH edit.
    $canEditOrg = $isLeader;
    $readonlyAttr = !$canEditOrg ? 'readonly' : '';
    $disabledAttr = !$canEditOrg ? 'disabled' : '';
@endphp

@extends('layouts.app')
@section('title', 'Edit Data Karyawan')
@section('menuEmployees', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                EDIT DATA KARYAWAN
            @endslot

            <form action="{{ route('employees.update', $employee) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- SECTION 1: ORGANISASI --}}
                <div class="row mb-3">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6">
                            <h4 class="fw-semibold mb-0">Organisasi</h4>
                        </div>
                        @if (!$canEditOrg)
                            <div class="col-md-6 text-center text-md-end">
                                <div class="alert alert-warning d-inline-block py-2 px-3 mb-0">
                                    <i class="bi bi-info-circle-fill"></i> Hanya admin yang dapat edit Organisasi
                                </div>
                            </div>
                        @endif
                    </div>
                    <hr>

                    {{-- Loop Input Text Organisasi --}}
                    @foreach ([
                'nip' => 'NIP',
                'nama' => 'Nama Lengkap',
                'divisi' => 'Divisi',
                'departemen' => 'Departemen',
                'seksi' => 'Seksi',
                'posisi' => 'Jabatan',
            ] as $field => $label)
                        <div class="col-12 col-sm-6 col-md-3 mb-3">
                            <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                            <input type="text" id="{{ $field }}" name="{{ $field }}"
                                value="{{ old($field, $employee->$field) }}"
                                class="form-control @error($field) is-invalid @enderror" {{ $readonlyAttr }}>
                            @error($field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    {{-- Tanggal Masuk --}}
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <label for="tgl_masuk" class="form-label">Tanggal Masuk Kerja</label>
                        <input type="date" id="tgl_masuk" name="tgl_masuk"
                            value="{{ old('tgl_masuk', $employee->tgl_masuk) }}"
                            class="form-control @error('tgl_masuk') is-invalid @enderror" {{ $readonlyAttr }}>
                    </div>

                    {{-- Subsidiary (Plant) --}}
                    <div class="col-12 col-md-3 mb-3">
                        <label for="subsidiary_id" class="form-label">Plant</label>
                        <select name="subsidiary_id" id="subsidiary_id"
                            class="form-select @error('subsidiary_id') is-invalid @enderror" {{ $disabledAttr }}>
                            <option value="">Pilih Plant</option>
                            @foreach ($subsidiaries as $subsidiary)
                                <option value="{{ $subsidiary->id }}" @selected($subsidiary->id == old('subsidiary_id', $employee->subsidiary_id))>
                                    {{ $subsidiary->name }}
                                </option>
                            @endforeach
                        </select>
                        @if (!$canEditOrg)
                            <input type="hidden" name="subsidiary_id" value="{{ $employee->subsidiary_id }}">
                        @endif
                    </div>

                    {{-- Status Pegawai & Kontrak (Alpine.js) --}}
                    <div x-data="{
                        statusPeg: '{{ old('status_peg', $employee->status_peg) }}',
                        canEdit: {{ $canEditOrg ? 'true' : 'false' }}
                    }" class="row g-3">
                        <div class="col-12 col-sm-6 col-md-3 mb-3">
                            <label for="status_peg" class="form-label">Status Pegawai</label>
                            <select name="status_peg" id="status_peg"
                                class="form-select @error('status_peg') is-invalid @enderror" x-model="statusPeg"
                                :disabled="!canEdit">
                                <option value="">Pilih Status</option>
                                @foreach (['PKWT', 'PKWTT', '-'] as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            <template x-if="!canEdit">
                                <input type="hidden" name="status_peg" :value="statusPeg">
                            </template>
                        </div>

                        <template x-if="statusPeg === 'PKWT'">
                            <div class="col-12 col-md-6 d-flex gap-3">
                                <div class="flex-fill mb-3">
                                    <label class="form-label">Awal Kontrak</label>
                                    <input type="date" name="awal_kontrak"
                                        value="{{ old('awal_kontrak', $employee->awal_kontrak) }}" class="form-control"
                                        :readonly="!canEdit">
                                </div>
                                <div class="flex-fill mb-3">
                                    <label class="form-label">Akhir Kontrak</label>
                                    <input type="date" name="akhir_kontrak"
                                        value="{{ old('akhir_kontrak', $employee->akhir_kontrak) }}" class="form-control"
                                        :readonly="!canEdit">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="row mb-3 pt-3">
                    <div class="col-12">
                        <h3 class="mb-2">Biodata</h3>
                        <hr>
                    </div>
                    {{-- NIK --}}
                    <div class="col-12 col-md-3 mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $employee->nik) }}"
                            class="form-control @error('nik') is-invalid @enderror" aria-describedby="nikHelp">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label" for="tmpt_lahir">Tempat Lahir</label>
                        <input type="text" id="tmpt_lahir" name="tmpt_lahir"
                            value="{{ old('tmpt_lahir', $employee->tmpt_lahir) }}"
                            class="form-control @error('tmpt_lahir') is-invalid @enderror">
                        @error('tmpt_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label" for="tgl_lahir">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir"
                            class="form-control @error('tgl_lahir') is-invalid @enderror"
                            value="{{ old('tgl_lahir', $employee->tgl_lahir) }}">
                        @error('tgl_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="form-check me-3">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio"
                                name="jenis_kelamin" id="laki_laki" value="L" @checked(old('jenis_kelamin', $employee->jenis_kelamin) == 'L')>
                            <label class="form-check-label" for="laki_laki">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio"
                                name="jenis_kelamin" id="perempuan" value="P" @checked(old('jenis_kelamin', $employee->jenis_kelamin) == 'P')>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" rows="3" name="alamat">{{ old('alamat', $employee->alamat) }}</textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 col-md-2 mb-3">
                        <label class="form-label" for="no_telp">No. Telp / WhatsApp</label>
                        <input type="text" id="no_telp" name="no_telp"
                            value="{{ old('no_telp', $employee->no_telp) }}"
                            class="form-control @error('no_telp') is-invalid @enderror">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-3 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="Enter employee email"
                            autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-sm-4 col-md-2 mb-3">
                        <label for="pend_trkhr" class="form-label">Pendidikan Terakhir</label>
                        <select name="pend_trkhr" id="pend_trkhr"
                            class="form-select @error('pend_trkhr') is-invalid @enderror">
                            @foreach (['SD', 'SMP', 'SMA', 'Diploma', 'Sarjana', 'Magister', 'Doktor'] as $level)
                                <option value="{{ $level }}" @selected($employee->pend_trkhr == $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('pend_trkhr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-3 mb-3">
                        <label class="form-label" for="jurusan">Jurusan</label>
                        <input type="text" id="jurusan" name="jurusan"
                            value="{{ old('jurusan', $employee->jurusan) }}"
                            class="form-control @error('jurusan') is-invalid @enderror">
                        @error('jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4 col-md-2 mb-3">
                        <label class="form-label" for="thn_lulus">Tahun Lulus</label>
                        <input type="text" id="thn_lulus" name="thn_lulus"
                            value="{{ old('thn_lulus', $employee->thn_lulus) }}"
                            class="form-control @error('thn_lulus') is-invalid @enderror">
                        @error('thn_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div x-data="{ statusNikah: '{{ old('status', $employee->status) }}' }" class="row mb-3">
                    <div class="col-sm-4 mb-3">
                        <label class="form-label" for="nama_ibu">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu"
                            value="{{ old('nama_ibu', $employee->nama_ibu) }}"
                            class="form-control @error('nama_ibu') is-invalid @enderror">
                        @error('nama_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="npwp">NPWP</label>
                        <input type="text" id="npwp" name="npwp" value="{{ old('npwp', $employee->npwp) }}"
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
                            @foreach (['Kawin', 'Belum Kawin', 'Cerai'] as $status)
                                <option value="{{ $status }}" @selected($employee->status == $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- tampil input jumlah anak jika kawin --}}
                    <template x-if="statusNikah ==='Kawin' || statusNikah ==='Cerai'">
                        <div class="col-sm-1 mb-3">
                            <label class="form-label" for="jml_ank">Anak</label>
                            <input type="text" id="jml_ank" name="jml_ank"
                                value="{{ old('jml_ank', $employee->jml_ank) }}"
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
                        <input type="text" id="nama_ibu" name="nama_kd"
                            value="{{ old('nama_kd', $employee->nama_kd) }}"
                            class="form-control @error('nama_kd') is-invalid @enderror">
                        @error('nama_kd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="no_kd">No. Kontak Darurat</label>
                        <input type="text" id="no_kd" name="no_kd" value="{{ old('no_kd', $employee->no_kd) }}"
                            class="form-control @error('no_kd') is-invalid @enderror">
                        @error('no_kd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="hubungan">Hubungan</label>
                        <input type="text" id="hubungan" name="hubungan"
                            value="{{ old('hubungan', $employee->hubungan) }}"
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
                            class="form-control @error('pp') is-invalid @enderror" accept="image/*">
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
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="ttd">Tanda Tangan</label>
                        <input type="file" id="ttd" name="ttd"
                            class="form-control @error('ttd') is-invalid @enderror" accept="image/*">
                        @error('ttd')
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
