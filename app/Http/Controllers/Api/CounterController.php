<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MachineStoppedNotification;
use Carbon\Carbon;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $seamerId = $request->query('seamer_id', 'Seamer1'); // Default ke Seamer1

        $data = Counter::where('seamer_name', $seamerId)
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        $lastTimestamp = Counter::where('seamer_name', $seamerId)->latest()->value('created_at');

        return response()->json([
            'labels' => $data->pluck('created_at')->map(fn($t) => $t->format('H:i:s')),
            'rpm' => $data->pluck('rpm'),
            'counter' => $data->pluck('counter'),
            'last_timestamp' => $lastTimestamp ? $lastTimestamp->timestamp : null,
        ]);
    }


    public function store(Request $request)
    {
        try {
            // Sesuai dengan payload ESP32: {"device_id": "...", "seamers": [...]}
            $validated = $request->validate([
                'device_id' => 'required|string',
                'seamers' => 'required|array',
                'seamers.*.id' => 'required|string',
                'seamers.*.rpm' => 'required|numeric',
                'seamers.*.counter' => 'required|numeric',
            ]);

            foreach ($validated['seamers'] as $item) {
                // Simpan data untuk masing-masing seamer
                $counterEntry = Counter::create([
                    'device_id' => $validated['device_id'],
                    'seamer_name' => $item['id'], // Menyimpan "Seamer1", "Seamer2", dst.
                    'rpm' => $item['rpm'],
                    'counter' => $item['counter'],
                ]);

                // Logika Notifikasi jika mesin berhenti (RPM = 0)
                if ($item['rpm'] == 0) {
                    $this->handleNotification($validated['device_id'], $item);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Data 3 Seamer tersimpan']);
        } catch (\Exception $e) {
            Log::error('Gagal simpan data ESP32: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleNotification($deviceId, $item)
    {
        // 1. Cek data sebelum yang baru saja disimpan (untuk melihat status sebelumnya)
        $previousStatus = Counter::where('device_id', $deviceId)
            ->where('seamer_name', $item['id'])
            ->latest()
            ->skip(1) // Data yang barusan disimpan adalah skip(0)
            ->first();

        // 2. Hanya proses jika sebelumnya mesin sedang jalan (RPM > 0) dan sekarang berhenti (RPM = 0)
        // Ini mencegah "spam" email jika mesin mati seharian.
        if ($previousStatus && $previousStatus->rpm > 0) {
            // Cek jeda waktu agar tidak double send dalam waktu singkat
            $lastEmailSent = Log::where('message', "Email dikirim: Mesin {$item['id']} Berhenti") // Misal cek lewat log atau tabel khusus
                ->latest()->first();

            Mail::to('it@amsgroup.co.id')->send(new MachineStoppedNotification($item));
            Log::warning("Email dikirim: Mesin {$item['id']} Berhenti");
        }
    }
    public function indexhourly(Request $request, $range = 'day')
    {
        $targetDate = $request->query('date') ? Carbon::parse($request->query('date')) : now();
        $seamerId = $request->query('seamer_id', 'Seamer1'); // Filter per mesin

        if ($range === 'day') {
            $start = $targetDate->copy()->startOfDay();
            $end = $targetDate->copy()->endOfDay();
        } else {
            $start = $targetDate->copy()->subDays(7)->startOfDay();
            $end = $targetDate->copy()->endOfDay();
        }

        // Ambil data pembanding (prev) khusus seamer yang dipilih
        $initialData = Counter::where('seamer_name', $seamerId)
            ->where('created_at', '<', $start)
            ->orderBy('created_at', 'desc')
            ->first();

        $prev = $initialData ? (int) $initialData->counter : null;

        $raw = Counter::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour,
                MAX(counter) as max_counter,
                AVG(rpm) as avg_rpm
            ')
            ->where('seamer_name', $seamerId)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        $labels = collect();
        $rpm = collect();
        $counter = collect();

        foreach ($raw as $row) {
            $current = (int) $row->max_counter;

            if ($prev === null) {
                $counter->push(0);
            } else {
                // Hitung selisih. Jika counter reset (current < prev), gunakan current saja.
                $counter->push($current < $prev ? $current : $current - $prev);
            }

            $labels->push(\Carbon\Carbon::parse($row->hour)->format('d-m H:i'));
            $rpm->push(round($row->avg_rpm, 2));
            $prev = $current;
        }

        return response()->json([
            'labels' => $labels,
            'rpm' => $rpm,
            'counter' => $counter,
        ]);
    }
}
