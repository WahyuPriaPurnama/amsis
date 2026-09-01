<?php

namespace App\Livewire\Purchasing;

use App\Models\Purchasing\RequestOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads; // 1. Tambahkan Trait ini untuk penanganan upload file

class RequestOrderShow extends Component
{
    use WithFileUploads; // 2. Gunakan Trait

    public RequestOrder $order;
    public array $receiveItems = [];

    public function mount(RequestOrder $requestOrder)
    {
        $this->loadOrderData($requestOrder);
    }

    public function loadOrderData(RequestOrder $requestOrder)
    {
        $this->order = $requestOrder->load(['items', 'subsidiary', 'requester', 'divHead', 'plantManager', 'bod']);

        foreach ($this->order->items as $item) {
            $this->receiveItems[$item->id] = [
                'qty_received' => $item->qty_received ?? 0,
                'date_received' => $item->date_received,
                'po_number' => $item->po_number,
                'po_date' => $item->po_date,
                'receipt_attachment' => null, // Untuk upload berkas baru
                'old_attachment' => $item->receipt_attachment,
            ];
        }
    }

    public function updateReceive()
    {
        // Check hak akses
        $user = auth()->user();
        if (!($user->can('request-order.receive') || $user->hasRole('super-admin'))) {
            abort(403, 'Anda tidak memiliki hak akses untuk menerima barang.');
        }

        // Rules Validasi Input
        $rules = [];
        foreach ($this->order->items as $item) {
            $rules["receiveItems.{$item->id}.qty_received"] = 'required|numeric|min:0|max:' . $item->quantity;
            $rules["receiveItems.{$item->id}.date_received"] = 'nullable|date';
            $rules["receiveItems.{$item->id}.po_number"] = 'nullable|string|max:100';
            $rules["receiveItems.{$item->id}.po_date"] = 'nullable|date';
            $rules["receiveItems.{$item->id}.receipt_attachment"] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $this->validate($rules);

        DB::beginTransaction();
        try {
            $totalReq = 0;
            $totalRec = 0;

            foreach ($this->order->items as $item) {
                $itemId = $item->id;
                $data = $this->receiveItems[$itemId];

                // Upload berkas lampiran jika ada yang baru
                $attachmentPath = $data['old_attachment'];
                if (isset($data['receipt_attachment']) && $data['receipt_attachment']) {
                    if ($data['old_attachment'] && Storage::disk('public')->exists($data['old_attachment'])) {
                        Storage::disk('public')->delete($data['old_attachment']);
                    }
                    $attachmentPath = $data['receipt_attachment']->store('request-orders/receipts', 'public');
                }

                $qtyReceived = (int) $data['qty_received'];

                // Update Item
                $item->update([
                    'qty_received' => $qtyReceived,
                    'date_received' => $data['date_received'] ?? null,
                    'po_number' => $data['po_number'] ?? null,
                    'po_date' => $data['po_date'] ?? null,
                    'receipt_attachment' => $attachmentPath,
                ]);

                $totalReq += $item->quantity;
                $totalRec += $qtyReceived;
            }

            // --- PERBAIKAN LOGIKA STATUS ---
            // Status dokumen HANYA diubah menjadi completed/partial jika dokumen SUDAH melewati approval BOD
            if ($this->order->status === 'approved_by_bod' || $this->order->status === 'partial' || $this->order->status === 'completed') {
                if ($totalRec >= $totalReq) {
                    $this->order->update(['status' => 'completed']);
                } elseif ($totalRec > 0) {
                    $this->order->update(['status' => 'partial']);
                }
            }

            DB::commit();

            $this->refreshData('Penerimaan barang berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan penerimaan: ' . $e->getMessage());
        }
    }
    // --- APPROVAL ACTIONS ---

    public function approveDivHead()
    {
        $this->authorizeAccess('request-order.approve-division');

        $this->order->update([
            'status' => 'approved_by_div_head',
            'approved_by_div_head' => auth()->id(),
            'approved_by_divhead_at' => now(),
        ]);

        $this->refreshData('Persetujuan Kadiv berhasil disimpan.');
    }

    public function approveManager()
    {
        $this->authorizeAccess('request-order.approve-manager');

        $this->order->update([
            'status' => 'approved_by_manager',
            'approved_by_manager' => auth()->id(),
            'approved_by_manager_at' => now(),
        ]);

        $this->refreshData('Persetujuan Plant Manager berhasil disimpan.');
    }

    public function approveBod()
    {
        $this->authorizeAccess('request-order.approve-bod');

        $this->order->update([
            'status' => 'approved_by_bod',
            'approved_by_bod' => auth()->id(),
            'approved_by_bod_at' => now(),
        ]);

        $this->refreshData('Persetujuan BOD berhasil disimpan.');
    }

    // --- UNAPPROVE ACTIONS ---

    public function unapproveBod()
    {
        $this->authorizeAccess('request-order.unapprove-bod');

        $this->order->update([
            'status' => 'approved_by_manager',
            'approved_by_bod' => null,
            'approved_by_bod_at' => null,
        ]);

        $this->refreshData('Persetujuan BOD berhasil dibatalkan. Status kembali ke Approved Manager.');
    }

    public function unapproveManager()
    {
        $this->authorizeAccess('request-order.unapprove-manager');

        $this->order->update([
            'status' => 'approved_by_div_head',
            'approved_by_manager' => null,
            'approved_by_manager_at' => null,
        ]);

        $this->refreshData('Persetujuan Plant Manager berhasil dibatalkan. Status kembali ke Approved Kadiv.');
    }

    public function unapproveDivHead()
    {
        $this->authorizeAccess('request-order.unapprove-division');

        $this->order->update([
            'status' => 'pending',
            'approved_by_div_head' => null,
            'approved_by_divhead_at' => null,
        ]);

        $this->refreshData('Persetujuan Kadiv berhasil dibatalkan. Status kembali ke Pending.');
    }

    // --- HELPER METHODS ---

    private function authorizeAccess(string $permission)
    {
        $user = auth()->user();
        if (!($user->can($permission) || $user->hasRole('super-admin'))) {
            abort(403, 'Anda tidak memiliki hak akses untuk aksi ini.');
        }
    }

    private function refreshData(string $message)
    {
        $this->loadOrderData($this->order);
        session()->flash('success', $message);
    }
    // Tambahkan metode ini di dalam kelas RequestOrderShow

    public function deleteOrder()
    {
        // 1. Cek Hak Akses Penghapusan
        $user = auth()->user();
        if (!($user->can('request-order.delete') || $user->hasRole('super-admin'))) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus Request Order ini.');
        }

        DB::beginTransaction();
        try {
            // 2. Hapus Berkas Lampiran Header (Jika Ada)
            if ($this->order->attachment && Storage::disk('public')->exists($this->order->attachment)) {
                Storage::disk('public')->delete($this->order->attachment);
            }

            // 3. Hapus Berkas Lampiran Penerimaan pada Item (Jika Ada)
            foreach ($this->order->items as $item) {
                if ($item->receipt_attachment && Storage::disk('public')->exists($item->receipt_attachment)) {
                    Storage::disk('public')->delete($item->receipt_attachment);
                }
            }

            // 4. Hapus Item & Header Dokumen (Cascading / Manual)
            $this->order->items()->delete();
            $this->order->delete();

            DB::commit();

            session()->flash('success', 'Request Order berhasil dihapus!');
            return redirect()->route('request-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('purchasing.request-order.show');
    }
}
