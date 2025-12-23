@extends('layouts.app')
@section('title', 'Detail Aset')
@section('menuAsset', 'active')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Detail Aset
            @endslot

            {{-- Baris 1 --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3"><strong>Subsidiary:</strong><br>{{ $asset->subsidiary->name ?? '-' }}</div>
                <div class="col-12 col-md-3"><strong>Kode Aset:</strong><br>{{ $asset->code }}</div>
                <div class="col-12 col-md-3"><strong>Nama Aset:</strong><br>{{ $asset->name }}</div>
                <div class="col-12 col-md-3"><strong>Lokasi:</strong><br>{{ $asset->location }}</div>
            </div>

            {{-- Baris 2 --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3"><strong>Jumlah:</strong><br>{{ $asset->quantity }}</div>
                <div class="col-12 col-md-3"><strong>Satuan:</strong><br>{{ $asset->unit }}</div>
                <div class="col-12 col-md-3"><strong>Kondisi:</strong><br>{{ $asset->condition }}</div>
                <div class="col-12 col-md-3"><strong>Pemilik:</strong><br>{{ $asset->owner }}</div>
            </div>

            {{-- Baris 3 --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3"><strong>Kategori:</strong><br>{{ $asset->category }}</div>
                <div class="col-12 col-md-3"><strong>Kode Akuntansi:</strong><br>{{ $asset->accounting_code }}</div>
                <div class="col-12 col-md-3"><strong>Tanggal Pemakaian:</strong><br>{{ $asset->usage_date }}</div>
                <div class="col-12 col-md-3"><strong>Tanggal Pembelian:</strong><br>{{ $asset->purchase_date }}</div>
            </div>

            {{-- Baris 4 --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3"><strong>Nilai
                        Pembelian:</strong><br>{{ number_format($asset->purchase_value, 2) }}
                </div>
                <div class="col-12 col-md-3"><strong>Nilai
                        Penyusutan:</strong><br>{{ number_format($asset->depreciation_value, 2) }}</div>
                <div class="col-12 col-md-3"><strong>Total Nilai:</strong><br>{{ number_format($asset->total_value, 2) }}</div>
                <div class="col-12 col-md-3"><strong>Masa Manfaat:</strong><br>{{ $asset->useful_life }} bulan</div>
            </div>

            {{-- Baris 5: File Uploads --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <strong>Tanda Terima:</strong><br>
                    @if ($asset->delivery_receipt)
                        <a href="{{ asset('storage/' . $asset->delivery_receipt) }}" target="_blank">Lihat File</a>
                    @else
                        -
                    @endif
                </div>
                <div class="col-12 col-md-3">
                    <strong>Manual Book:</strong><br>
                    @if ($asset->manual_book)
                        <a href="{{ asset('storage/' . $asset->manual_book) }}" target="_blank">Lihat File</a>
                    @else
                        -
                    @endif
                </div>
                <div class="col-12 col-md-3">
                    <strong>Foto Aset:</strong><br>
                    @if ($asset->photo)
                        <img src="{{ asset('storage/' . $asset->photo) }}" alt="Foto Aset" class="img-fluid"
                            style="max-height:150px;">
                    @else
                        -
                    @endif
                </div>
                <div class="col-12 col-md-3">
                    <strong>Lampiran:</strong><br>
                    @if ($asset->attachment)
                        <a href="{{ asset('storage/' . $asset->attachment) }}" target="_blank">Lihat File</a>
                    @else
                        -
                    @endif
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-3">
                <strong>Deskripsi:</strong><br>{{ $asset->description }}
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('asset.index') }}" class="btn btn-secondary">Kembali</a>
                <div class="d-flex align-items-center">
                    @can('asset.delete')
                        <x-buttons.delete2 :href="route('asset.destroy', $asset->id)" />
                    @endcan
                    @can('asset.edit')
                        <a href="{{ route('asset.edit', $asset->id) }}" class="btn btn-primary ms-2">Edit</a>
                    @endcan
                </div>
            </div>
        @endcomponent
    </div>
@endsection
