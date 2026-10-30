<?php

namespace App\Livewire\Purchasing\RequestPayment;

use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Request Payment')]
class RequestPaymentEdit extends Component
{
    use WithFileUploads;

    public RequestPayment $payment;

    // Form Fields
    public $subsidiary_id;
    public $division;
    public $date;
    public $payment_number;
    public $purpose;
    public $attachment;
    public $old_attachment;

    // Dynamic Items Array
    public array $items = [];

    protected function rules()
    {
        return [
            'subsidiary_id' => [
                'required',
                'exists:subsidiaries,id',
                function ($attribute, $value, $fail) {
                    $user = Auth::user();

                    // 1. Jika Super Admin / Punya permission global edit, izinkan semua plant
                    if ($user->hasRole('super-admin') || $user->can('request-payment.edit')) {
                        return;
                    }

                    // 2. Jika nilainya adalah Plant asli dari dokumen ini, izinkan
                    if ($value == $this->payment->subsidiary_id) {
                        return;
                    }
                },
            ],
            'division' => 'required|string|max:255',
            'date' => 'required|date',
            'payment_number' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('request_payments', 'payment_number')
                    ->where('subsidiary_id', $this->subsidiary_id)
                    ->ignore($this->payment->id),
            ],
            'purpose' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',

            // Validasi Items
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.due_date' => 'nullable|date',
            'items.*.attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // File lampiran baru per item
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'subsidiary_id' => 'Plant',
            'division' => 'Divisi',
            'date' => 'Tanggal',
            'payment_number' => 'Nomor Payment',
            'items.*.item_name' => 'Nama Item',
            'items.*.quantity' => 'Qty',
            'items.*.unit' => 'Satuan',
            'items.*.unit_price' => 'Harga Satuan',
            'items.*.attachment' => 'Lampiran Item',
        ];
    }

    private function generatePaymentNumber()
    {
        if (empty($this->subsidiary_id) || empty($this->date)) {
            return;
        }

        // Jika dikembalikan ke subsidiary dan tanggal awal dokumen
        if ($this->subsidiary_id == $this->payment->subsidiary_id && $this->date == Carbon::parse($this->payment->date)->format('Y-m-d')) {
            $this->payment_number = $this->payment->payment_number;
            return;
        }

        $dateObj = Carbon::parse($this->date);
        $yearMonth = $dateObj->format('Ym');

        // Melanjutkan nomor terakhir pada plant tersebut
        $lastPayment = RequestPayment::where('subsidiary_id', $this->subsidiary_id)
            ->whereYear('date', $dateObj->year)
            ->whereMonth('date', $dateObj->month)
            ->where('id', '!=', $this->payment->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment && $lastPayment->payment_number) {
            $parts = explode('/', $lastPayment->payment_number);
            $lastNumber = isset($parts[1]) ? (int) $parts[1] : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $this->payment_number = "{$yearMonth}/" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function mount(RequestPayment $requestPayment)
    {
        $this->payment = $requestPayment->load('items');

        $this->subsidiary_id = $requestPayment->subsidiary_id;
        $this->division = $requestPayment->division;
        $this->date = Carbon::parse($requestPayment->date)->format('Y-m-d');
        $this->payment_number = $requestPayment->payment_number;
        $this->purpose = $requestPayment->purpose;
        $this->old_attachment = $requestPayment->attachment;

        // Populate items beserta lampiran yang ada
        $this->items = [];

        if ($requestPayment->items && count($requestPayment->items) > 0) {
            foreach ($requestPayment->items as $item) {
                $this->items[] = [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'due_date' => $item->due_date ? Carbon::parse($item->due_date)->format('Y-m-d') : '',
                    'attachment' => null, // Tempat temporary upload file baru
                    'old_attachment' => $item->attachment, // File lama per item
                ];
            }
        } else {
            $this->addItem();
        }
    }

    public function updatedSubsidiaryId()
    {
        $this->generatePaymentNumber();
    }

    public function updatedDate()
    {
        $this->generatePaymentNumber();
    }

    public function addItem()
    {
        if (!is_array($this->items)) {
            $this->items = [];
        }

        $this->items[] = [
            'id' => '',
            'item_name' => '',
            'quantity' => 1,
            'unit' => '',
            'unit_price' => 0,
            'due_date' => '',
            'attachment' => null,
            'old_attachment' => null,
        ];
    }

    public function removeItem($index)
    {
        if (is_array($this->items) && count($this->items) > 1) {
            // Hapus file fisik lampiran item jika item dihapus dari form
            $itemToDelete = $this->items[$index] ?? null;
            if ($itemToDelete && !empty($itemToDelete['id'])) {
                $itemModel = $this->payment->items()->find($itemToDelete['id']);
                if ($itemModel && $itemModel->attachment && Storage::disk('public')->exists($itemModel->attachment)) {
                    Storage::disk('public')->delete($itemModel->attachment);
                }
            }

            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function getGrandTotalProperty()
    {
        if (!is_array($this->items)) {
            return 0;
        }

        return collect($this->items)->sum(function ($item) {
            return (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
        });
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // Processing Attachment Header
            $attachmentPath = $this->old_attachment;

            if ($this->attachment) {
                if ($this->old_attachment && Storage::disk('public')->exists($this->old_attachment)) {
                    Storage::disk('public')->delete($this->old_attachment);
                }
                $attachmentPath = $this->attachment->store('attachments/request-payments', 'public');
            }

            $this->payment->update([
                'subsidiary_id' => $this->subsidiary_id,
                'division' => $this->division,
                'date' => $this->date,
                'payment_number' => $this->payment_number,
                'purpose' => $this->purpose,
                'attachment' => $attachmentPath,
                'grand_total' => $this->grandTotal,
            ]);

            // Hapus item fisik & DB yang dihilangkan oleh user
            $existingItemIds = collect($this->items)->pluck('id')->filter()->toArray();
            $deletedItems = $this->payment->items()->whereNotIn('id', $existingItemIds)->get();

            foreach ($deletedItems as $deletedItem) {
                if ($deletedItem->attachment && Storage::disk('public')->exists($deletedItem->attachment)) {
                    Storage::disk('public')->delete($deletedItem->attachment);
                }
                $deletedItem->delete();
            }

            // Sync / Save Items
            foreach ($this->items as $itemData) {
                $amount = (float)$itemData['quantity'] * (float)$itemData['unit_price'];

                if (!empty($itemData['id'])) {
                    $itemModel = $this->payment->items()->find($itemData['id']);
                    $itemAttachmentPath = $itemData['old_attachment'] ?? null;

                    // Jika ada upload file baru untuk item ini
                    if (isset($itemData['attachment']) && $itemData['attachment']) {
                        if ($itemAttachmentPath && Storage::disk('public')->exists($itemAttachmentPath)) {
                            Storage::disk('public')->delete($itemAttachmentPath);
                        }
                        $itemAttachmentPath = $itemData['attachment']->store('attachments/request-payments/items', 'public');
                    }

                    $itemModel->update([
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'],
                        'unit_price' => $itemData['unit_price'],
                        'amount' => $amount,
                        'due_date' => $itemData['due_date'] ?: null,
                        'attachment' => $itemAttachmentPath,
                    ]);
                } else {
                    $itemAttachmentPath = null;
                    if (isset($itemData['attachment']) && $itemData['attachment']) {
                        $itemAttachmentPath = $itemData['attachment']->store('attachments/request-payments/items', 'public');
                    }

                    $this->payment->items()->create([
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'],
                        'unit_price' => $itemData['unit_price'],
                        'amount' => $amount,
                        'due_date' => $itemData['due_date'] ?: null,
                        'attachment' => $itemAttachmentPath,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Request Payment berhasil diperbarui!');
            return $this->redirectRoute('request-payment.index', navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->hasRole('super-admin') || $user->can('request-payment.edit')) {
            $subsidiaries = Subsidiary::all();
        } else {
            $subsidiaries = Subsidiary::all();
        }

        return view('purchasing.request-payment.request-payment-edit', [
            'subsidiaries' => $subsidiaries,
        ]);
    }
}
