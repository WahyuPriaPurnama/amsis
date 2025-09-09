<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
        $subsidiaryIds = [
            'ams' => 1,
            'eln1' => 2,
            'eln2' => 3,
            'bofi' => 4,
            'hk' => 5,
            'rmm' => 6,
        ];

        $counts = collect($subsidiaryIds)->mapWithKeys(function ($id, $key) {
            return [$key => Employee::where('subsidiary_id', $id)->count()];
        });

        return view('dashboard', $counts->toArray());
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function logActivity()
    {
        $logs = \App\Helpers\LogActivity::logActivityLists();
        return view('logActivity', compact('logs'));
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
