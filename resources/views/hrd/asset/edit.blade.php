@extends('layouts.app')
@section('title', 'Edit Aset')
@section('menuAsset', 'active')
@section('content')
    <div class="container-fluid mt-3">
        @component('components.card')
            @slot('header')
                Edit Aset
            @endslot

            <form action="{{ route('asset.update', $asset->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Kode Aset</label>
                        <input type="text" name="code" value="{{ old('code', $asset->code) }}"
                            class="form-control @error('code') is-invalid @enderror" placeholder="A001">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Aset</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Printer Canon">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kode Akuntansi</label>
                        <input type="text" name="accounting_code"
                            value="{{ old('accounting_code', $asset->accounting_code) }}"
                            class="form-control @error('accounting_code') is-invalid @enderror">
                        @error('accounting_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location', $asset->location) }}"
                            class="form-control @error('location') is-invalid @enderror" placeholder="Gudang A">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $asset->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Kondisi</label>
                        <select name="condition" class="form-select @error('condition') is-invalid @enderror">
                            @foreach (['Baik', 'Rusak', 'Lainnya'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('condition', $asset->condition) == $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pemilik</label>
                        <select name="owner" class="form-select @error('owner') is-invalid @enderror">
                            @foreach (['Umum', 'Engineering', 'QC & Lab'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('owner', $asset->owner) == $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('owner')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                            @foreach (['Tanah & Bangunan', 'Mesin', 'Furniture & Fixture', 'Kendaraan', 'Alat Kerja'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('category', $asset->category) == $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subsidiary</label>
                        <select name="subsidiary_id" class="form-select @error('subsidiary_id') is-invalid @enderror">
                            @foreach ($subsidiaries as $subsidiary)
                                <option value="{{ $subsidiary->id }}"
                                    {{ old('subsidiary_id', $asset->subsidiary_id) == $subsidiary->id ? 'selected' : '' }}>
                                    {{ $subsidiary->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subsidiary_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $asset->quantity) }}"
                            class="form-control @error('quantity') is-invalid @enderror" min="1">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="unit" value="{{ old('unit', $asset->unit) }}"
                            class="form-control @error('unit') is-invalid @enderror">
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Pemakaian</label>
                        <input type="date" name="usage_date" value="{{ old('usage_date', $asset->usage_date) }}"
                            class="form-control @error('usage_date') is-invalid @enderror">
                        @error('usage_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}"
                            class="form-control @error('purchase_date') is-invalid @enderror">
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    @foreach ([
                'purchase_value' => 'Nilai Pembelian',
                'depreciation_value' => 'Nilai Penyusutan',
                'total_value' => 'Total Nilai',
                'useful_life' => 'Masa Manfaat (bulan)',
            ] as $field => $label)
                        <div class="col-md-3">
                            <label class="form-label">{{ $label }}</label>
                            <input type="number" step="0.01" name="{{ $field }}"
                                value="{{ old($field, $asset->$field) }}"
                                class="form-control @error($field) is-invalid @enderror">
                            @error($field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="row mb-3">
                    @foreach ([
                'delivery_receipt' => 'Tanda Terima',
                'manual_book' => 'Manual Book',
                'photo' => 'Foto Aset',
                'attachment' => 'Lampiran',
            ] as $field => $label)
                        <div class="col-md-3">
                            <label class="form-label">{{ $label }}</label>
                            <input type="file" name="{{ $field }}"
                                class="form-control @error($field) is-invalid @enderror">
                            @error($field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- tampilkan file lama jika ada --}}
                            @if ($asset->$field)
                                <small class="d-block mt-1">
                                    File saat ini:
                                    <a href="{{ asset('storage/' . $asset->$field) }}" target="_blank">Lihat</a>
                                </small>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection
