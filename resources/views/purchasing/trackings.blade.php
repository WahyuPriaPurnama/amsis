@extends('layouts.app')
@section('title', 'Lacak Paket')
@section('menuTracking', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-search-location me-2"></i> Lacak Pengiriman</span>
                    @if (isset($history))
                        <a href="{{ route('tracking.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    @endif
                </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('tracking.process') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="fw-bold mb-2">Kurir</label>
                                <select name="courier" id="courier-select" class="form-select" required>
                                    <option value="auto">-- Deteksi Otomatis --</option>

                                    <optgroup label="Populer">
                                        <option value="jne" {{ old('courier') == 'jne' ? 'selected' : '' }}>JNE Express
                                        </option>
                                        <option value="jnt" {{ old('courier') == 'jnt' ? 'selected' : '' }}>J&T Express
                                        </option>
                                        <option value="sicepat" {{ old('courier') == 'sicepat' ? 'selected' : '' }}>SiCepat
                                        </option>
                                        <option value="pos" {{ old('courier') == 'pos' ? 'selected' : '' }}>POS Indonesia
                                        </option>
                                        <option value="spx" {{ old('courier') == 'spx' ? 'selected' : '' }}>Shopee Express
                                        </option>
                                    </optgroup>

                                    <optgroup label="Cargo & Logistik">
                                        <option value="jnt_cargo" {{ old('courier') == 'jnt_cargo' ? 'selected' : '' }}>J&T
                                            Cargo (Resi JX)</option>
                                        <option value="indah_cargo" {{ old('courier') == 'indah_cargo' ? 'selected' : '' }}>
                                            Indah Cargo</option>
                                        <option value="dakota" {{ old('courier') == 'dakota' ? 'selected' : '' }}>Dakota Cargo
                                        </option>
                                        <option value="rex" {{ old('courier') == 'rex' ? 'selected' : '' }}>REX Express
                                        </option>
                                    </optgroup>

                                    <optgroup label="Lainnya">
                                        <option value="tiki">TIKI</option>
                                        <option value="anteraja">AnterAja</option>
                                        <option value="wahana">Wahana</option>
                                        <option value="ninja">Ninja Express</option>
                                        <option value="lion">Lion Parcel</option>
                                        <option value="ide">ID Express</option>
                                        <option value="lex">Lazada Express</option>
                                        <option value="sap">SAP Express</option>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="fw-bold mb-2">Nomor Resi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" name="no_resi" class="form-control" placeholder="Contoh: JX7301537416"
                                        value="{{ old('no_resi', $resi ?? '') }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100 shadow-sm" id="btn-track">
                                    <i class="fas fa-search me-1"></i> Lacak Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if (isset($history))
                <hr class="my-5">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">Ringkasan Paket</h6>
                                <div class="d-flex flex-column gap-2">
                                    <div class="small">
                                        <span class="text-muted d-block">No. Resi:</span>
                                        <strong class="text-primary">{{ $data['awb'] }}</strong>
                                    </div>
                                    <div class="small">
                                        <span class="text-muted d-block">Kurir:</span>
                                        <strong>{{ $courier_name }}</strong>
                                    </div>
                                    <div class="small">
                                        <span class="text-muted d-block">Status Terakhir:</span>
                                        <span class="badge bg-success">{{ $data['status'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h6 class="fw-bold mb-4">Riwayat Pengiriman</h6>
                        <div class="tracking-timeline">
                            @foreach ($history as $item)
                                <div class="tracking-item">
                                    <div class="tracking-date small text-muted">
                                        {{ \Carbon\Carbon::parse($item['date'])->format('d M Y, H:i') }}
                                    </div>
                                    <div class="tracking-content ps-4 pb-4 border-start position-relative">
                                        <i class="fas fa-check-circle text-primary bg-white position-absolute"
                                            style="left: -8px; top: 0;"></i>
                                        <div class="fw-bold text-dark">{{ $item['desc'] }}</div>
                                        <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $item['location'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endcomponent
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

        <style>
            /* CSS Refactor untuk Dropdown & Timeline */
            .select2-container--bootstrap-5 .select2-results__options {
                max-height: 250px;
            }

            .tracking-timeline .tracking-item:last-child .tracking-content {
                border-left: 2px solid transparent !important;
            }

            .tracking-content {
                border-left: 2px solid #e9ecef;
                margin-left: 7px;
            }

            /* Loading state saat klik */
            #btn-track.loading {
                pointer-events: none;
                opacity: 0.7;
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#courier-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: "Cari Kurir...",
                    allowClear: true
                });

                // Tambahkan efek loading sederhana
                $('form').on('submit', function() {
                    $('#btn-track').addClass('loading').html(
                        '<span class="spinner-border spinner-border-sm me-2"></span> Mencari...');
                });
            });
        </script>
    @endpush
@endsection
