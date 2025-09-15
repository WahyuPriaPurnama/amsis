@extends('layouts.app')
@section('title', 'Data Kendaraan')
@section('menuVehicles', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            @slot('header')
                🚗 DATA KENDARAAN
            @endslot

            <div class="button-action mb-3 d-flex gap-2 flex-wrap justify-content-between flex-wrap">
                @can('create', App\Models\Vehicle::class)
                    <x-buttons.create href="{{ route('vehicles.create') }}"></x-buttons.create>
                    <x-buttons.pdf href="{{ route('vehicles.pdf') }}"></x-buttons.pdf>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NAMA KENDARAAN</th>
                            <th>KATEGORI</th>
                            <th>PLANT</th>
                            <th>NOPOL</th>
                            <th>SERVICE</th>
                            <th>KM</th>
                            <th>STNK</th>
                            <th>PAJAK</th>
                            <th>KIR</th>
                            <th>ASURANSI</th>
                            <th>KONDISI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @can('view', $item)
                                        <a href="{{ route('vehicles.show', ['vehicle' => $item->id]) }}" class="text-decoration-none"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="klik untuk melihat detail">
                                            {{ $item->jenis_kendaraan }}</a>
                                    @else
                                        <span class="text-muted">
                                            {{ $item->jenis_kendaraan }}
                                        </span>
                                    @endcan
                                </td>
                                <td>{{ $item->kategori }}</td>
                                <td>{{ $item->subsidiary->name }}</td>
                                <td>{{ $item->nopol }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ $item->stnk }}</td>
                                <td>{{ $item->pajak }}</td>
                                <td>{{ $item->kir }}</td>
                                <td>{{ $item->jth_tempo }}</td>
                                <td>{{ $item->kondisi }}</td>

                            </tr>
                        @empty
                            <td colspan="12" class="text-center">Tidak ada data...</td>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>NAMA KENDARAAN</th>
                            <th>KATEGORI</th>
                            <th>PLANT</th>
                            <th>NOPOL</th>
                            <th>STNK</th>
                            <th>PAJAK</th>
                            <th>KIR</th>
                            <th>ASURANSI</th>
                            <th>KONDISI</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
