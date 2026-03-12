@extends('layouts.app')
@section('title', 'Edit Kedatangan Barang')
@section('menuReceipt', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-edit me-2"></i> Edit Kedatangan: {{ $receipt->reference_number }}</span>
                    <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            @endslot

            <div class="bg-primary p-4 text-white rounded-top mb-4">
                <h5 class="mb-1 fw-bold">Update Data Kedatangan</h5>
                <p class="small mb-0 opacity-75">Perbarui informasi atau ganti lampiran dokumen jika diperlukan.</p>
            </div>

            <form id="receiptForm" action="{{ route('receipts.update', $receipt->id) }}" method="POST"
                enctype="multipart/form-data" class="px-3 pb-3">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Supplier</label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $receipt->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
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
                            value="{{ old('arrival_date', $receipt->arrival_date) }}">
                        @error('arrival_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Diterima Oleh</label>
                        <input type="text" name="received_by" class="form-control bg-light"
                            value="{{ $receipt->received_by }}" readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Opsional...">{{ old('notes', $receipt->notes) }}</textarea>
                </div>

                <hr class="my-4">

                <h6 class="mb-3 fw-bold text-secondary text-uppercase"><i class="fas fa-camera me-2"></i> Foto Dokumen Fisik
                </h6>

                <div class="row g-3">
                    @php
                        $docs = [
                            'SURAT_JALAN' => ['label' => 'Surat Jalan'],
                            'PO' => ['label' => 'Purchase Order'],
                            'FAKTUR' => ['label' => 'Faktur / Invoice'],
                        ];
                    @endphp

                    @foreach ($docs as $key => $doc)
                        @php
                            // Ambil data dokumen dari relasi jika ada
                            $existingDoc = $receipt->documents->where('type', $key)->first();
                        @endphp
                        <div class="col-md-4">
                            <div class="card h-100 border-dashed p-3 text-center position-relative doc-card"
                                id="card-{{ $key }}">
                                <label class="form-label fw-bold small mb-2">{{ $doc['label'] }}</label>

                                {{-- Document Number --}}
                                <input type="text" name="doc_numbers[{{ $key }}]"
                                    class="form-control form-control-sm mb-2" placeholder="No. {{ $doc['label'] }}"
                                    value="{{ old("doc_numbers.$key", $existingDoc->document_number ?? '') }}">

                                {{-- Image Preview Area --}}
                                {{-- Jika ada data lama, tampilkan d-block, jika tidak d-none --}}
                                <div class="preview-area mb-2 {{ $existingDoc ? 'd-block' : 'd-none' }}"
                                    id="preview-container-{{ $key }}">
                                    <img src="{{ $existingDoc ? asset('storage/' . $existingDoc->file_path) : '' }}"
                                        id="img-{{ $key }}" class="img-thumbnail shadow-sm"
                                        style="max-height: 120px;">

                                    @if ($existingDoc)
                                        <div class="text-xs text-success mt-1"><i class="fas fa-check-circle"></i> Terupload
                                        </div>
                                    @endif

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
                                <small class="text-muted text-xs">Pilih file baru untuk mengganti foto lama</small>

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
                        <i class="fas fa-sync me-2"></i> Update Data Kedatangan
                    </button>
                </div>
            </form>
        @endcomponent
    </div>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.2.1/compressor.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.compress-input');
            const btnSubmit = document.getElementById('btnSubmit');

            inputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const key = this.dataset.key;
                    const file = e.target.files[0];
                    const progress = document.getElementById(`progress-${key}`);
                    const previewContainer = document.getElementById(`preview-container-${key}`);
                    const previewImg = document.getElementById(`img-${key}`);

                    if (!file) return;

                    // Tampilkan Loading & Disable Tombol Submit
                    progress.classList.remove('d-none');
                    btnSubmit.disabled = true;

                    new Compressor(file, {
                        quality: 0.6,
                        maxWidth: 1600,
                        success(result) {
                            // Masukkan hasil kompresi ke input file
                            const dt = new DataTransfer();
                            dt.items.add(new File([result], result.name, {
                                type: result.type
                            }));
                            input.files = dt.files;

                            // Update Preview (Ganti gambar lama dengan yang baru dikompres)
                            previewImg.src = URL.createObjectURL(result);
                            previewContainer.classList.remove('d-none');

                            // Sembunyikan Loading & Enable Tombol
                            progress.classList.add('d-none');
                            btnSubmit.disabled = false;

                            console.log(
                                `Compressed ${key}: ${(result.size/1024).toFixed(2)} KB`
                            );
                        },
                        error(err) {
                            console.error(err.message);
                            alert('Gagal mengompres gambar: ' + err.message);
                            progress.classList.add('d-none');
                            btnSubmit.disabled = false;
                        },
                    });
                });
            });

            // Logika Hapus Foto (Frontend saja)
            document.querySelectorAll('.btn-remove-img').forEach(btn => {
                btn.addEventListener('click', function() {
                    const key = this.dataset.key;
                    const input = document.querySelector(`.compress-input[data-key="${key}"]`);
                    const previewContainer = document.getElementById(`preview-container-${key}`);
                    const previewImg = document.getElementById(`img-${key}`);

                    input.value = ""; // Kosongkan input file
                    previewContainer.classList.add('d-none');
                    previewImg.src = ""; // Bersihkan src
                });
            });
        });
    </script>
@endpush
