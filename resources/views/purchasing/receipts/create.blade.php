@extends('layouts.app')
@section('title', 'Input Kedatangan Barang')
@section('menuReceipt', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck-loading me-2"></i> Input Kedatangan Barang</span>
                    <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list me-1"></i> Daftar Kedatangan
                    </a>
                </div>
            @endslot

            <div class="bg-primary p-4 text-white rounded-top mb-4">
                <h5 class="mb-1 fw-bold">Input Kedatangan Barang</h5>
                <p class="small mb-0 opacity-75">Gunakan kamera untuk mencatat dokumen fisik secara akurat.</p>
            </div>

            <form id="receiptForm" action="{{ route('receipts.store') }}" method="POST" enctype="multipart/form-data"
                class="px-3 pb-3">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Supplier</label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            <option value="" selected disabled>-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Tanggal Datang</label>
                        <input type="date" name="arrival_date"
                            class="form-control @error('arrival_date') is-invalid @enderror"
                            value="{{ old('arrival_date', date('Y-m-d')) }}">
                        @error('arrival_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Diterima Oleh</label>
                        <input type="text" name="received_by" class="form-control bg-light"
                            value="{{ auth()->user()->name ?? 'System' }}" readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Opsional...">{{ old('notes') }}</textarea>
                </div>

                <hr class="my-4">

                <h6 class="mb-3 fw-bold text-secondary text-uppercase"><i class="fas fa-camera me-2"></i> Foto Dokumen Fisik
                </h6>

                <div class="row g-3">
                    @php
                        $docs = [
                            'SURAT_JALAN' => ['label' => 'Surat Jalan', 'icon' => 'fa-file-invoice'],
                            'PO' => ['label' => 'Purchase Order', 'icon' => 'fa-file-signature'],
                            'FAKTUR' => ['label' => 'Faktur / Invoice', 'icon' => 'fa-file-invoice-dollar'],
                        ];
                    @endphp

                    @foreach ($docs as $key => $doc)
                        <div class="col-md-4">
                            <div class="card h-100 border-dashed p-3 text-center position-relative doc-card"
                                id="card-{{ $key }}">
                                <label class="form-label fw-bold small mb-2">{{ $doc['label'] }}</label>

                                {{-- Document Number --}}
                                <input type="text" name="doc_numbers[{{ $key }}]"
                                    class="form-control form-control-sm mb-2" placeholder="No. {{ $doc['label'] }}"
                                    value="{{ old("doc_numbers.$key") }}">

                                {{-- Image Preview Area --}}
                                <div class="preview-area mb-2 d-none" id="preview-container-{{ $key }}">
                                    <img src="" id="img-{{ $key }}" class="img-thumbnail shadow-sm"
                                        style="max-height: 120px;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute btn-remove-img"
                                        data-key="{{ $key }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                {{-- Input File --}}
                                <div class="input-group input-group-sm mb-1">
                                    <input type="file" name="files[{{ $key }}]" class="form-control compress-input"
                                        accept="image/*" capture="environment" data-key="{{ $key }}">
                                </div>

                                <div class="progress d-none mt-2" id="progress-{{ $key }}" style="height: 5px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                        style="width: 100%"></div>
                                </div>

                                @error("files.$key")
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg w-100 shadow">
                        <i class="fas fa-save me-2"></i> Simpan Data Kedatangan
                    </button>
                </div>
            </form>
        @endcomponent
    </div>

    <style>
        .border-dashed {
            border: 2px dashed #dee2e6 !important;
            transition: all 0.3s;
        }

        .doc-card:hover {
            border-color: #0d6efd !important;
            background-color: #f8f9fa;
        }

        .btn-remove-img {
            top: 40px;
            right: 20px;
            border-radius: 50%;
            padding: 2px 6px;
        }

        .text-xs {
            font-size: 0.75rem;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.2.1/compressor.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.compress-input');
            const form = document.getElementById('receiptForm');
            const btnSubmit = document.getElementById('btnSubmit');

            inputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const key = this.dataset.key;
                    const file = e.target.files[0];
                    const progress = document.getElementById(`progress-${key}`);
                    const previewContainer = document.getElementById(`preview-container-${key}`);
                    const previewImg = document.getElementById(`img-${key}`);

                    if (!file) return;

                    // Show loading
                    progress.classList.remove('d-none');
                    btnSubmit.disabled = true;

                    new Compressor(file, {
                        quality: 0.6,
                        maxWidth: 1600,
                        success(result) {
                            // Update input with compressed file
                            const dt = new DataTransfer();
                            dt.items.add(new File([result], result.name, {
                                type: result.type
                            }));
                            input.files = dt.files;

                            // Show preview
                            previewImg.src = URL.createObjectURL(result);
                            previewContainer.classList.remove('d-none');
                            progress.classList.add('d-none');
                            btnSubmit.disabled = false;

                            console.log(
                                `Compressed ${key}: ${(result.size/1024).toFixed(2)} KB`
                                );
                        },
                        error(err) {
                            console.error(err.message);
                            progress.classList.add('d-none');
                            btnSubmit.disabled = false;
                        },
                    });
                });
            });

            // Remove Image Logic
            document.querySelectorAll('.btn-remove-img').forEach(btn => {
                btn.addEventListener('click', function() {
                    const key = this.dataset.key;
                    const input = document.querySelector(`.compress-input[data-key="${key}"]`);
                    const previewContainer = document.getElementById(`preview-container-${key}`);

                    input.value = ""; // Clear file input
                    previewContainer.classList.add('d-none');
                });
            });
        });
    </script>
@endpush
