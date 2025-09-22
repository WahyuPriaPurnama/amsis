<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LogActivity;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $subsidiaryIds = [
            'ams' => 1,
            'eln1' => 2,
            'eln2' => 3,
            'bofi' => 4,
            'hk' => 5,
            'rmm' => 6,
        ];

        $employeeCounts = collect($subsidiaryIds)->mapWithKeys(function ($id, $key) {
            return [$key => Employee::where('subsidiary_id', $id)->count()];
        });

        $vehicleCounts = collect($subsidiaryIds)->mapWithKeys(function ($id, $key) {
            return [$key => Vehicle::where('subsidiary_id', $id)->count()];
        });

        return response()->json([
            'employees' => $employeeCounts,
            'vehicles' => $vehicleCounts,
        ]);
    }

    public function logActivity(): JsonResponse
    {
        $logs = \App\Helpers\LogActivity::logActivityLists();

        return response()->json([
            'logs' => $logs,
        ]);
    }

    public function truncate(): JsonResponse
    {
        if (!LogActivity::exists()) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Tidak ada data yang perlu dihapus',
            ], 404);
        }

        LogActivity::truncate();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dikosongkan!',
        ]);
    }
}
