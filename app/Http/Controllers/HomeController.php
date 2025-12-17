<?php

namespace App\Http\Controllers;

use App\Models\HRD\Employee;
use App\Models\LogActivity;
use App\Models\HRD\Vehicle;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
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

        return view('admin.dashboard', [
            'ams' => $employeeCounts['ams'],
            'eln1' => $employeeCounts['eln1'],
            'eln2' => $employeeCounts['eln2'],
            'bofi' => $employeeCounts['bofi'],
            'hk' => $employeeCounts['hk'],
            'rmm' => $employeeCounts['rmm'],
            'ams_vehicles' => $vehicleCounts['ams'],
            'eln1_vehicles' => $vehicleCounts['eln1'],
            'eln2_vehicles' => $vehicleCounts['eln2'],
            'bofi_vehicles' => $vehicleCounts['bofi'],
            'hk_vehicles' => $vehicleCounts['hk'],
            'rmm_vehicles' => $vehicleCounts['rmm'],
        ]);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function logActivity()
    {
        $logs = \App\Helpers\LogActivity::logActivityLists();
        return view('admin.log-activity', compact('logs'));
    }
    public function truncate()
    {
        if (!LogActivity::exists()) {
            return redirect()->route('log.activity')->with('alert2', 'tidak ada data yang perlu dihapus');
        }
        LogActivity::truncate();
        return redirect()->route('log.activity')->with('alert', 'data berhasil dikosongkan!');
    }
}
