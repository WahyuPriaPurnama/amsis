<?php

namespace App\Livewire\Purchasing\RequestPayment;

use App\Models\Purchasing\RequestPayment;
use App\Models\HRD\Subsidiary;
use Carbon\Carbon;
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
    public $attachment; // Untuk file upload baru
    public $old_attachment; // Menyimpan path lampiran lama

    // Items Details
    public $items = [];

    protected function rules()
    {
        return [
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'division' => 'required|string|max:255',
            'date' => 'required|date',
            // Validasi unique dicek berdasarkan gabungan subsidiary_id dan payment_number, kecuali untuk ID ini sendiri
            'payment_number' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('request_payments', 'payment_number')
                    ->where('subsidiary_id', $this->subsidiary_id)
                    ->ignore($this->payment->id),
            ],
            'purpose' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.due_date' => 'nullable|date',
        ];
    }

    private function generatePaymentNumber()
    {
        if (!$this->subsidiary_id || !$this->date) {
            return;
        }

        // Jika subsidiary dan tanggal dikembalikan seperti semula ke data awal dokumen ini
        if ($this->subsidiary_id == $this->payment->subsidiary_id && $this->date == Carbon::parse($this->payment->date)->format('Y-m-d')) {
            $this->payment_number = $this->payment->payment_number;
            return;
        }

        $dateObj = Carbon::parse($this->date);
        $yearMonth = $dateObj->format('Ym');

        // Hitung jumlah record berdasarkan subsidiary yang dipilih pada bulan & tahun tersebut
        $count = RequestPayment::where('subsidiary_id', $this->subsidiary_id)
            ->where('id', '!=', $this->payment->id)
            ->whereYear('date', $dateObj->year)
            ->whereMonth('date', $dateObj->month)
            ->count() + 1;

        $this->payment_number = "{$yearMonth}/" . str_pad($count, 3, '0', STR_PAD_LEFT);
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

        // Map items existing
        foreach ($requestPayment->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'due_date' => $item->due_date ? Carbon::parse($item->due_date)->format('Y-m-d') : '',
            ];
        }
    }

    // Hook untuk mengubah nomor payment otomatis jika plant atau tanggal berubah
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
        $this->items[] = [
            'id' => '',
            'item_name' => '',
            'quantity' => 1,
            'unit' => '',
            'unit_price' => 0,
            'due_date' => '',
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function getGrandTotalProperty()
    {
        return collect($this->items)->sum(function ($item) {
            return (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
        });
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $attachmentPath = $this->old_attachment;

            if ($this->attachment) {
                // Hapus file lama jika ada
                if ($this->old_attachment && Storage::disk('public')->exists($this->old_attachment)) {
                    Storage::disk('public')->delete($this->old_attachment);
                }
                $attachmentPath = $this->attachment->store('request-payments', 'public');
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

            // Sinkronisasi items (update yang lama, tambah yang baru, hapus yang dibuang)
            $existingItemIds = collect($this->items)->pluck('id')->filter()->toArray();

            // Hapus item yang dihapus dari UI
            $this->payment->items()->whereNotIn('id', $existingItemIds)->delete();

            foreach ($this->items as $itemData) {
                $amount = (float)$itemData['quantity'] * (float)$itemData['unit_price'];

                if (!empty($itemData['id'])) {
                    // Update existing
                    $this->payment->items()->where('id', $itemData['id'])->update([
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'],
                        'unit_price' => $itemData['unit_price'],
                        'amount' => $amount,
                        'due_date' => $itemData['due_date'] ?: null,
                    ]);
                } else {
                    // Create new item
                    $this->payment->items()->create([
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'],
                        'unit_price' => $itemData['unit_price'],
                        'amount' => $amount,
                        'due_date' => $itemData['due_date'] ?: null,
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
        return view('purchasing.request-payment.request-payment-edit', [
            'subsidiaries' => Subsidiary::all(),
        ]);
    }
}
