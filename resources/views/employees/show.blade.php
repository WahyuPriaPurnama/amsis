@extends('layouts.app')
@section('title', "Biodata $employee->nama")
@section('menuEmployees', 'active')
@section('content')
    <div class="container mt-3">
        @if (Auth::check() && Auth::user()->role === 'employee' && session('feature_changes'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5>🔔 Update Terbaru:</h5>
                <ul>
                    @foreach (session('feature_changes') as $date => $note)
                        <li><strong>{{ $date }}:</strong> {{ $note }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @component('components.card')
            @slot('header')
                {{ $employee->nama }}
            @endslot
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="btn-group d-flex gap-2 flex-wrap">
                    @can('update', $employee)
                        <x-buttons.edit href="{{ route('employees.edit', ['employee' => $employee->id]) }}"
                            wire:navigate></x-buttons.edit>
                        <x-buttons.pdf href="{{ route('employee.pdf', ['employee' => $employee->id]) }}"></x-buttons.pdf>
                    @endcan
                    @can('delete', $employee)
                        <x-buttons.delete data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        </x-buttons.delete>
                    @endcan
                    @php
                        $user = auth()->user();
                    @endphp
                    {{-- @if ($user->employee_id && $user->role == 'employee')
                        <x-buttons.create href="#">Pengajuan
                            Cuti</x-buttons.create>
                    @endif --}}
                </div>
                <img src="{{ Storage::url('subsidiary/logo/' . $employee->subsidiary->logo) }}"
                    class="img-fluid ms-auto me-md-3" alt="Logo {{ $employee->subsidiary->name }}"
                    style="max-width: 200px; height: auto;">
            </div>
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Yakin mau hapus {{ $employee->nama }}?
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Gak Jadi</button>
                            <button type="submit" form="delete" class="btn btn-danger ms-3">Iya, Yakin</button>
                            <form id="delete" action="{{ route('employees.destroy', ['employee' => $employee->id]) }}"
                                method="post">
                                @method('DELETE')
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <section class="bg-light py-3 py-md-5 py-xl-8">
                <div class="container">
                    <div class="row gy-4 gy-lg-0">
                        <div class="col-12 col-lg-4 col-xl-3">
                            <div class="row gy-4">
                                <div class="col-12">
                                    <div class="card widget-card border-light shadow-sm">
                                        <div class="card-header text-bg-secondary">
                                            {{ $employee->subsidiary->name }}
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <img class="img-thumbnail" oncontextmenu="return false"
                                                    @if ($employee->pp == null) src="
                                                {{ Storage::url('public/foto_profil/default.png') }}"
                                               @else
                                              src="  {{ Storage::url('public/foto_profil/') . $employee->pp }}" @endif
                                                    alt="" srcset="">
                                            </div>
                                            <h5 class="mb-1">{{ $employee->nama }}</h5>
                                            </b>Usia: {{ Carbon\Carbon::parse($employee->tgl_lahir)->age }} Tahun
                                            <p class="text-secondary mb-4">{{ $employee->posisi }}</p>
                                            <hr>
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                                                </svg>

                                                {{ $employee->status_peg == 'PKWT' ? 'PKWT' : '' }}
                                                {{ $employee->status_peg == 'PKWTT' ? 'PKWTT' : '' }}<br>

                                                @if ($employee->status_peg == 'PKWT')
                                                    <i class="bi bi-calendar-week"></i> {{ $employee->akhir_kontrak }}<br>
                                                    @php
                                                        $days = \Carbon\Carbon::now()->diffInDays(
                                                            $employee->akhir_kontrak,
                                                            false,
                                                        );
                                                    @endphp

                                                    @if ($days < 0)
                                                        <span class="text-danger">Kontrak sudah berakhir</span>
                                                    @else
                                                        <span class="text-success">Sisa {{ $days }} hari</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8 col-xl-9">
                            <div class="card widget-card border-light shadow-sm">
                                <div class="card-body p-4">
                                    <ul class="nav nav-tabs" id="biodataTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="organisasi-tab" data-bs-toggle="tab"
                                                data-bs-target="#organisasi-tab-pane" type="button" role="tab"
                                                aria-controls="organisasi-tab-pane" aria-selected="true">Organisasi</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="biodata-tab" data-bs-toggle="tab"
                                                data-bs-target="#biodata-tab-pane" type="button" role="tab"
                                                aria-controls="biodata-tab-pane" aria-selected="false">Biodata</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="emergency-tab" data-bs-toggle="tab"
                                                data-bs-target="#emergency-tab-pane" type="button" role="tab"
                                                aria-controls="emergency-tab-pane" aria-selected="false">Kontak
                                                Darurat</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="lampiran-tab" data-bs-toggle="tab"
                                                data-bs-target="#lampiran-tab-pane" type="button" role="tab"
                                                aria-controls="lampiran-tab-pane" aria-selected="false">Lampiran</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content pt-4" id="organisasiTabContent">
                                        <div class="tab-pane fade show active" id="organisasi-tab-pane" role="tabpanel"
                                            aria-labelledby="organisasi-tab" tabindex="0">

                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">NIP</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->nip }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Nama Lengkap</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->nama }}</div>
                                                </div>


                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Perusahaan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->subsidiary->name }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Divisi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->divisi }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Departemen</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->departemen }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Seksi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->seksi }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Jabatan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->posisi }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Tgl Masuk / Masa Kerja</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->tgl_masuk)
                                                            @php
                                                                $start = \Carbon\Carbon::parse($employee->tgl_masuk);
                                                                $now = \Carbon\Carbon::now();
                                                                $diff = $start->diff($now);

                                                                $years = $diff->y;
                                                                $months = $diff->m;
                                                                $days = $diff->d;
                                                            @endphp

                                                            {{ $start->isoFormat('dddd, D MMMM YYYY') }}
                                                            / {{ $years }} Tahun {{ $months }} Bulan
                                                            {{ $days }} Hari
                                                        @else
                                                            Belum ada data
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade show" id="biodata-tab-pane" role="tabpanel"
                                            aria-labelledby="biodata-tab" tabindex="0">
                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">NIK</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->nik }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Tempat Lahir</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->tmpt_lahir }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Tanggal Lahir</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ Carbon\Carbon::create($employee->tgl_lahir)->isoFormat('dddd, D MMMM YYYY') }}
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Jenis Kelamin</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Alamat</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->alamat }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">No. Telepon</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->no_telp }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Email</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->email }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Pendidikan Terakhir</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->pend_trkhr }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Jurusan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->jurusan }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Tahun Lulus</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->thn_lulus }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Nama Ibu</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->nama_ibu }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">NPWP</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->npwp }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Status Pernikahan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->status }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Jumlah Anak</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->jml_ank }}</div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="tab-pane fade show" id="emergency-tab-pane" role="tabpanel"
                                            aria-labelledby="emergency-tab" tabindex="0">

                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Nama</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->nama_kd }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">No. Telepon</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->no_kd }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Hubungan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $employee->hubungan }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show" id="lampiran-tab-pane" role="tabpanel"
                                            aria-labelledby="lampiran-tab" tabindex="0">

                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Foto Profil</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->pp == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('employee.pp', $employee->pp) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">KTP</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->ktp == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('employee.ktp', $employee->ktp) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">NPWP</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->npwp2 == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('employee.npwp', $employee->npwp2) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Kartu Keluarga</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->kk == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('employee.kk', $employee->kk) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">BPJS Ketenagakerjaan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->bpjs_ket == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('employee.bpjs_ket', $employee->bpjs_ket) }}">
                                                            </x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">BPJS Kesehatan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($employee->bpjs_kes == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('employee.bpjs_kes', $employee->bpjs_kes) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>
    @endcomponent
    </div>
@endsection
