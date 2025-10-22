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
        $data = Sensor::latest()->take(50)->get(); // ambil 50 data terbaru
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'suhu' => 'required|numeric',
            'kelembapan' => 'required|numeric',
            'device_id' => 'nullable|string',
            'lokasi' => 'nullable|string',
        ]);

        // Simpan ke database atau log dulu
        Log::info('Data sensor diterima:', $validated);

        // Contoh respon
        return response()->json([
            'status' => 'success',
            'data' => $validated,
        ]);
    }
}
