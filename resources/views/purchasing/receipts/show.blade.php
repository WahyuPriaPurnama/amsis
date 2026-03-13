@extends('layouts.app')
@section('title', 'Detail Kedatangan Barang')
@section('menuReceipt', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle me-2"></i> Detail Kedatangan</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        @can('receipts.edit')
                            <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        @endcan
                        @can('receipts.delete')
                            <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endslot

            {{-- Info Banner --}}
            <div class="bg-primary p-4 text-white rounded shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1 fw-bold">{{ $receipt->reference_number }}</h4>
                        <p class="small mb-0 opacity-75">
                            <i class="fas fa-calendar-alt me-1"></i> Dicatat pada
                            {{ $receipt->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-white text-primary px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> Terverifikasi
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-2">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Supplier</label>
                        <p class="form-control-plaintext border-bottom pb-2 fw-bold text-dark">
                            <i class="fas fa-building me-2 text-secondary"></i>{{ $receipt->supplier->name }}
                        </p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Tanggal Datang</label>
                        <p class="form-control-plaintext border-bottom pb-2 text-dark">
                            <i
                                class="fas fa-truck me-2 text-secondary"></i>{{ \Carbon\Carbon::parse($receipt->arrival_date)->format('d F Y') }}
                        </p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Diterima Oleh</label>
                        <p class="form-control-plaintext border-bottom pb-2 text-dark">
                            <i class="fas fa-user-check me-2 text-secondary"></i>{{ $receipt->received_by }}
                        </p>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold text-muted small text-uppercase">Catatan Internal</label>
                    <div class="p-3 bg-light rounded border border-start-0 border-end-0 border-top-0 border-3 border-primary">
                        {{ $receipt->notes ?: 'Tidak ada catatan tambahan.' }}
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-4 text-uppercase fw-bold text-secondary d-flex align-items-center">
                    <i class="fas fa-camera me-2"></i> Arsip Digital Dokumen
                </h6>

                <div class="row g-4">
                    @php
                        $docTypes = [
                            'SURAT_JALAN' => ['label' => 'Surat Jalan', 'icon' => 'fa-file-invoice'],
                            'PO' => ['label' => 'Purchase Order (PO)', 'icon' => 'fa-file-signature'],
                            'FAKTUR' => ['label' => 'Faktur / Invoice', 'icon' => 'fa-file-invoice-dollar'],
                        ];
                    @endphp

                    @foreach ($docTypes as $type => $info)
                        @php
                            $doc = $receipt->documents->where('type', $type)->first();
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="card-header bg-light py-3 border-0">
                                    <h6 class="mb-0 fw-bold small">
                                        <i class="fas {{ $info['icon'] }} me-2 text-primary"></i>{{ $info['label'] }}
                                    </h6>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center p-3">
                                    @if ($doc)
                                        <div class="text-center mb-3">
                                            <span class="badge bg-dark rounded-pill px-3 py-2 mb-3">
                                                {{ $doc->document_number ?: 'Tanpa Nomor' }}
                                            </span>
                                            <div class="position-relative hover-zoom">
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $doc->file_path) }}"
                                                        class="img-fluid rounded shadow-sm border"
                                                        style="max-height: 200px; width: 100%; object-fit: cover;">
                                                </a>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" download
                                            class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-download me-1"></i> Unduh File
                                        </a>
                                    @else
                                        <div class="text-center py-4 border-dashed rounded bg-white">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-light mb-2"></i>
                                            <p class="text-muted small mb-0">Dokumen tidak terlampir</p>
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

<style>
    .border-dashed {
        border: 2px dashed #dee2e6 !important;
    }

    .hover-zoom img {
        transition: transform .3s ease;
    }

    .hover-zoom:hover img {
        transform: scale(1.02);
        filter: brightness(0.9);
    }
</style>
