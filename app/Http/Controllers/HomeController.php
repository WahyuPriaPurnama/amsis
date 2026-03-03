<?php

namespace App\Http\Controllers;

use App\Models\HRD\Subsidiary;
use App\Models\LogActivity;

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
        // 1. Ambil semua data plant/subsidiary dari database
        $subsidiaries = Subsidiary::withCount(['employees', 'vehicles'])->get();

        // 2. Siapkan data untuk Chart.js (Labels dan Data Karyawan)
        $chartData = [
            'labels'   => $subsidiaries->pluck('name'), // Ambil nama plant otomatis
            'employee' => $subsidiaries->pluck('employees_count'), // Hasil withCount
            'vehicle'  => $subsidiaries->pluck('vehicles_count'),
        ];

        // 3. Kirim ke view
        return view('admin.dashboard', compact('subsidiaries', 'chartData'));
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
