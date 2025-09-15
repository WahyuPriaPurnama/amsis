@extends('layouts.app')
@section('title', "$vehicle->jenis_kendaraan")
@section('menuVehicles', 'active')
@section('content')
    <div class="container mt-3">

        @component('components.card')
            @slot('header')
                {{ $vehicle->jenis_kendaraan }}
            @endslot
            <div class="btn-group d-flex gap-2 flex-wrap">
                @can('update', $vehicle)
                    <x-buttons.edit href="{{ route('vehicles.edit', ['vehicle' => $vehicle->id]) }}"></x-buttons.edit>
                @endcan

                @can('delete', $vehicle)
                    <x-buttons.delete data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $vehicle->id }}"></x-buttons.delete>

                    <div class="modal fade" id="modal-delete-{{ $vehicle->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="modalLabel-{{ $vehicle->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalLabel-{{ $vehicle->id }}">
                                        Yakin mau hapus {{ $vehicle->jenis_kendaraan }}?
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Gak Jadi</button>
                                    <button type="submit" form="delete-{{ $vehicle->id }}" class="btn btn-danger ms-3">Iya,
                                        Yakin</button>
                                    <form id="delete-{{ $vehicle->id }}"
                                        action="{{ route('vehicles.destroy', ['vehicle' => $vehicle->id]) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                <x-buttons.pdf href="{{ route('vehicle.pdf', $vehicle->id) }}"></x-buttons.pdf>
            </div>
            <hr>

            <section class="bg-light py-3 py-md-5 py-xl-8">
                <div class="container">
                    <div class="row gy-4 gy-lg-0">
                        <div class="col-12 col-lg-4 col-xl-3">
                            <div class="row gy-4">
                                <div class="col-12">
                                    <div class="card widget-card border-light shadow-sm">
                                        <div class="card-header text-bg-secondary">
                                            {{ $vehicle->subsidiary->name }}
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <img class="img-thumbnail"
                                                    @if ($vehicle->foto == null) src="
                                                {{ Storage::url('public/vehicles/foto/default.png') }}"
                                               @else
                                              src="  {{ Storage::url('public/vehicles/foto/') . $vehicle->foto }}" @endif
                                                    alt="" srcset="">
                                            </div>
                                            <h5 class="mb-1">{{ $vehicle->jenis_kendaraan }}</h5>
                                            </b>Usia Kendaraan: {{ Carbon\Carbon::parse('0-0-' . $vehicle->tahun)->age }} Tahun
                                            <p class="text-secondary">{{ $vehicle->nopol }}</p>
                                            <b>Kondisi: {{ $vehicle->kondisi }}
                                                @if ($vehicle->kondisi == 'Baik')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-hand-thumbs-up-fill"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-hand-thumbs-down-fill"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.38 1.38 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51q.205.03.443.051c.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.9 1.9 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2 2 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.2 3.2 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.8 4.8 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591" />
                                                    </svg>
                                                @endif
                                            </b><br>
                                            Service: {{ $vehicle->tgl_service }}<br>
                                            <i class="bi bi-speedometer"></i> {{ $vehicle->km_akhir }} KM<br>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                                            </svg>
                                            {{ $vehicle->pengguna }}<br>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
                                            </svg> Kendaraan: {{ $vehicle->kategori }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8 col-xl-9">
                            <div class="card widget-card border-light shadow-sm">
                                <div class="card-body p-4">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="ringkasan-tab" data-bs-toggle="tab"
                                                data-bs-target="#ringkasan-tab-pane" type="button" role="tab"
                                                aria-controls="ringkasan-tab-pane" aria-selected="true">Ringkasan</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="kelengkapan-tab" data-bs-toggle="tab"
                                                data-bs-target="#kelengkapan-tab-pane" type="button" role="tab"
                                                aria-controls="kelengkapan-tab-pane" aria-selected="false">Kelengkapan</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="jatuh-tempo-tab" data-bs-toggle="tab"
                                                data-bs-target="#jatuh-tempo-tab-pane" type="button" role="tab"
                                                aria-controls="jatuh-tempo-tab-pane" aria-selected="false">Jatuh
                                                Tempo</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="lampiran-tab" data-bs-toggle="tab"
                                                data-bs-target="#lampiran-tab-pane" type="button" role="tab"
                                                aria-controls="lampiran-tab-pane" aria-selected="false">Lampiran</button>
                                        </li>

                                    </ul>
                                    <div class="tab-content pt-4" id="ringkasanTabContent">
                                        <div class="tab-pane fade show active" id="ringkasan-tab-pane" role="tabpanel"
                                            aria-labelledby="ringkasan-tab" tabindex="0">

                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Kendaraan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->jenis_kendaraan }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Tanggal Perolehan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->tgl_perolehan }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Pengguna</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->pengguna }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Perusahaan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->subsidiary->name }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Warna</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->nama_warna }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Visual dan Kode Warna</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2" style="background-color:{{ $vehicle->warna }}">
                                                        {{ $vehicle->warna }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Tahun Produksi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->tahun }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Atas Nama</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->atas_nama }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Kondisi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->kondisi }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Keterangan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->keterangan == null)
                                                            <p class="font-monospace">Belum ada keterangan</p>
                                                        @else
                                                            {{ $vehicle->keterangan }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="tab-pane fade show" id="kelengkapan-tab-pane" role="tabpanel"
                                            aria-labelledby="kelengkapan-tab" tabindex="0">
                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">No. Rangka</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->no_rangka }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">No. BPKB</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $vehicle->no_bpkb }}
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">No. Mesin</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $vehicle->no_mesin }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Jenis Asuransi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $vehicle->j_asuransi }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Polis Asuransi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $vehicle->p_asuransi }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">No. Asuransi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $vehicle->no_asuransi }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show" id="jatuh-tempo-tab-pane" role="tabpanel"
                                            aria-labelledby="jatuh-tempo-tab" tabindex="0">

                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Asuransi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->jth_tempo }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">STNK</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->stnk }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Pajak</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">{{ $vehicle->pajak }}</div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">KIR</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        {{ $vehicle->kir }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show" id="lampiran-tab-pane" role="tabpanel"
                                            aria-labelledby="lampiran-tab" tabindex="0">

                                            <div class="row g-0">
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Foto Kendaraan</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->foto == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('vehicle.foto', $vehicle->foto) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">STNK</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->f_stnk == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('vehicle.stnk', $vehicle->f_stnk) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">PAJAK</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->f_pajak == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('vehicle.pajak', $vehicle->f_pajak) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">KIR</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->f_kir == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('vehicle.kir', $vehicle->f_kir) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">QR Code BBM Subsidi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->qr == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('vehicle.qr', $vehicle->qr) }}"></x-buttons.download>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
                                                    <div class="p-2">Polis Asuransi</div>
                                                </div>
                                                <div
                                                    class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
                                                    <div class="p-2">
                                                        @if ($vehicle->f_polis == null)
                                                            <p class="font-monospace">kosong</p>
                                                        @else
                                                            <x-buttons.download
                                                                href="{{ route('vehicle.polis', $vehicle->f_polis) }}"></x-buttons.download>
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
            </section>
        @endcomponent
    </div>
@endsection
