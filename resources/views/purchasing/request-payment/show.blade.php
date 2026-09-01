<div>
    <div class="container mt-3 mb-5">
        @component('components.card')
        @slot('header')
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Detail Request Payment</span>
            <span class="badge bg-primary fs-6">{{ $payment->payment_number }}</span>
        </div>
        @endslot

        {{-- Status Banner & Action Header --}}
        <div class="row align-items-center mb-4 g-3">
            <div class="col-12 col-md-8">
                @switch($payment->status)
                @case('pending')
                <div class="alert alert-warning mb-0 d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-hourglass-split fs-4 me-2"></i>
                    <div>
                        <strong class="d-block">Status: Menunggu Acc Direktur</strong>
                        <small class="text-muted">Dokumen sedang menunggu persetujuan tingkat pertama.</small>
                    </div>
                </div>
                @break

                @case('approved_by_manager')
                <div class="alert alert-info mb-0 d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-hourglass-split fs-4 me-2"></i>
                    <div>
                        <strong class="d-block">Status: Menunggu Acc Direktur OP</strong>
                        <small class="text-muted">Telah disetujui Direktur, menunggu persetujuan akhir Direktur Operasional.</small>
                    </div>
                </div>
                @break

                @case('approved_by_bod')
                <div class="alert alert-success mb-0 d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                    <div>
                        <strong class="d-block">Status: Approved (Selesai)</strong>
                        <small>Dokumen telah disetujui sepenuhnya oleh Direksi.</small>
                    </div>
                </div>
                @break

                @default
                <div class="alert alert-secondary mb-0 d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-info-circle fs-4 me-2"></i>
                    <div>
                        <strong class="d-block">Status: {{ strtoupper($payment->status) }}</strong>
                    </div>
                </div>
                @break
                @endswitch
            </div>

            <div class="col-12 col-md-4 d-flex justify-content-md-end justify-content-start gap-2">
                @can('request-payment.edit')
                <a href="{{ route('request-payment.edit', $payment->id) }}" class="btn btn-warning btn-sm shadow-sm d-inline-flex align-items-center" wire:navigate>
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @endcan

                @can('request-payment.delete')
                <button type="button" class="btn btn-danger btn-sm rounded-3" wire:click="deletePayment" wire:confirm="Yakin ingin menghapus Request Payment ini?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deletePayment"><i class="bi bi-trash-fill me-1"></i> Hapus</span>
                    <span wire:loading wire:target="deletePayment">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Menghapus...
                    </span>
                </button>
                @endcan
            </div>

            {{-- Info Revisi Document --}}
            @if ($payment->revision_count > 0)
            <div class="col-12">
                <div class="alert alert-light border py-2 px-3 d-flex align-items-center mb-0" role="alert">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>
                    <span class="small">
                        <strong>Dokumen telah direvisi {{ $payment->revision_count }}x</strong>
                        <span class="text-muted ms-1">(Terakhir diperbarui: {{ \Carbon\Carbon::parse($payment->last_revised_at)->format('d M Y, H:i') }} WIB)</span>
                    </span>
                </div>
            </div>
            @endif
        </div>

        {{-- Data Header Dokumen --}}
        <div class="row g-3 p-3 bg-light border rounded mx-0 mb-4 shadow-sm">
            <div class="col-6 col-md-3">
                <span class="d-block small text-muted">Plant / Subsidiary</span>
                <strong class="fs-6 text-dark">{{ $payment->subsidiary->name ?? '-' }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <span class="d-block small text-muted">Divisi</span>
                <strong class="fs-6 text-dark">{{ $payment->division }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <span class="d-block small text-muted">Tanggal Dokumen</span>
                <strong class="fs-6 text-dark">{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <span class="d-block small text-muted">Nomor Payment</span>
                <strong class="fs-6 text-primary">{{ $payment->payment_number }}</strong>
            </div>
        </div>

        {{-- Tabel Detail Items --}}
        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-list-stars me-1"></i> Daftar Barang / Detail Pembayaran</h6>
        <div class="table-responsive mb-4">
            <table class="table table-hover border align-middle">
                <thead class="table-dark text-center align-middle">
                    <tr>
                        <th width="50">No.</th>
                        <th>Nama Barang / Deskripsi</th>
                        <th width="80">Qty</th>
                        <th width="100">Satuan</th>
                        <th width="150" class="text-end">Harga Satuan</th>
                        <th width="170" class="text-end">Jumlah Harga</th>
                        <th width="120" class="text-center">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payment->items as $item)
                    <tr>
                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                        <td class="text-end">Rp {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($item->amount, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Grand Total:</td>
                        <td class="text-end fw-bold fs-6 text-primary">Rp {{ number_format($payment->grand_total, 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Catatan / Note --}}
        <div class="p-3 bg-light border rounded mb-4">
            <strong class="d-block small text-muted mb-1"><i class="bi bi-sticky me-1"></i> Catatan / Note:</strong>
            <p class="mb-0 text-dark">{{ $payment->purpose ?? '-' }}</p>
        </div>

        {{-- Kartu Jejak Otorisasi / Approval --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 border rounded bg-white text-center shadow-sm h-100">
                    <span class="d-block text-muted small mb-1">Dibuat Oleh</span>
                    <strong class="d-block text-dark">{{ $payment->requester->name ?? '-' }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded bg-white text-center shadow-sm h-100">
                    <span class="d-block text-muted small mb-1">Direktur</span>
                    <strong class="d-block text-dark">{{ $payment->plantManager->name ?? '-' }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded bg-white text-center shadow-sm h-100">
                    <span class="d-block text-muted small mb-1">Direktur Operasional</span>
                    <strong class="d-block text-dark">{{ $payment->bod->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        {{-- Tombol Navigasi & Aksi Bottom --}}
        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('request-payment.index') }}" class="btn btn-secondary px-4" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @php
                $user = auth()->user();

                // Logic Unapprove
                $canUnapprove = false;
                if ($payment->status === 'approved_by_manager' && ($user->can('request-payment.approve-manager') || $user->can('request-payment.unapprove') || $user->hasRole('super-admin'))) {
                $canUnapprove = true;
                } elseif ($payment->status === 'approved_by_bod' && ($user->can('request-payment.approve-bod') || $user->can('request-payment.unapprove') || $user->hasRole('super-admin'))) {
                $canUnapprove = true;
                }

                // Logic Approve
                $canApproveManager = ($payment->status === 'pending' && ($user->can('request-payment.approve-manager') || $user->hasRole('super-admin')));
                $canApproveBod = ($payment->status === 'approved_by_manager' && ($user->can('request-payment.approve-bod') || $user->hasRole('super-admin')));
                @endphp

                {{-- Button Unapprove --}}
                @if ($canUnapprove)
                <button type="button"
                    class="btn btn-outline-warning"
                    wire:click="unapprove"
                    wire:confirm="Apakah Anda yakin ingin membatalkan persetujuan ini?"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="unapprove"><i class="bi bi-arrow-counterclockwise me-1"></i> Unapprove</span>
                    <span wire:loading wire:target="unapprove"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
                </button>
                @endif

                {{-- Button Lampiran --}}
                @if ($payment->attachment)
                <a href="{{ route('request-payment.attachment', $payment->id) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-paperclip me-1"></i> Lampiran
                </a>
                @endif

                {{-- Button Cetak PDF --}}
                <x-buttons.pdf href="{{ route('request-payment.pdf', $payment->id) }}"></x-buttons.pdf>

                {{-- Button Approve Direktur --}}
                @if ($canApproveManager)
                <button type="button"
                    class="btn btn-success fw-bold shadow-sm"
                    wire:click="approveManager"
                    wire:confirm="Apakah Anda yakin ingin menyetujui Request Payment ini?"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="approveManager"><i class="bi bi-check2-circle me-1"></i> Acc Direktur</span>
                    <span wire:loading wire:target="approveManager"><span class="spinner-border spinner-border-sm me-1"></span>Menyetujui...</span>
                </button>
                @endif

                {{-- Button Approve Direktur OP --}}
                @if ($canApproveBod)
                <button type="button"
                    class="btn btn-success fw-bold shadow-sm"
                    wire:click="approveBod"
                    wire:confirm="Apakah Anda yakin ingin menyetujui Request Payment ini?"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="approveBod"><i class="bi bi-check2-circle me-1"></i> Acc Direktur Op</span>
                    <span wire:loading wire:target="approveBod"><span class="spinner-border spinner-border-sm me-1"></span>Menyetujui...</span>
                </button>
                @endif
            </div>
        </div>
        @endcomponent
    </div>
</div>