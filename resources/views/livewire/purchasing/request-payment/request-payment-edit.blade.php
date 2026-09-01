<div class="container mt-3 mb-5">
    @component('components.card')
    @slot('header')
    <div class="d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Request Payment</span>
        <span class="badge bg-secondary">{{ $payment_number }}</span>
    </div>
    @endslot

    <form wire:submit="update">

        {{-- SECTION 1: INFORMASI UMUM --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Plant / Subsidiary</label>
                <input type="text" readonly value="{{ $payment->subsidiary->name ?? '-' }}" class="form-control bg-light fw-bold">
                <input type="hidden" wire:model="subsidiary_id">
                <small class="text-muted" style="font-size: 0.75rem;">Plant tidak dapat diubah setelah dokumen terbuat.</small>
            </div>

            <div class="col-md-3">
                <label for="division" class="form-label fw-bold small">Divisi <span class="text-danger">*</span></label>
                <input type="text" id="division" wire:model="division" class="form-control @error('division') is-invalid @enderror" placeholder="Nama Divisi">
                @error('division') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small" for="date">Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="date" wire:model.live="date" class="form-control @error('date') is-invalid @enderror">
                @error('date') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small" for="payment_number">Nomor Payment <span class="text-danger">*</span></label>
                <input type="text" id="payment_number" wire:model="payment_number" class="form-control bg-light @error('payment_number') is-invalid @enderror" readonly>
                @error('payment_number') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- SECTION 2: DETAIL PEMBAYARAN --}}
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4 border-top pt-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-list-task me-2"></i>Detail Pembayaran</h5>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center" wire:click="addItem">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
            </button>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center fw-bold small">
                    <tr>
                        <th width="30%">Deskripsi <span class="text-danger">*</span></th>
                        <th width="10%">QTY <span class="text-danger">*</span></th>
                        <th width="10%">Satuan <span class="text-danger">*</span></th>
                        <th width="18%">Harga Satuan <span class="text-danger">*</span></th>
                        <th width="18%">Harga Total</th>
                        <th width="14%">Due Date</th>
                        <th width="5%">Menu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                    <tr>
                        <td>
                            <input type="text" class="form-control form-control-sm @error('items.'.$index.'.item_name') is-invalid @enderror" placeholder="Nama Item" wire:model="items.{{ $index }}.item_name">
                            @error('items.'.$index.'.item_name') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm text-center @error('items.'.$index.'.quantity') is-invalid @enderror" placeholder="Qty" wire:model.live="items.{{ $index }}.quantity">
                            @error('items.'.$index.'.quantity') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-center @error('items.'.$index.'.unit') is-invalid @enderror" placeholder="Satuan" wire:model="items.{{ $index }}.unit">
                            @error('items.'.$index.'.unit') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm text-end @error('items.'.$index.'.unit_price') is-invalid @enderror" placeholder="Harga Satuan" wire:model.live="items.{{ $index }}.unit_price">
                            @error('items.'.$index.'.unit_price') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </td>
                        <td>
                            @php
                            $rowAmount = ((float)($item['quantity'] ?? 0)) * ((float)($item['unit_price'] ?? 0));
                            @endphp
                            <input type="text" class="form-control form-control-sm text-end bg-light" value="Rp {{ number_format($rowAmount, 2, ',', '.') }}" readonly>
                        </td>
                        <td>
                            <input type="date" class="form-control form-control-sm" wire:model="items.{{ $index }}.due_date">
                        </td>
                        <td class="text-center">
                            @if (count($items) > 1)
                            <button type="button" class="btn btn-outline-danger btn-sm border-0" wire:click="removeItem({{ $index }})" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- SECTION 3: PURPOSE & ATTACHMENT --}}
        <div class="row g-3 mb-4 pt-3 border-top">
            <div class="col-md-8">
                <label for="purpose" class="form-label fw-bold small">Note / Keperluan</label>
                <textarea wire:model="purpose" id="purpose" rows="2" placeholder="Note..." class="form-control @error('purpose') is-invalid @enderror"></textarea>
                @error('purpose') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label for="attachment" class="form-label fw-bold small">Lampiran</label>
                <input type="file" wire:model="attachment" id="attachment" class="form-control shadow-sm @error('attachment') is-invalid @enderror">

                @if ($old_attachment)
                <small class="form-text mt-1 d-block">
                    File saat ini: <a href="{{ asset('storage/' . $old_attachment) }}" target="_blank">Lihat Lampiran</a>
                </small>
                @endif

                <div wire:loading wire:target="attachment" class="text-primary small mt-1">
                    <span class="spinner-border spinner-border-sm me-1"></span> Mengunggah file...
                </div>

                <small class="form-text text-muted d-block">Biarkan kosong jika tidak ingin mengubah dokumen.</small>
                @error('attachment') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- SECTION 4: GRAND TOTAL & ACTION BUTTONS --}}
        <div class="row align-items-center border-top pt-3">
            <div class="col-md-6">
                <h4 class="fw-bold mb-0 text-dark">
                    Grand Total: <span class="text-primary">Rp {{ number_format($this->grandTotal, 2, ',', '.') }}</span>
                </h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('request-payment.index') }}" class="btn btn-secondary px-4 me-2" wire:navigate>Batal</a>
                <button type="submit" class="btn btn-success px-4 shadow-sm fw-bold" wire:loading.attr="disabled" wire:target="update">
                    <span wire:loading.remove wire:target="update"><i class="bi bi-save me-1"></i> SIMPAN PERUBAHAN</span>
                    <span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
    @endcomponent
</div>