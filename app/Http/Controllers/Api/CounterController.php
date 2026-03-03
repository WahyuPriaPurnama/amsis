<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MachineStoppedNotification;

class CounterController extends Controller
{
    public function index()
    {
        $data = Counter::latest()->take(20)->get()->reverse()->values();
        $lastTimestamp = Counter::latest()->value('created_at');
        return response()->json([
            'labels' => $data->pluck('created_at')->map(fn($t) => $t->format('H:i:s')),
            'rpm' => $data->pluck('rpm'),
            'counter' => $data->pluck('counter'),
            'last_timestamp' => $lastTimestamp ? $lastTimestamp->timestamp : null,
        ]);
    }

    public function indexhourly(Request $request, $range = 'day')
    {
        // 1. Tentukan Tanggal Target
        // Jika ada input 'date' dari query string, gunakan itu. Jika tidak, gunakan hari ini.
        $targetDate = $request->query('date') ? \Carbon\Carbon::parse($request->query('date')) : now();

        // 2. Tentukan Rentang Waktu Query
        if ($range === 'day') {
            // Ambil data dari awal hari yang dipilih (jam 00:00)
            // Jika ingin mulai jam 07:00 sesuai kode lama, gunakan ->startOfDay()->addHours(7)
            $start = $targetDate->copy()->startOfDay();
            $end = $targetDate->copy()->endOfDay();
        } elseif ($range === 'week') {
            $start = $targetDate->copy()->subDays(7)->startOfDay();
            $end = $targetDate->copy()->endOfDay();
        } else {
            $start = $targetDate->copy()->subMonth()->startOfDay();
            $end = $targetDate->copy()->endOfDay();
        }

        // 3. Ambil satu data tepat sebelum $start untuk menjadi nilai awal ($prev)
        // Ini penting agar grafik jam pertama tidak langsung meloncat/0
        $initialData = Counter::where('created_at', '<', $start)
            ->orderBy('created_at', 'desc')
            ->first();
        $prev = $initialData ? (int) $initialData->counter : null;

        // 4. Query Data Utama
        $raw = Counter::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour,
            MAX(counter) as max_counter,
            AVG(rpm) as avg_rpm
        ')
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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'rpm' => 'required|numeric',
                'counter' => 'required|numeric',
                'device_id' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            $isStopped = ($validated['rpm'] == 0 || $validated['counter'] == 0);

            $counter = Counter::create($validated);
            Log::info('Data RPM disimpan', ['payload' => $validated]);

            if ($isStopped) {
                $lastNotified = Counter::where('device_id', $validated['device_id'] ?? null)
                    ->where(function ($query) {
                        $query->where('rpm', 0)->orWhere('counter', 0);
                    })
                    ->orderByDesc('created_at')
                    ->skip(1) // abaikan entri baru yang barusan disimpan
                    ->first();

                $shouldNotify = !$lastNotified || $lastNotified->created_at->diffInMinutes(now()) >= 60;

                if ($shouldNotify) {
                    Mail::to('it@amsgroup.co.id')->send(new MachineStoppedNotification($validated));
                    Log::warning('Notifikasi mesin berhenti dikirim', ['payload' => $validated]);
                } else {
                    Log::info('Mesin masih berhenti, tapi belum waktunya kirim notifikasi ulang');
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $validated,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data RPM: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
