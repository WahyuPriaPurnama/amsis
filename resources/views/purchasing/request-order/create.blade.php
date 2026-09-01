<div>
    <div class="container mt-3">
        <form wire:submit.prevent="save">
            @component('components.card')
            @slot('header')
            <span class="fw-bold text-uppercase">Tambah Request Order</span>
            @endslot

            {{-- SECTION 1: HEADER --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Plant <span class="text-danger">*</span></label>
                    <select wire:model.live="subsidiary_id" class="form-select @error('subsidiary_id') is-invalid @enderror">
                        <option value="">Pilih Plant...</option>
                        @foreach ($subsidiaries as $subsidiary)
                        <option value="{{ $subsidiary->id }}">{{ $subsidiary->name }}</option>
                        @endforeach
                    </select>
                    @error('subsidiary_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Divisi <span class="text-danger">*</span></label>
                    <input type="text" wire:model="division" class="form-control @error('division') is-invalid @enderror" placeholder="Nama Divisi">
                    @error('division') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" wire:model.live="request_date" class="form-control @error('request_date') is-invalid @enderror">
                    @error('request_date') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                        <span>Nomor RO</span>
                    </label>

                    <div class="position-relative">
                        <input type="text"
                            readonly
                            wire:model="request_number"
                            wire:loading.class="opacity-50"
                            wire:target="subsidiary_id, request_date"
                            class="form-control bg-light @error('request_number') is-invalid @enderror"
                            placeholder="Terisi Otomatis">

                        {{-- Spinner overlay di dalam bagian kanan input --}}
                        <div wire:loading wire:target="subsidiary_id, request_date" class="position-absolute top-50 end-0 translate-middle-y me-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    @error('request_number')
                    <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            {{-- SECTION 2: DYNAMIC ITEMS --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-list-task me-2"></i>Daftar Barang</h5>
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" wire:click="addItem" wire:loading.attr="disabled" wire:target="addItem">
                    <span wire:loading.remove wire:target="addItem"><i class="bi bi-plus-circle me-1"></i> Tambah Baris</span>
                    <span wire:loading wire:target="addItem"><span class="spinner-border spinner-border-sm me-1"></span> Menambahkan...</span>
                </button>
            </div>

            @foreach ($items as $index => $item)
            <div class="card mb-3 border shadow-sm item-row" wire:key="item-row-{{ $index }}">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" wire:model="items.{{ $index }}.item_name" class="form-control @error('items.'.$index.'.item_name') is-invalid @enderror" placeholder="Input nama barang...">
                            @error('items.'.$index.'.item_name') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-1 text-center">
                            <label class="small text-muted fw-bold d-block text-center">Qty <span class="text-danger">*</span></label>
                            <input type="number" wire:model="items.{{ $index }}.quantity" class="form-control text-center @error('items.'.$index.'.quantity') is-invalid @enderror" min="1">
                            @error('items.'.$index.'.quantity') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="small text-muted fw-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" wire:model="items.{{ $index }}.unit" class="form-control @error('items.'.$index.'.unit') is-invalid @enderror" placeholder="Pcs/Set/Ltr">
                            @error('items.'.$index.'.unit') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Keterangan / Spec</label>
                            <input type="text" wire:model="items.{{ $index }}.remark" class="form-control" placeholder="Opsional">
                        </div>

                        <div class="col-md-1 text-center">
                            @if (count($items) > 1)
                            <button type="button" class="btn btn-outline-danger border-0" wire:click="removeItem({{ $index }})" title="Hapus baris">
                                <i class="bi bi-trash-fill fs-5"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- SECTION 3: FOOTER --}}
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-muted small uppercase">Catatan Tambahan (Header)</label>
                    <textarea wire:model="purpose" rows="2" class="form-control" placeholder="Tulis alasan permintaan atau instruksi khusus..."></textarea>
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label fw-bold text-muted small d-block">Lampiran Utama RO</label>
                    <input type="file" wire:model="attachment" class="form-control shadow-sm">
                    @error('attachment') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-5 fw-bold shadow" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save"><i class="bi bi-save me-1"></i> SIMPAN REQUEST ORDER</span>
                    <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
                </button>
            </div>
            @endcomponent
        </form>
    </div>
</div>