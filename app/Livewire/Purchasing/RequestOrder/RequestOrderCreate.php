<?php

namespace App\Livewire\Purchasing\RequestOrder;

use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestOrder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Buat Request Order')]
class RequestOrderCreate extends Component
{
    use WithFileUploads;

    // Header Form Fields
    public $subsidiary_id = '';
    public $division = '';
    public $request_date;
    public $request_number = '';
    public $purpose = '';
    public $attachment;

    // Dynamic Items Form
    public $items = [];

    public function mount()
    {
        $this->request_date = date('Y-m-d');

        // Inisialisasi baris pertama secara default
        $this->items = [
            [
                'item_name' => '',
                'quantity'  => 1,
                'unit'      => '',
                'remark'    => '',
            ]
        ];

        // Jika ada data plant awal, pilih plant pertama secara otomatis
        $firstSub = Subsidiary::orderBy('name', 'asc')->first();
        if ($firstSub) {
            $this->subsidiary_id = $firstSub->id;
            $this->generateRequestNumber();
        }
    }

    // Dipanggil otomatis oleh Livewire 3 saat $subsidiary_id berubah
    public function updatedSubsidiaryId()
    {
        $this->generateRequestNumber();
    }

    // Dipanggil otomatis oleh Livewire 3 saat $request_date berubah
    public function updatedRequestDate()
    {
        $this->generateRequestNumber();
    }

    public function generateRequestNumber()
    {
        // Validasi: Harus memilih Plant dan Tanggal
        if (!$this->request_date || !$this->subsidiary_id) {
            $this->request_number = '';
            return;
        }

        // Format Prefix: YYYYMM/ (Contoh: 202608/)
        $yearMonth = Carbon::parse($this->request_date)->format('Ym');
        $prefix = "{$yearMonth}/";

        // Cari nomor urut terakhir KHUSUS untuk Subsidiary (Plant) dan Bulan/Tahun yang dipilih
        $lastOrder = RequestOrder::where('subsidiary_id', $this->subsidiary_id)
            ->where('request_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(request_number, "/", -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastOrder) {
            // Ambil angka paling belakang dan buat 3 digit (contoh: dari 202608/001 -> didapat angka 1 + 1 = 002)
            $parts = explode('/', $lastOrder->request_number);
            $lastNum = (int) end($parts);
            $nextNum = sprintf('%03d', $lastNum + 1);
        } else {
            // Reset ke 001 jika plant/bulan/tahun tersebut belum memiliki transaksi
            $nextNum = '001';
        }

        // Gabungkan prefix dan nomor urut (Hasil: 202608/001)
        $this->request_number = $prefix . $nextNum;
    }

    public function addItem()
    {
        $this->items[] = [
            'item_name' => '',
            'quantity'  => 1,
            'unit'      => '',
            'remark'    => '',
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    protected function rules()
    {
        return [
            'subsidiary_id'        => 'required|exists:subsidiaries,id',
            'division'             => 'required|string|max:255',
            'request_date'         => 'required|date',
            'request_number'       => 'required|string|max:100|unique:request_orders,request_number',
            'purpose'              => 'nullable|string',
            'attachment'           => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',

            'items'                => 'required|array|min:1',
            'items.*.item_name'    => 'required|string|max:255',
            'items.*.quantity'     => 'required|numeric|min:1',
            'items.*.unit'         => 'required|string|max:50',
            'items.*.remark'       => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'subsidiary_id.required'     => 'Pilih Plant!',
        'division.required'          => 'Divisi wajib diisi!',
        'request_date.required'      => 'Tanggal wajib diisi!',
        'request_number.required'    => 'Nomor RO wajib diisi!',
        'items.*.item_name.required' => 'Wajib!',
        'items.*.quantity.required'  => 'Min 1!',
        'items.*.unit.required'      => 'Wajib!',
    ];

    public function save()
    {
        $validatedData = $this->validate();

        // Simpan file lampiran jika ada
        if ($this->attachment) {
            $path = $this->attachment->store('public/request-orders');
            $validatedData['attachment'] = basename($path);
        }

        // Simpan Data Header
        $order = RequestOrder::create([
            'subsidiary_id'  => $validatedData['subsidiary_id'],
            'requested_by'   => auth()->id(),
            'division'       => $validatedData['division'],
            'request_date'   => $validatedData['request_date'],
            'request_number' => $validatedData['request_number'],
            'purpose'        => $validatedData['purpose'] ?? null,
            'attachment'     => $validatedData['attachment'] ?? null,
            'status'         => 'pending',
        ]);

        // Simpan Data Items Detail
        foreach ($this->items as $item) {
            $order->items()->create([
                'item_name' => $item['item_name'],
                'quantity'  => $item['quantity'],
                'unit'      => $item['unit'],
                'remark'    => $item['remark'] ?? null,
            ]);
        }

        session()->flash('success', 'Request Order berhasil dibuat.');

        return $this->redirectRoute('request-order.index', navigate: true);
    }

    public function render()
    {
        return view('purchasing.request-order.request-order-create', [
            'subsidiaries' => Subsidiary::orderBy('name', 'asc')->get(),
        ]);
    }
}
