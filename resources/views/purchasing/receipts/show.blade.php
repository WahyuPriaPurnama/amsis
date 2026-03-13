@extends('layouts.app')
@section('title', 'Detail Kedatangan Barang')
@section('menuReceipt', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle me-2"></i> Detail Kedatangan: {{ $receipt->reference_number }}</span>
                    <div>
                        <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        @can('receipts.edit')
                            <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                        @endcan
                        @can('receipts.delete')
                            <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endslot

            {{-- Banner Header --}}
            <div class="bg-info p-4 text-white rounded-top mb-4">
                <h5 class="mb-1 fw-bold">Nomor Referensi: {{ $receipt->reference_number }}</h5>
                <p class="small mb-0 opacity-75">Data dicatatkan pada {{ $receipt->created_at->format('d M Y H:i') }}</p>
            </div>

            <div class="px-3 pb-3">
                <div class="row g-3 mb-4">
                    {{-- Supplier --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Supplier</label>
                        <div class="form-control bg-light">{{ $receipt->supplier->name }}</div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Tanggal Datang</label>
                        <div class="form-control bg-light">{{ \Carbon\Carbon::parse($receipt->arrival_date)->format('d F Y') }}
                        </div>
                    </div>

                    {{-- Received By --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Diterima Oleh</label>
                        <div class="form-control bg-light">{{ $receipt->received_by }}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small">Catatan</label>
                        <div class="form-control bg-light" style="min-height: 80px;">
                            {{ $receipt->notes ?? '-' }}
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-3 text-uppercase fw-bold text-secondary">Lampiran Dokumen Fisik</h6>

                <div class="row g-3">
                    @php
                        $docTypes = [
                            'SURAT_JALAN' => 'Surat Jalan',
                            'PO' => 'Purchase Order (PO)',
                            'FAKTUR' => 'Faktur / Invoice',
                        ];
                    @endphp

                    @foreach ($docTypes as $type => $label)
                        @php
                            $doc = $receipt->documents->where('type', $type)->first();
                        @endphp
                        <div class="col-md-4">
                            <div class="card h-100 border-light shadow-sm text-center">
                                <div class="card-header bg-transparent border-0 pb-0">
                                    <span class="fw-bold small">{{ $label }}</span>
                                </div>
                                <div class="card-body p-3">
                                    @if ($doc)
                                        <div class="mb-2">
                                            <span
                                                class="badge bg-secondary mb-2">{{ $doc->document_number ?? 'No. Tidak Ada' }}</span>
                                        </div>
                                        <div class="position-relative">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $label }}"
                                                    class="img-fluid rounded border shadow-sm"
                                                    style="max-height: 200px; object-fit: cover;">
                                            </a>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" download
                                                class="btn btn-sm btn-link text-decoration-none">
                                                <i class="fas fa-download me-1"></i> Unduh Gambar
                                            </a>
                                        </div>
                                    @else
                                        <div
                                            class="py-5 bg-light rounded border border-dashed d-flex flex-column align-items-center justify-content-center">
                                            <i class="fas fa-image-slash fa-2x text-muted mb-2"></i>
                                            <span class="text-muted small">Tidak ada dokumen</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endcomponent
    </div>
@endsection
