@extends('layouts.app')
@section('title', 'Data Aset')
@section('menuAsset', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex">
                <x-buttons.create href="{{ route('asset.create') }}" />
            </div>
            @slot('header')
                LIST ASSET
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered active">
                    <thead>
                        <tr style="text-align: center">
                            <th>No.</th>
                            <th>Plant</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kondisi</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Editor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td>{{ $assets->firstItem() + $loop->iteration - 1 }}</td>
                                <td>{{ $asset->subsidiary->name }}</td>
                                <td><a href="{{ route('asset.show', $asset->id) }}"class="text-decoration-none"
                                        data-bs-toggle="tooltip" data-bs-title="klik untuk lihat detail">{{ $asset->code }}</a>
                                </td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->condition }}</td>
                                <td>{{ $asset->category }}</td>
                                <td>{{ $asset->location }}</td>
                                <td>{{ $asset->user->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center">tidak ada data..</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
