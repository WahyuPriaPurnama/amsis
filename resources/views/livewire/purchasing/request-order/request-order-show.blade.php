<div>
    <div class="container mt-3">
        @component('components.card')
        @slot('header')
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-info-circle me-2"></i>Detail Request Order</span>
            <span class="small">{{ $order->request_number }}</span>
        </div>
        @endslot

        {{-- Status Header Badge --}}
        <div class="row mt-3">
            <div class="col-md-8">
                @php
                $statusConfig = [
                'pending' => ['bg' => 'warning', 'text' => 'dark', 'label' => 'Menunggu Approval Kepala Divisi'],
                'approved_by_div_head' => ['bg' => 'warning', 'text' => 'dark', 'label' => 'Menunggu Approval Plant Manager'],
                'approved_by_manager' => ['bg' => 'warning', 'text' => 'dark', 'label' => 'Menunggu Approval BOD'],
                'approved_by_bod' => ['bg' => 'success', 'text' => 'white', 'label' => 'Approved'],
                'completed' => ['bg' => 'primary', 'text' => 'white', 'label' => 'Selesai (Barang Diterima)'],
                'partial' => ['bg' => 'info', 'text' => 'dark', 'label' => 'Diterima Sebagian'],
                ];
                $current = $statusConfig[$order->status] ?? ['bg' => 'secondary', 'text' => 'white', 'label' => $order->status];
                @endphp
                <div class="badge bg-{{ $current['bg'] }} text-{{ $current['text'] }} p-2 fs-6 w-100 shadow-sm">
                    {{ strtoupper($current['label']) }}
                </div>
            </div>
            <div class="col-md-4 mt-2 mt-md-0 d-flex justify-content-md-end gap-1">
                @can('request-order.edit')
                <a href="{{ route('request-order.edit', ['requestOrder' => $order->id]) }}" class="btn btn-warning btn-sm rounded-3" wire:navigate>
                    <i class="bi bi-pencil-square me-1"></i> Revisi
                </a>
                @endcan

                @can('request-order.delete')
                <button type="button" class="btn btn-danger btn-sm rounded-3" wire:click="deleteOrder" wire:confirm="Yakin ingin menghapus Request Order ini?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deleteOrder"><i class="bi bi-trash-fill me-1"></i> Hapus</span>
                    <span wire:loading wire:target="deleteOrder">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Menghapus...
                    </span>
                </button>
                @endcan
            </div>
        </div>

        {{-- Info Umum --}}
        <div class="row g-3 p-3 bg-light rounded border my-4">
            <div class="col-6 col-md-3">
                <label class="text-muted small d-block">Plant</label>
                <span class="fw-bold">{{ $order->subsidiary->name ?? '-' }}</span>
            </div>
            <div class="col-6 col-md-3">
                <label class="text-muted small d-block">Divisi</label>
                <span class="fw-bold">{{ $order->division }}</span>
            </div>
            <div class="col-6 col-md-3">
                <label class="text-muted small d-block">Tanggal Request</label>
                <span class="fw-bold">{{ \Carbon\Carbon::parse($order->request_date)->format('d M Y') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <label class="text-muted small d-block">Dibuat Oleh</label>
                <span class="fw-bold">{{ $order->requester->name ?? '-' }}</span>
            </div>

            @if ($order->revision_count > 0)
            <div class="alert alert-info py-2 px-3 mt-3 d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-pencil-square me-2 fs-5"></i>
                <div>
                    <strong>Dokumen telah direvisi {{ $order->revision_count }}x</strong>
                    <span class="text-muted small ms-2">(Terakhir diperbarui: {{ \Carbon\Carbon::parse($order->last_revised_at)->format('d M Y, H:i') }} WIB)</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Tabel Daftar Barang & Realisasi --}}
        @php
        $canReceive = in_array($order->status, ['approved_by_div_head', 'approved_by_manager', 'approved_by_bod', 'partial', 'completed']) && (auth()->user()->can('request-order.receive') || auth()->user()->hasRole('super-admin'));
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>Daftar Barang & Realisasi</h5>
            @if ($canReceive)
            <button type="button" wire:click="updateReceive" class="btn btn-primary btn-sm shadow-sm" wire:loading.attr="disabled" wire:target="updateReceive">
                <span wire:loading.remove wire:target="updateReceive"><i class="bi bi-check-circle me-1"></i> Simpan Penerimaan</span>
                <span wire:loading wire:target="updateReceive">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Menyimpan...
                </span>
            </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover border align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">No.</th>
                        <th>Nama Barang</th>
                        <th class="text-center" width="90">Qty Req</th>
                        <th class="text-center" width="110">Qty Rec</th>
                        <th width="80">Satuan</th>
                        <th class="text-center" width="160">Realisasi & Tgl Datang</th>
                        <th width="180">Info PO</th>
                        <th class="text-center" width="130">Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                    @php
                    $id = $item->id;
                    $qtyReq = $item->quantity > 0 ? $item->quantity : 1;
                    $qtyRec = isset($receiveItems[$id]['qty_received']) ? (int)$receiveItems[$id]['qty_received'] : (int)($item->qty_received ?? 0);
                    $percentage = min(round(($qtyRec / $qtyReq) * 100), 100);
                    $barColor = $percentage >= 100 ? 'bg-success' : ($percentage > 0 ? 'bg-info' : 'bg-danger');
                    @endphp
                    <tr wire:key="order-item-{{ $id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="fw-bold">{{ $item->item_name }}</span>
                            @if ($item->remark)<br><small class="text-muted">{{ $item->remark }}</small>@endif
                        </td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>

                        <td class="text-center">
                            @if ($canReceive)
                            <input type="number" wire:model.live="receiveItems.{{ $id }}.qty_received" class="form-control form-control-sm text-center border-success @error('receiveItems.'.$id.'.qty_received') is-invalid @enderror" min="0" max="{{ $item->quantity }}">
                            @error('receiveItems.'.$id.'.qty_received') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            @else
                            <span class="badge {{ $item->qty_received >= $item->quantity ? 'bg-success' : ($item->qty_received > 0 ? 'bg-info text-dark' : 'bg-secondary') }}">
                                {{ $item->qty_received ?? 0 }}
                            </span>
                            @endif
                        </td>
                        <td>{{ $item->unit }}</td>

                        <td>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <small class="fw-bold {{ $percentage >= 100 ? 'text-success' : 'text-muted' }}">{{ $percentage }}%</small>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>

                            @if ($canReceive)
                            <input type="date" wire:model="receiveItems.{{ $id }}.date_received" class="form-control form-control-sm @error('receiveItems.'.$id.'.date_received') is-invalid @enderror">
                            @else
                            <div class="small text-muted">{{ $item->date_received ? \Carbon\Carbon::parse($item->date_received)->format('d/m/Y') : '-' }}</div>
                            @endif
                        </td>

                        <td>
                            @if ($canReceive)
                            <input type="text" wire:model="receiveItems.{{ $id }}.po_number" class="form-control form-control-sm mb-1" placeholder="No. PO">
                            <input type="date" wire:model="receiveItems.{{ $id }}.po_date" class="form-control form-control-sm">
                            @else
                            @if ($item->po_number)
                            <div class="small fw-bold text-dark">{{ $item->po_number }}</div>
                            <div class="small text-muted">{{ $item->po_date ? \Carbon\Carbon::parse($item->po_date)->format('d/m/Y') : '-' }}</div>
                            @else
                            <span class="text-muted small italic">-</span>
                            @endif
                            @endif
                        </td>

                        <td class="text-center">
                            @if ($canReceive)
                            <input type="file" wire:model="receiveItems.{{ $id }}.receipt_attachment" class="form-control form-control-sm">
                            @if (isset($receiveItems[$id]['old_attachment']) && $receiveItems[$id]['old_attachment'])
                            <a href="{{ asset('storage/' . $receiveItems[$id]['old_attachment']) }}" target="_blank" class="btn btn-sm btn-outline-primary p-1 py-0 mt-1">
                                <i class="bi bi-file-earmark-image"></i> Lihat
                            </a>
                            @endif
                            @else
                            @if ($item->receipt_attachment)
                            <a href="{{ asset('storage/' . $item->receipt_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary p-1 py-0">
                                <i class="bi bi-file-earmark-image"></i>
                            </a>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Note & Attachment --}}
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="p-3 border rounded h-100 shadow-sm">
                    <label class="fw-bold text-muted small uppercase">Catatan / Keperluan:</label>
                    <p class="mb-0 italic">{{ $order->purpose ?? 'Tidak ada catatan.' }}</p>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0 text-center">
                <div class="p-3 border rounded h-100 shadow-sm">
                    <label class="fw-bold text-muted small d-block mb-2">Lampiran RO (Header):</label>
                    @if ($order->attachment)
                    <a href="{{ asset('storage/' . $order->attachment) }}" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="bi bi-paperclip me-2"></i>Lihat Lampiran
                    </a>
                    @else
                    <span class="text-muted small italic">Tidak ada lampiran</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Approval Tracking --}}
        <div class="mt-4 p-3 border-top bg-light rounded shadow-sm">
            <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-clock-history me-2"></i>Approval Tracking</h6>
            <div class="row text-center g-3 small">
                <div class="col-md-4 border-start">
                    <span class="text-muted d-block small uppercase fw-bold">Div Head Approval</span>
                    <span class="fw-bold {{ $order->divHead ? 'text-success' : 'text-muted' }}">{{ $order->divHead->name ?? 'Waiting...' }}</span>
                    @if ($order->approved_by_divhead_at)
                    <div class="text-muted mt-1" style="font-size: 0.75rem;"><i class="bi bi-calendar-check text-success me-1"></i>{{ \Carbon\Carbon::parse($order->approved_by_divhead_at)->format('d M Y, H:i') }}</div>
                    @endif
                </div>

                <div class="col-md-4 border-start">
                    <span class="text-muted d-block small uppercase fw-bold">Plant Manager Approval</span>
                    <span class="fw-bold {{ $order->plantManager ? 'text-success' : 'text-muted' }}">{{ $order->plantManager->name ?? 'Waiting...' }}</span>
                    @if ($order->approved_by_manager_at)
                    <div class="text-muted mt-1" style="font-size: 0.75rem;"><i class="bi bi-calendar-check text-success me-1"></i>{{ \Carbon\Carbon::parse($order->approved_by_manager_at)->format('d M Y, H:i') }}</div>
                    @endif
                </div>

                <div class="col-md-4 border-start border-end">
                    <span class="text-muted d-block small uppercase fw-bold">BOD Approval</span>
                    <span class="fw-bold {{ $order->bod ? 'text-success' : 'text-muted' }}">{{ $order->bod->name ?? 'Waiting...' }}</span>
                    @if ($order->approved_by_bod_at)
                    <div class="text-muted mt-1" style="font-size: 0.75rem;"><i class="bi bi-calendar-check text-success me-1"></i>{{ \Carbon\Carbon::parse($order->approved_by_bod_at)->format('d M Y, H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tombol Aksi Bawah --}}
        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('request-order.index') }}" class="btn btn-secondary px-4" wire:navigate>Kembali</a>

            <div class="d-flex gap-2 align-items-center flex-wrap">
                @php $user = auth()->user(); @endphp

                {{-- ========================================== --}}
                {{-- 1. LOGIKA TOMBOL UNAPPROVE (SESUAI TAHAP TERAKHIR) --}}
                {{-- ========================================== --}}

                {{-- A. Jika tahap terakhir disetujui oleh BOD --}}
                @if ($order->approved_by_bod_at && ($user->can('request-order.unapprove-bod') || $user->hasRole('super-admin')))
                <button type="button" class="btn btn-warning fw-bold shadow-sm" wire:click="unapproveBod" wire:confirm="Batal persetujuan BOD?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="unapproveBod"><i class="bi bi-arrow-counterclockwise me-1"></i> Unapprove BOD</span>
                    <span wire:loading wire:target="unapproveBod"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
                </button>

                {{-- B. Jika tahap terakhir disetujui oleh Plant Manager (dan belum disetujui BOD) --}}
                @elseif ($order->approved_by_manager_at && !$order->approved_by_bod_at && ($user->can('request-order.unapprove-manager') || $user->hasRole('super-admin')))
                <button type="button" class="btn btn-warning fw-bold shadow-sm" wire:click="unapproveManager" wire:confirm="Batal persetujuan Manager?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="unapproveManager"><i class="bi bi-arrow-counterclockwise me-1"></i> Unapprove Manager</span>
                    <span wire:loading wire:target="unapproveManager"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
                </button>

                {{-- C. Jika tahap terakhir disetujui oleh Kadiv (dan belum disetujui Manager & BOD) --}}
                @elseif ($order->approved_by_divhead_at && !$order->approved_by_manager_at && !$order->approved_by_bod_at && ($user->can('request-order.unapprove-division') || $user->hasRole('super-admin')))
                <button type="button" class="btn btn-warning fw-bold shadow-sm" wire:click="unapproveDivHead" wire:confirm="Batal persetujuan Kadiv?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="unapproveDivHead"><i class="bi bi-arrow-counterclockwise me-1"></i> Unapprove Kadiv</span>
                    <span wire:loading wire:target="unapproveDivHead"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
                </button>
                @endif

                {{-- ========================================== --}}
                {{-- 2. TOMBOL CETAK PDF --}}
                {{-- ========================================== --}}
                <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>

                {{-- ========================================== --}}
                {{-- 3. LOGIKA TOMBOL APPROVE --}}
                {{-- ========================================== --}}

                {{-- A. Menunggu Approval Kadiv --}}
                @if (!$order->approved_by_divhead_at && ($user->can('request-order.approve-division') || $user->hasRole('super-admin')))
                <button type="button" class="btn btn-success fw-bold shadow-sm" wire:click="approveDivHead" wire:confirm="Setujui Request Order ini?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="approveDivHead"><i class="bi bi-check2-circle me-1"></i> Approve Kadiv</span>
                    <span wire:loading wire:target="approveDivHead"><span class="spinner-border spinner-border-sm me-1"></span>Menyetujui...</span>
                </button>
                @endif

                {{-- B. Menunggu Approval Plant Manager --}}
                @if ($order->approved_by_divhead_at && !$order->approved_by_manager_at && ($user->can('request-order.approve-manager') || $user->hasRole('super-admin')))
                <button type="button" class="btn btn-success fw-bold shadow-sm" wire:click="approveManager" wire:confirm="Setujui Request Order ini?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="approveManager"><i class="bi bi-check2-circle me-1"></i> Approve Manager</span>
                    <span wire:loading wire:target="approveManager"><span class="spinner-border spinner-border-sm me-1"></span>Menyetujui...</span>
                </button>
                @endif

                {{-- C. Menunggu Approval BOD --}}
                @if ($order->approved_by_manager_at && !$order->approved_by_bod_at && ($user->can('request-order.approve-bod') || $user->hasRole('super-admin')))
                <button type="button" class="btn btn-success fw-bold shadow-sm" wire:click="approveBod" wire:confirm="Setujui Request Order ini?" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="approveBod"><i class="bi bi-check2-circle me-1"></i> Approve BOD</span>
                    <span wire:loading wire:target="approveBod"><span class="spinner-border spinner-border-sm me-1"></span>Menyetujui...</span>
                </button>
                @endif
            </div>
        </div>
        @endcomponent
    </div>
</div>