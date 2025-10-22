<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SensorController extends Controller
{
    public function index()
    {
        $data = Sensor::latest()->take(20)->get()->reverse()->values();
        return response()->json([
            'labels' => $data->pluck('created_at')->map(fn($t) => $t->format('H:i:s')),
            'temperature' => $data->pluck('temperature'),
            'humidity' => $data->pluck('humidity'),
        ]);
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'temperature' => 'required|numeric',
                'humidity' => 'required|numeric',
                'device_id' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            Sensor::create($validated);
            Log::info('Data sensor disimpan', ['payload' => $validated]);

            return response()->json([
                'status' => 'success',
                'data' => $validated,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data sensor: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
