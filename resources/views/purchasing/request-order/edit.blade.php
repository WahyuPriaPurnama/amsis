<div>
    <div class="container mt-3 mb-5">
        @component('components.card')
        @slot('header')
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-pencil-square me-2"></i>Revisi Request Order</span>
            <span class="badge bg-secondary">{{ $order->request_number }}</span>
        </div>
        @endslot

        {{-- Form Update --}}
        <form wire:submit.prevent="update">
            {{-- Alert Validasi Error --}}
            @if ($errors->any())
            <div class="alert alert-danger mb-4 shadow-sm">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Alert Informasi Revisi --}}
            <div class="alert alert-warning py-2 px-3 mb-4 d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="small">
                    <strong>Perhatian:</strong> Melakukan revisi akan mencatat jejak perubahan serta menambah jumlah revisi dokumen ini.
                </div>
            </div>

            {{-- Info Umum --}}
            <div class="row g-3 p-3 bg-light rounded border mb-4">
                <h6 class="fw-bold mb-2 text-dark border-bottom pb-2"><i class="bi bi-card-heading me-1"></i> Informasi Umum</h6>

                {{-- Plant / Subsidiary Readonly --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Plant / Subsidiary</label>
                    <input type="text" readonly value="{{ $order->subsidiary->name ?? '-' }}" class="form-control form-control-sm bg-light fw-bold">
                    <input type="hidden" wire:model="subsidiary_id">
                    <small class="text-muted" style="font-size: 0.75rem;">Plant tidak dapat diubah setelah dokumen terbuat.</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Divisi <span class="text-danger">*</span></label>
                    <input type="text" wire:model="division" class="form-control form-control-sm @error('division') is-invalid @enderror" placeholder="Contoh: IT / Maintenance">
                    @error('division') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Tanggal Request <span class="text-danger">*</span></label>
                    <input type="date" wire:model="request_date" class="form-control form-control-sm @error('request_date') is-invalid @enderror">
                    @error('request_date') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Nomor RO</label>
                    <input type="text" readonly value="{{ $order->request_number }}" class="form-control form-control-sm bg-light fw-bold">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Lampiran Header (PDF/Gambar)</label>
                    <input type="file" wire:model="attachment" class="form-control form-control-sm @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                    @if ($old_attachment)
                    <div class="form-text mt-1">
                        <small class="text-muted me-1">Lampiran saat ini:</small>
                        <a href="{{ asset('storage/' . $old_attachment) }}" target="_blank" class="small text-decoration-none">
                            <i class="bi bi-file-earmark-arrow-down"></i> Lihat Lampiran
                        </a>
                    </div>
                    @endif
                    @error('attachment') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Catatan / Keperluan <span class="text-danger">*</span></label>
                    <textarea wire:model="purpose" class="form-control form-control-sm @error('purpose') is-invalid @enderror" rows="2" placeholder="Tuliskan alasan atau catatan kebutuhan barang..."></textarea>
                    @error('purpose') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Daftar Barang --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>Daftar Barang</h6>
                <button type="button" class="btn btn-success btn-sm shadow-sm" wire:click="addItem">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Barang
                </button>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-hover border align-middle">
                    <thead class="table-dark">
                        <tr class="text-center align-middle">
                            <th width="40">No.</th>
                            <th width="35%">Nama Barang <span class="text-danger">*</span></th>
                            <th width="15%">Qty Req <span class="text-danger">*</span></th>
                            <th width="15%">Satuan <span class="text-danger">*</span></th>
                            <th width="30%">Keterangan / Spec</th>
                            <th width="60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                        <tr wire:key="ro-item-{{ $index }}">
                            <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <input type="text" wire:model="items.{{ $index }}.item_name" class="form-control form-control-sm @error('items.'.$index.'.item_name') is-invalid @enderror" placeholder="Nama Barang">
                                @error('items.'.$index.'.item_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <input type="number" step="any" wire:model="items.{{ $index }}.quantity" class="form-control form-control-sm text-center @error('items.'.$index.'.quantity') is-invalid @enderror" min="1">
                                @error('items.'.$index.'.quantity') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <input type="text" wire:model="items.{{ $index }}.unit" class="form-control form-control-sm text-center @error('items.'.$index.'.unit') is-invalid @enderror" placeholder="Pcs/Ltr/Kg">
                                @error('items.'.$index.'.unit') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <input type="text" wire:model="items.{{ $index }}.remark" class="form-control form-control-sm" placeholder="Catatan Opsional">
                            </td>
                            <td class="text-center">
                                @if (count($items) > 1)
                                <button type="button" class="btn btn-outline-danger btn-sm p-1 py-0" wire:click="removeItem({{ $index }})" title="Hapus Baris">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tombol Aksi Bawah --}}
            <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-3">
                <a href="{{ route('request-order.show', $order->id) }}" class="btn btn-secondary px-4" wire:navigate>Batal</a>
                <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update">
                        <i class="bi bi-check-circle me-1"></i> Simpan Revisi
                    </span>
                    <span wire:loading wire:target="update">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
        @endcomponent
    </div>
</div>