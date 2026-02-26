@extends('layouts.app')
@section('title', 'Data Aset')
@section('menuAsset', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            <div class="button-action mb-3 d-flex flex-wrap justify-content-between">
                <div class="d-flex gap-2 flex-wrap">
                    @can('asset.create')
                        <x-buttons.create href="{{ route('asset.create') }}" />
                    @endcan
                    <x-buttons.pdf href="{{ route('asset.export_pdf') }}"></x-buttons.pdf>
                    <x-buttons.excel href="{{ route('asset.export_excel') }}"></x-buttons.excel>
                </div>
                <form method="GET" action="{{ route('asset.index') }}" class="d-flex gap-2">
                    <select name="subsidiary_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Plant --</option>
                        @foreach ($allSubsidiaries as $sub)
                            <option value="{{ $sub->id }}" {{ request('subsidiary_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari nama atau kode ...">
                    <button class="btn btn-primary" type="submit">Cari</button>

                </form>
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
                                <td>
                                    @can('asset.view')
                                        <a href="{{ route('asset.show', $asset->id) }}"class="text-decoration-none"
                                            data-bs-toggle="tooltip"
                                            data-bs-title="klik untuk lihat detail">{{ $asset->code }}</a>
                                    @else
                                        {{ $asset->code }}
                                    @endcan
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
                {{ $assets->links() }}
            </div>
        @endcomponent
    </div>
@endsection
