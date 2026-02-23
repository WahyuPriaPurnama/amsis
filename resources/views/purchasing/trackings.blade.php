@extends('layouts.app')
@section('title', 'Lacak Paket')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <i class="fas fa-search-location me-2"></i> Lacak Pengiriman
            @endslot

            <div class="row">
                <div class="col-md-12">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('tracking.process') }}" method="POST">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fw-bold mb-2">Pilih Kurir</label>
                                    <select name="courier" id="courier-select" class="form-control" required>
                                        <option value="auto">-- Deteksi Otomatis --</option>
                                        <option value="jne">JNE Express</option>
                                        <option value="pos">POS Indonesia</option>
                                        <option value="jnt">J&T Express</option>
                                        <option value="jnt_cargo">J&T Cargo</option>
                                        <option value="sicepat">SiCepat</option>
                                        <option value="tiki">TIKI</option>
                                        <option value="anteraja">AnterAja</option>
                                        <option value="wahana">Wahana</option>
                                        <option value="ninja">Ninja Express</option>
                                        <option value="lion">Lion Parcel</option>
                                        <option value="pcp">PCP Express</option>
                                        <option value="jet">JET Express</option>
                                        <option value="rex">REX Express</option>
                                        <option value="first">First Logistics</option>
                                        <option value="ide">ID Express</option>
                                        <option value="spx">Shopee Express</option>
                                        <option value="kgx">KGXpress</option>
                                        <option value="sap">SAP Express</option>
                                        <option value="rpx">RPX</option>
                                        <option value="lex">Lazada Express</option>
                                        <option value="indah_cargo">Indah Cargo</option>
                                        <option value="dakota">Dakota Cargo</option>
                                        <option value="kurir_tokopedia">Kurir Rekomendasi</option>
                                    </select>
                                </div>

                                @push('scripts')
                                    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
                                        rel="stylesheet" />
                                    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#courier-select').select2({
                                                theme: 'bootstrap-5', // Jika Anda menggunakan tema bootstrap
                                                placeholder: "Pilih atau Ketik Nama Kurir"
                                            });
                                        });
                                    </script>
                                @endpush
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="fw-bold mb-2">Nomor Resi</label>
                                    <input type="text" name="no_resi" class="form-control" placeholder="Contoh: JX7301537416"
                                        value="{{ old('no_resi', $resi ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100 mt-2">
                                    <i class="fas fa-truck"></i> Lacak Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if (isset($history))
                <hr class="my-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded shadow-sm">
                            <h6 class="fw-bold">Ringkasan</h6>
                            <table class="table table-sm mb-0 mt-2">
                                <tr>
                                    <td>Resi</td>
                                    <td>: {{ $data['awb'] }}</td>
                                </tr>
                                <tr>
                                    <td>Kurir</td>
                                    <td>: {{ $courier_name }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>: <span class="badge bg-primary">{{ $data['status'] }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-3">Riwayat Perjalanan</h6>
                        <ul class="list-group list-group-flush border-start ms-2">
                            @foreach ($history as $item)
                                <li class="list-group-item position-relative pb-4" style="border:none">
                                    <i class="fas fa-check-circle text-primary position-absolute"
                                        style="left:-1.3rem; background:#fff"></i>
                                    <div class="small text-muted">{{ $item['date'] }}</div>
                                    <div class="fw-bold">{{ $item['desc'] }}</div>
                                    <div class="text-secondary small">{{ $item['location'] }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endcomponent
    </div>

    <style>
        .list-group-item::before {
            content: "";
            position: absolute;
            left: -1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ebebeb;
        }
    </style>
@endsection
