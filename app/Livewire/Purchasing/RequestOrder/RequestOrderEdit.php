<?php

namespace App\Livewire\Purchasing\RequestOrder;

use App\Models\Purchasing\RequestOrder;
use App\Models\Purchasing\RequestOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Request Order')]
class RequestOrderEdit extends Component
{
    use WithFileUploads;

    public RequestOrder $order;

    // Form Header Fields
    public $subsidiary_id;
    public $division;
    public $request_date;
    public $purpose;
    public $attachment;
    public $old_attachment;

    // Form Items Fields
    public array $items = [];

    protected function rules()
    {
        return [
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'division'      => 'required|string|max:100',
            'request_date'  => 'required|date',
            'purpose'       => 'nullable|string|max:1000',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'items'                 => 'required|array|min:1',
            'items.*.item_name'     => 'required|string|max:255',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit'          => 'required|string|max:50',
            'items.*.remark'        => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'items.min'                  => 'Minimal harus terdapat 1 barang pada Request Order.',
        'items.*.item_name.required' => 'Nama barang tidak boleh kosong.',
        'items.*.quantity.required'  => 'Qty tidak boleh kosong.',
        'items.*.quantity.min'       => 'Qty minimal harus 0.01.',
        'items.*.unit.required'      => 'Satuan wajib diisi.',
    ];

    public function mount(RequestOrder $requestOrder)
    {
        $this->order = $requestOrder->load(['subsidiary', 'items']);

        // Populate Header Data
        $this->subsidiary_id  = $this->order->subsidiary_id;
        $this->division       = $this->order->division;
        $this->request_date   = $this->order->request_date ? Carbon::parse($this->order->request_date)->format('Y-m-d') : '';
        $this->purpose        = $this->order->purpose;
        $this->old_attachment = $this->order->attachment;

        // Populate Items Data
        $this->items = [];
        if ($this->order->items && count($this->order->items) > 0) {
            foreach ($this->order->items as $item) {
                $this->items[] = [
                    'id'        => $item->id,
                    'item_name' => $item->item_name,
                    'quantity'  => (float) $item->quantity,
                    'unit'      => $item->unit,
                    'remark'    => $item->remark ?? '',
                ];
            }
        } else {
            $this->addItem();
        }
    }

    public function addItem()
    {
        if (!is_array($this->items)) {
            $this->items = [];
        }

        $this->items[] = [
            'id'        => null,
            'item_name' => '',
            'quantity'  => 1,
            'unit'      => '',
            'remark'    => '',
        ];
    }

    public function removeItem($index)
    {
        if (is_array($this->items) && count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // 1. Upload Lampiran Header Baru Jika Ada
            $attachmentPath = $this->old_attachment;
            if ($this->attachment) {
                // Hapus berkas lama dari storage jika ada
                if ($this->old_attachment) {
                    $oldFilePath = str_contains($this->old_attachment, '/')
                        ? $this->old_attachment
                        : 'request-orders/attachments/' . $this->old_attachment;

                    if (Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                }

                $attachmentPath = $this->attachment->store('request-orders/attachments', 'public');
            }

            // 2. Update Data Header Dokumen
            $this->order->update([
                'division'        => $this->division,
                'request_date'    => $this->request_date,
                'purpose'         => $this->purpose,
                'attachment'      => $attachmentPath,
                'revision_count'  => $this->order->revision_count + 1,
                'last_revised_at' => now(),
            ]);

            // 3. Tangani Item Yang Dihapus dari Form oleh User
            $existingIds = collect($this->items)->pluck('id')->filter()->toArray();

            $deletedItems = RequestOrderItem::where('request_order_id', $this->order->id)
                ->whereNotIn('id', $existingIds)
                ->get();

            foreach ($deletedItems as $deletedItem) {
                // Hapus file lampiran penerimaan item jika ada
                if (!empty($deletedItem->receipt_attachment)) {
                    $receiptPath = str_contains($deletedItem->receipt_attachment, '/')
                        ? $deletedItem->receipt_attachment
                        : 'request-orders/receipts/' . $deletedItem->receipt_attachment;

                    if (Storage::disk('public')->exists($receiptPath)) {
                        Storage::disk('public')->delete($receiptPath);
                    }
                }
                $deletedItem->delete();
            }

            // 4. Update atau Tambah Item Baru
            foreach ($this->items as $itemData) {
                $payload = [
                    'item_name' => $itemData['item_name'],
                    'quantity'  => (float) $itemData['quantity'],
                    'unit'      => $itemData['unit'],
                    'remark'    => $itemData['remark'] ?? null,
                ];

                if (!empty($itemData['id'])) {
                    RequestOrderItem::where('id', $itemData['id'])->update($payload);
                } else {
                    $this->order->items()->create($payload);
                }
            }

            DB::commit();

            session()->flash('success', 'Request Order berhasil direvisi!');
            return redirect()->route('request-order.show', $this->order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal merevisi dokumen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('purchasing.request-order.request-order-edit');
    }
}
