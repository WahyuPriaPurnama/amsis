<div>
    <div class="container-fluid mt-3">
        <x-card>
            <x-slot:header>
                <span class="fw-bold text-uppercase">Create Request Payment</span>
            </x-slot:header>

            <form wire:submit="save">
                {{-- SECTION 1: HEADER --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="subsidiary_id" class="form-label fw-bold small">Plant <span class="text-danger">*</span></label>
                        <select wire:model.live="subsidiary_id" id="subsidiary_id" class="form-select @error('subsidiary_id') is-invalid @enderror">
                            <option value="">Pilih Plant</option>
                            @foreach ($subsidiaries as $subsidiary)
                            <option value="{{ $subsidiary->id }}">{{ $subsidiary->name }}</option>
                            @endforeach
                        </select>
                        @error('subsidiary_id')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="division" class="form-label fw-bold small">Divisi <span class="text-danger">*</span></label>
                        <input type="text" id="division" wire:model="division" class="form-control @error('division') is-invalid @enderror" placeholder="Nama Divisi">
                        @error('division')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="date" class="form-label fw-bold small">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="date" wire:model.live="date" class="form-control @error('date') is-invalid @enderror">
                        @error('date')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="payment_number" class="form-label fw-bold small d-flex justify-content-between align-items-center">
                            <span>Nomor Payment <span class="text-danger">*</span></span>
                        </label>
                        <div class="position-relative">
                            <input type="text"
                                id="payment_number"
                                wire:model="payment_number"
                                wire:loading.class="opacity-50"
                                wire:target="subsidiary_id, date"
                                class="form-control bg-light fw-bold @error('payment_number') is-invalid @enderror"
                                readonly
                                placeholder="Terisi Otomatis">

                            <div wire:loading wire:target="subsidiary_id, date" class="position-absolute top-50 end-0 translate-middle-y me-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                        @error('payment_number')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- SECTION 2: DYNAMIC ITEMS --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-list-task me-2"></i>Detail Pembayaran
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" wire:click="addItem">
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
                            <tr wire:key="item-row-{{ $index }}">
                                <td>
                                    <input type="text" class="form-control form-control-sm @error(" items.$index.item_name") is-invalid @enderror" placeholder="Nama Item" wire:model="items.{{ $index }}.item_name">
                                    @error("items.$index.item_name")
                                    <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-center @error(" items.$index.quantity") is-invalid @enderror" placeholder="Qty" wire:model.live="items.{{ $index }}.quantity">
                                    @error("items.$index.quantity")
                                    <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-center @error(" items.$index.unit") is-invalid @enderror" placeholder="Satuan" wire:model="items.{{ $index }}.unit">
                                    @error("items.$index.unit")
                                    <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-end @error(" items.$index.unit_price") is-invalid @enderror" placeholder="Harga Satuan" wire:model.live="items.{{ $index }}.unit_price">
                                    @error("items.$index.unit_price")
                                    <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    @php
                                    $subtotal = ((float) ($items[$index]['quantity'] ?? 0)) * ((float) ($items[$index]['unit_price'] ?? 0));
                                    @endphp
                                    <input type="text" class="form-control form-control-sm text-end bg-light" value="{{ number_format($subtotal, 2, ',', '.') }}" readonly>
                                </td>
                                <td>
                                    <input type="date" class="form-control form-control-sm @error(" items.$index.due_date") is-invalid @enderror" wire:model="items.{{ $index }}.due_date">
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
                        <label for="purpose" class="form-label fw-bold small">Catatan / Note</label>
                        <textarea id="purpose" rows="2" placeholder="Tulis catatan atau instruksi khusus..." wire:model="purpose" class="form-control @error('purpose') is-invalid @enderror"></textarea>
                        @error('purpose')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="attachment" class="form-label fw-bold small">Lampiran Utama</label>
                        <input type="file" id="attachment" wire:model="attachment" class="form-control shadow-sm @error('attachment') is-invalid @enderror">
                        <small class="form-text text-muted d-block">Contoh: Invoice, kwitansi, dll. (Maks 5MB)</small>
                        @error('attachment')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- SECTION 4: GRAND TOTAL & ACTION --}}
                <div class="row align-items-center border-top pt-3">
                    <div class="col-md-6">
                        <h4 class="fw-bold mb-0 text-dark">
                            Grand Total: <span class="text-primary">Rp {{ number_format($this->grandTotal, 2, ',', '.') }}</span>
                        </h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="submit" class="btn btn-success px-5 fw-bold shadow" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">SIMPAN REQUEST PAYMENT</span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>