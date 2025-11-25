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
        return response()->json([
            'labels' => $data->pluck('created_at')->map(fn($t) => $t->format('H:i:s')),
            'rpm' => $data->pluck('rpm'),
            'counter' => $data->pluck('counter'),
        ]);
    }

    public function indexhourly()
    {
        $start = now()->startOfDay()->addHours(7); // mulai jam 07:00 hari ini

        $raw = Counter::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour,
            MAX(counter) as max_counter,
            AVG(rpm) as avg_rpm
        ')
            ->where('created_at', '>=', $start)
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        $labels = collect();
        $rpm = collect();
        $counter = collect();

        $prev = null;

        foreach ($raw as $row) {
            $current = (int) $row->max_counter;

            if ($prev === null) {
                // jam pertama selalu 0
                $counter->push(0);
            } else {
                if ($current < $prev) {
                    $counter->push($current); // reset
                } else {
                    $counter->push($current - $prev);
                }
            }

            $labels->push(\Carbon\Carbon::parse($row->hour)->format('H:i'));
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
