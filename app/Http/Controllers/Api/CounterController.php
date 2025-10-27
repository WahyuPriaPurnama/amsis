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
