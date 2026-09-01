<div class="container mt-3">
    {{-- Alert Update Fitur --}}
    @if (Auth::check() && Auth::user()->hasRole('employee') && session('feature_changes'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h5 class="alert-heading">🔔 Update Terbaru:</h5>
        <ul class="mb-0">
            @foreach (session('feature_changes') as $date => $note)
            <li><strong>{{ $date }}:</strong> {{ $note }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @component('components.card')
    @slot('header')
    👤 {{ $employee->nama }}
    @endslot

    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
            <!-- 1. Tombol Kembali -->
            <a href="{{ route('employees.index') }}"
                class="btn btn-secondary btn-sm rounded-3 d-inline-flex align-items-center justify-content-center px-3 py-2"
                wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            <!-- 2. Tombol Edit & PDF -->
            @can('employee.edit')
            <x-buttons.edit href="{{ route('employees.edit', ['employee' => $employee->id]) }}" wire:navigate></x-buttons.edit>
            <x-buttons.pdf href="{{ route('employee.pdf', ['employee' => $employee->id]) }}"></x-buttons.pdf>
            @endcan

            <!-- 3. Tombol Hapus -->
            @can('employee.delete')
            <button type="button"
                class="btn btn-danger btn-sm rounded-3 d-inline-flex align-items-center justify-content-center px-3 py-2"
                wire:click="deleteEmployee"
                wire:confirm="Yakin mau hapus {{ $employee->nama }}?"
                wire:loading.attr="disabled">
                <i class="bi bi-trash-fill me-1"></i> Hapus
            </button>
            @endcan
        </div>

        @if ($employee->subsidiary?->logo)
        <img src="{{ Storage::url('subsidiary/logo/' . $employee->subsidiary->logo) }}"
            class="img-fluid ms-auto me-md-3"
            alt="Logo {{ $employee->subsidiary->name }}"
            style="max-width: 200px; height: auto;">
        @endif
    </div>

    <section class="bg-light py-3 py-md-4 py-xl-5 rounded">
        <div class="container-fluid">
            <div class="row gy-4">
                {{-- Left Sidebar: Profile Card --}}
                <div class="col-12 col-lg-4 col-xl-3">
                    <div class="card widget-card border-light shadow-sm">
                        <div class="card-header text-bg-secondary fw-semibold">
                            {{ $employee->subsidiary?->name ?? 'Perusahaan Tidak Ditemukan' }}
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @php
                                $fotoPath = $employee->pp
                                ? Storage::url('public/foto_profil/' . $employee->pp)
                                : Storage::url('public/foto_profil/default.png');
                                @endphp
                                <img class="img-thumbnail rounded-circle shadow-sm"
                                    style="width: 140px; height: 140px; object-fit: cover;"
                                    oncontextmenu="return false"
                                    src="{{ $fotoPath }}"
                                    alt="Foto Profil {{ $employee->nama }}" />
                            </div>

                            <h5 class="mb-1 fw-bold">👤 {{ $employee->nama }}</h5>
                            <p class="text-muted small mb-1">
                                📧 {{ $employee->user?->email ?? '-' }}
                            </p>
                            <p class="text-muted small mb-2">
                                🎂 Usia: {{ $age }} Tahun
                            </p>
                            <p class="text-secondary fw-semibold mb-3">💼 {{ $employee->posisi ?? '-' }}</p>
                            <hr>

                            <div class="text-center small">
                                <i class="bi bi-person-fill me-1"></i>
                                <span class="fw-bold">{{ $employee->status_peg ?? '-' }}</span><br>

                                @if ($employee->status_peg === 'PKWT' && $contractStatus)
                                <i class="bi bi-calendar-week me-1"></i>
                                {{ $contractStatus['date_formatted'] }}<br>

                                @if ($contractStatus['is_expired'])
                                <span class="badge bg-danger mt-1">{{ $contractStatus['text'] }}</span>
                                @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                                    {{ $contractStatus['text'] }}
                                </span>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Main Content: Tabs detail data --}}
                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="card widget-card border-light shadow-sm" x-data="{ activeTab: 'organisasi' }">
                        <div class="card-body p-4">
                            {{-- Tab Navigation via Alpine.js --}}
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link" :class="{ 'active': activeTab === 'organisasi' }" @click="activeTab = 'organisasi'">Organisasi</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" :class="{ 'active': activeTab === 'biodata' }" @click="activeTab = 'biodata'">Biodata</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" :class="{ 'active': activeTab === 'emergency' }" @click="activeTab = 'emergency'">Kontak Darurat</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" :class="{ 'active': activeTab === 'lampiran' }" @click="activeTab = 'lampiran'">Lampiran</button>
                                </li>
                            </ul>

                            <div class="tab-content pt-4">
                                {{-- TAB 1: ORGANISASI --}}
                                <div class="tab-pane fade show" :class="{ 'active': activeTab === 'organisasi' }" x-show="activeTab === 'organisasi'">
                                    <div class="row g-0">
                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">NIP</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->nip ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Nama Lengkap</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->nama }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Perusahaan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->subsidiary?->name ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Divisi</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->divisi ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Departemen</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->departemen ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Seksi</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->seksi ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Jabatan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->posisi ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Tgl Masuk / Masa Kerja</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $masaKerja }}</div>
                                    </div>
                                </div>

                                {{-- TAB 2: BIODATA --}}
                                <div class="tab-pane fade show" :class="{ 'active': activeTab === 'biodata' }" x-show="activeTab === 'biodata'">
                                    <div class="row g-0">
                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">NIK</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->nik ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Tempat Lahir</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->tmpt_lahir ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Tanggal Lahir</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            {{ $employee->tgl_lahir ? \Carbon\Carbon::parse($employee->tgl_lahir)->isoFormat('dddd, D MMMM YYYY') : '-' }}
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Jenis Kelamin</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            {{ $employee->jenis_kelamin === 'L' ? 'Laki-laki' : ($employee->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Alamat</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->alamat ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">No. Telepon</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->no_telp ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Email</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->email ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Pendidikan Terakhir</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->pend_trkhr ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Jurusan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->jurusan ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Tahun Lulus</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->thn_lulus ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Nama Ibu</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->nama_ibu ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">NPWP</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->npwp ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Status Pernikahan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->status ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Jumlah Anak</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->jml_ank ?? '0' }}</div>
                                    </div>
                                </div>

                                {{-- TAB 3: KONTAK DARURAT --}}
                                <div class="tab-pane fade show" :class="{ 'active': activeTab === 'emergency' }" x-show="activeTab === 'emergency'">
                                    <div class="row g-0">
                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Nama</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->nama_kd ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">No. Telepon</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->no_kd ?? '-' }}</div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Hubungan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">{{ $employee->hubungan ?? '-' }}</div>
                                    </div>
                                </div>

                                {{-- TAB 4: LAMPIRAN BERKAS --}}
                                <div class="tab-pane fade show" :class="{ 'active': activeTab === 'lampiran' }" x-show="activeTab === 'lampiran'">
                                    <div class="row g-0">
                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Foto Profil</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->pp)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.pp', ['pp' => $employee->pp, 'name' => $employee->nama]) }}"></x-buttons.download>
                                            @endif
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">KTP</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->ktp)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.ktp', ['ktp' => $employee->ktp, 'name' => $employee->nama]) }}"></x-buttons.download>
                                            @endif
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">NPWP</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->npwp2)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.npwp', ['npwp' => $employee->npwp2, 'name' => $employee->nama]) }}"></x-buttons.download>
                                            @endif
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Kartu Keluarga</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->kk)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.kk', ['kk' => $employee->kk, 'name' => $employee->nama]) }}"></x-buttons.download>
                                            @endif
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">BPJS Ketenagakerjaan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->bpjs_ket)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.bpjs_ket', ['bpjs_ket' => $employee->bpjs_ket, 'name' => $employee->nama]) }}"></x-buttons.download>
                                            @endif
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">BPJS Kesehatan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->bpjs_kes)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.bpjs_kes', ['bpjs_kes' => $employee->bpjs_kes, 'name' => $employee->nama]) }}"></x-buttons.download>
                                            @endif
                                        </div>

                                        <div class="col-5 col-md-3 bg-light border-bottom p-2 fw-semibold">Tanda Tangan</div>
                                        <div class="col-7 col-md-9 bg-light border-start border-bottom p-2">
                                            @if (!$employee->ttd)
                                            <span class="text-muted font-monospace">kosong</span>
                                            @else
                                            <x-buttons.download href="{{ route('employee.ttd', ['ttd' => $employee->ttd, 'name' => $employee->nama]) }}"></x-buttons.download>
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
    </section>
    @endcomponent
</div>