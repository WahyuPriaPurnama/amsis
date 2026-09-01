<?php

namespace App\Livewire\Purchasing\RequestPayment;

use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestPayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; // <-- Import Rule
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Buat Request Payment')]
class RequestPaymentCreate extends Component
{
    use WithFileUploads;

    // Form Header Properties
    public $subsidiary_id = '';
    public string $division = '';
    public string $date = '';
    public string $payment_number = '';

    // Form Additional Properties
    public ?string $purpose = null;
    public $attachment;

    // Dynamic Items Array
    public array $items = [];

    protected function rules(): array
    {
        return [
            'subsidiary_id' => 'required|exists:subsidiaries,id',
            'division'      => 'required|string|max:255',
            'date'          => 'required|date',
            // VALIDASI UNIK DIKHUSUSKAN PER SUBSIDIARY_ID
            'payment_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('request_payments', 'payment_number')->where(function ($query) {
                    return $query->where('subsidiary_id', $this->subsidiary_id);
                }),
            ],
            'purpose'       => 'nullable|string',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',

            'items'              => 'required|array|min:1',
            'items.*.item_name'  => 'required|string|max:255',
            'items.*.quantity'   => 'required|numeric|min:1',
            'items.*.unit'       => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:1',
            'items.*.due_date'   => 'nullable|date',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'subsidiary_id'      => 'Plant',
            'division'           => 'Divisi',
            'date'               => 'Tanggal',
            'payment_number'     => 'Nomor Payment',
            'items.*.item_name'  => 'Nama Item',
            'items.*.quantity'   => 'Qty',
            'items.*.unit'       => 'Satuan',
            'items.*.unit_price' => 'Harga Satuan',
        ];
    }

    public function mount(): void
    {
        $this->date = date('Y-m-d');
        $this->addItem();

        $firstSub = Subsidiary::orderBy('name', 'asc')->first();
        if ($firstSub) {
            $this->subsidiary_id = $firstSub->id;
            $this->generatePaymentNumber();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'item_name'  => '',
            'quantity'   => 1,
            'unit'       => '',
            'unit_price' => 0,
            'due_date'   => '',
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updatedSubsidiaryId(): void
    {
        $this->generatePaymentNumber();
    }

    public function updatedDate(): void
    {
        $this->generatePaymentNumber();
    }

    public function generatePaymentNumber(): void
    {
        if (!$this->subsidiary_id || !$this->date) {
            $this->payment_number = '';
            return;
        }

        $yearMonth = Carbon::parse($this->date)->format('Ym');
        $prefix = "{$yearMonth}/";

        $lastPayment = RequestPayment::where('subsidiary_id', (int) $this->subsidiary_id)
            ->where('payment_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(payment_number, "/", -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastPayment) {
            $parts = explode('/', $lastPayment->payment_number);
            $lastNum = (int) end($parts);
            $nextNum = sprintf('%03d', $lastNum + 1);
        } else {
            $nextNum = '001';
        }

        $this->payment_number = $prefix . $nextNum;
    }

    #[Computed]
    public function grandTotal(): float
    {
        return array_reduce($this->items, function ($total, $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            return $total + ($qty * $price);
        }, 0.0);
    }

    public function save()
    {
        $this->generatePaymentNumber();

        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $attachmentPath = null;
            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('attachments/request-payments', 'public');
            }

            $payment = RequestPayment::create([
                'subsidiary_id'  => $this->subsidiary_id,
                'division'       => $this->division,
                'date'           => $this->date,
                'payment_number' => $this->payment_number,
                'purpose'        => $this->purpose,
                'attachment'     => $attachmentPath,
                'grand_total'    => $this->grandTotal,
                'status'         => 'pending',
                'requested_by'   => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];

                $payment->items()->create([
                    'item_name'  => $item['item_name'],
                    'quantity'   => $qty,
                    'unit'       => $item['unit'],
                    'unit_price' => $price,
                    'amount'     => $qty * $price,
                    'due_date'   => !empty($item['due_date']) ? $item['due_date'] : null,
                ]);
            }
        });

        session()->flash('success', 'Request Payment berhasil dibuat!');

        return $this->redirect(route('request-payment.index'), navigate: true);
    }

    public function render()
    {
        return view('purchasing.request-payment.request-payment-create', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}