<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $start = now()->startOfDay()->addHours(7); // hari ini jam 07:00

        $raw = Counter::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, MAX(counter) as max_counter, AVG(rpm) as avg_rpm')
            ->where('created_at', '>=', $start)
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        // Hitung delta counter antar jam
        $delta = [];
        $prev = null;
        foreach ($raw as $row) {
            $current = (int) $row->max_counter;
            $delta[] = $prev === null ? $current : $current - $prev;
            $prev = $current;
        }

        return response()->json([
            'labels' => $raw->pluck('hour')->map(fn($h) => \Carbon\Carbon::parse($h)->format('H:i')),
            'rpm' => $raw->pluck('avg_rpm')->map(fn($v) => round($v, 2)),
            'counter' => collect($delta),
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

            Counter::create($validated);
            Log::info('Data RPM disimpan', ['payload' => $validated]);

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
