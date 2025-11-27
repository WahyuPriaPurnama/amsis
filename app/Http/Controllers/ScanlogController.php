<?php

namespace App\Http\Controllers;

use App\Exports\ScanlogExport;
use App\Imports\ScanlogImport;
use App\Models\Employee;
use App\Models\Scanlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScanlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Employee::class);
        $scanlogs = Scanlog::latest()->get();
        return view('scanlog.index', compact('scanlogs'));
    }
   
    public function prosesGaji()
    {
        $scanlogs = Scanlog::with('harian')->get();

        if ($scanlogs->isEmpty()) {
            return redirect()->route('scanlog.index')->with('alert2', 'Database kosong. Tidak ada data scanlog yang tersedia.');
        }

        $pinTidakDitemukan = [];

        foreach ($scanlogs as $scanlog) {
            $harian = $scanlog->harian;
            if ($harian && $scanlog->dk) {
                $gaji = ($harian->gaji ?? 0) * $scanlog->dk;
                $scanlog->update(['tgaji' => $gaji]);
            } else {
                $pinTidakDitemukan[] = $scanlog->pin;
            }
        }

        $pesan = count($pinTidakDitemukan) > 0
            ? 'Gaji berhasil diproses! Namun, beberapa PIN tidak ditemukan: ' . implode(', ', $pinTidakDitemukan)
            : 'Gaji berhasil diproses!';

        return redirect()->route('scanlog.index')->with('alert', $pesan);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx'
        ]);

        $file = $request->file('file');

        // membuat nama file unik
        $nama_file = $file->hashName();

        //temporary file
        $path = $file->storeAs('public/excel/', $nama_file);
        try {
            $import = Excel::import(new ScanlogImport(), storage_path('app/public/excel/' . $nama_file));
            $pesan = ['alert' => 'data berhasil diimport!'];
        } catch (\Exception $e) {
            $pesan = ['alert2' => 'data gagal diimport!'];
            Log::error('Gagal import: ' . $e->getMessage());
        }
        //remove from server
        Storage::delete($path);
        return redirect()->route('scanlog.index')->with($pesan);
    }

    public function export()
    {
        return Excel::download(new ScanlogExport(), "scanlog " . now() . ".xlsx");
    }

    public function scanPulang(Carbon $datetime)
    {
        $menitPulang = $datetime->minute;
        if ($menitPulang <= 20) {
            return $datetime->copy()->startOfHour();
        } else if ($menitPulang <= 49) {
            return $datetime->copy()->setMinute(30)->setSecond(0);
        } else {
            return $datetime->copy()->addHour()->startOfHour();
        }
    }

    public function scanMasuk(Carbon $datetime, Carbon $jamMasukJadwal)
    {
        $menitMasuk = $datetime->minute;
        if ($menitMasuk <= 10) {
            $hasil = $datetime->copy()->startOfHour();
        } else if ($menitMasuk <= 40) {
            $hasil = $datetime->copy()->setMinute(30)->setSecond(0);
        } else {
            $hasil = $datetime->copy()->addHour()->startOfHour();
        }
        return $hasil->lt($jamMasukJadwal) ? $jamMasukJadwal : $hasil;
    }


    public function durasiKerja(Carbon $masuk, Carbon $pulang, Carbon $jamMasukJadwal)
    {
        $scanMasuk = $this->scanMasuk($masuk, $jamMasukJadwal);
        $scanPulang = $this->scanPulang($pulang);

        if (empty($scanMasuk) or empty($scanPulang) or $scanMasuk->format('H:i:s') === '00:00:00' or $scanPulang->format('H:i:s') === '00:00:00') {
            return null;
        }

        //shift malam (jam pulang < jam masuk)
        if ($scanPulang->lt($scanMasuk)) {
            $scanPulang->addDay();
        }

        //hitung durasi kerja dalam jam (float)
        $durasiMenit = $scanMasuk->diffInRealMinutes($scanPulang);
        $durasiJam = $durasiMenit / 60;

        //potong istirahat
        if ($durasiJam >= 10) {
            $durasiJam -= 1.5;
        } else {
            $durasiJam -= 1;
        }

        //pastikan selalu positif
        return $durasiJam > 0 ? round($durasiJam, 2) : 0;
    }

    public function convert()
    {
        $datas = Scanlog::where('status', 0)->get();
        if ($datas->isEmpty()) {
            return redirect()->route('scanlog.index')->with('alert2', 'tidak ada data yang perlu diproses');
        }
        $berhasil = 0;
        foreach ($datas as $data) {
            $jamMasukJadwal = $data->jm ? Carbon::parse($data->jm) : null;
            $scanMasuk = $data->sm ? $this->scanMasuk(Carbon::parse($data->sm), $jamMasukJadwal) : null;
            $scanPulang = $data->sp ? $this->scanPulang(Carbon::parse($data->sp)) : null;

            // Cek tidak lengkap atau tidak valid
            if (
                empty($scanMasuk) ||
                empty($scanPulang) ||
                ($scanMasuk instanceof \Carbon\Carbon && $scanMasuk->format('H:i:s') === '00:00:00') ||
                ($scanPulang instanceof \Carbon\Carbon && $scanPulang->format('H:i:s') === '00:00:00')
            ) {
                $data->update([
                    'sm' => $scanMasuk,
                    'sp' => $scanPulang,
                    'status' => 2,
                ]);
                continue;
            }

            // Durasi kerja valid
            $durasi = $this->durasiKerja($scanMasuk, $scanPulang, $jamMasukJadwal);
            $updated = $data->update([
                'dk' => $durasi,
                'sm' => $scanMasuk,
                'sp' => $scanPulang,
                'status' => 1,
            ]);

            if ($updated) {
                $berhasil++;
            }
        }
        $pesan = $berhasil > 0
            ? "Berhasil, {$berhasil} data berhasil dikonversi ke jam."
            : "Gagal mengonversi data ke jam.";
        $alert = $berhasil > 0 ? 'alert' : 'alert2';
        return redirect()->route('scanlog.index')->with($alert, $pesan);
    }

    public function truncate()
    {
        if (!Scanlog::exists()) {
            return redirect()->route('scanlog.index')->with('alert2', 'tidak ada data yang perlu dihapus');
        }
        Scanlog::truncate();
        return redirect()->route('scanlog.index')->with('alert', 'data berhasil dikosongkan!');
    }
}
