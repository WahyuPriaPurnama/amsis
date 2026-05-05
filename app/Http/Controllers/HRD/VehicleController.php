<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\HRD\Subsidiary;
use App\Models\HRD\Vehicle;
use App\Traits\FileUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Vehicle::Index()->paginate(50);
        return view('hrd.vehicle.index', compact('data'));
    }


    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole('holding-admin') || $user->hasRole('super-admin')) {
            // Tampilkan semua subsidiary
            $sub = Subsidiary::orderBy('name')->get();
        } else {
            // Tampilkan hanya subsidiary milik user
            $sub = Subsidiary::where('id', $user->subsidiary_id)->get();
        }

        return view('hrd.vehicle.create', compact('sub'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleRequest $request)
    {
        \App\Helpers\LogActivity::addToLog();
        $data = Vehicle::create($request->validated());
        if ($request->file('foto')) {
            $foto = $this->fileUpload($request, 'public/vehicles/foto', 'foto');
            $data->update(['foto' => $foto->hashName()]);
        }
        if ($request->file('f_stnk')) {
            $stnk = $this->fileUpload($request, 'public/vehicles/stnk', 'f_stnk');
            $data->update(['f_stnk' => $stnk->hashName()]);
        }
        if ($request->file('f_pajak')) {
            $pajak = $this->fileUpload($request, 'public/vehicles/pajak', 'f_pajak');
            $data->update(['f_pajak' => $pajak->hashName()]);
        }
        if ($request->file('f_kir')) {
            $kir = $this->fileUpload($request, 'public/vehicles/kir', 'f_kir');
            $data->update(['f_kir' => $kir->hashName()]);
        }
        if ($request->file('qr')) {
            $qr = $this->fileUpload($request, 'public/vehicles/qr', 'qr');
            $data->update(['qr' => $qr->hashName()]);
        }
        if ($request->file('f_polis')) {
            $polis = $this->fileUpload($request, 'public/vehicles/polis', 'f_polis');
            $data->update(['f_polis' => $polis->hashName()]);
        }
        if ($request->file('f_service')) {
            $service = $this->fileUpload($request, 'public/vehicles/service', 'f_service');
            $data->update(['f_service' => $service->hashName()]);
        }
        return redirect()->route('vehicles.index')->with('success', 'data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return view('hrd.vehicle.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $sub = Subsidiary::all();
        return view('hrd.vehicle.edit', compact('sub', 'vehicle'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle::findOrFail($vehicle->id);
        $vehicle->update($request->validated());

        if ($request->file('foto')) {
            Storage::disk('local')->delete('public/vehicles/foto' . $vehicle->foto);
            $foto = $this->fileUpload($request, 'public/vehicles/foto', 'foto');
            $vehicle->update(['foto' => $foto->hashName()]);
        }
        if ($request->file('f_stnk')) {
            Storage::disk('local')->delete('public/vehicles/stnk' . $vehicle->stnk);
            $stnk = $this->fileUpload($request, 'public/vehicles/stnk', 'f_stnk');
            $vehicle->update(['f_stnk' => $stnk->hashName()]);
        }
        if ($request->file('f_pajak')) {
            Storage::disk('local')->delete('public/vehicles/pajak' . $vehicle->f_pajak);
            $pajak = $this->fileUpload($request, 'public/vehicles/pajak', 'f_pajak');
            $vehicle->update(['f_pajak' => $pajak->hashName()]);
        }

        if ($request->file('f_kir')) {
            Storage::disk('local')->delete('public/vehicles/kir/' . $vehicle->f_kir);
            $kir = $this->fileUpload($request, 'public/vehicles/kir', 'f_kir');
            $vehicle->update(['f_kir' => $kir->hashName()]);
        }

        if ($request->file('qr')) {
            Storage::disk('local')->delete('public/vehicles/qr/' . $vehicle->qr);
            $qr = $this->fileUpload($request, 'public/vehicles/qr', 'qr');
            $vehicle->update(['qr' => $qr->hashName()]);
        }
        if ($request->file('f_polis')) {
            Storage::disk('local')->delete('public/vehicles/polis/' . $vehicle->f_polis);
            $polis = $this->fileUpload($request, 'public/vehicles/polis', 'f_polis');
            $vehicle->update(['f_polis' => $polis->hashName()]);
        }
        if ($request->file('f_service')) {
            Storage::disk('local')->delete('public/vehicles/service/' . $vehicle->f_service);
            $service = $this->fileUpload($request, 'public/vehicles/service', 'f_service');
            $vehicle->update(['f_service' => $service->hashName()]);
        }

        return redirect()->route('vehicles.show', ['vehicle' => $vehicle->id])
            ->with($vehicle ? 'success' : 'error', $vehicle ? 'update data berhasil' : 'update data gagal');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $data = Storage::disk('local');
        $data->delete('/public/vehicles/foto/' . $vehicle->foto);
        $data->delete('/public/vehicles/stnk/' . $vehicle->stnk);
        $data->delete('/public/vehicles/pajak/' . $vehicle->pajak);
        $data->delete('/public/vehicles/kir/' . $vehicle->kir);
        $data->delete('/public/vehicles/qr/' . $vehicle->qr);
        $data->delete('/public/vehicles/polis/' . $vehicle->polis);
        $data->delete('/public/vehicles/service/' . $vehicle->f_service);
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with(
                $vehicle ? 'success' : 'error',
                "hapus data {$vehicle->jenis_kendaraan} " . ($vehicle ? 'berhasil' : 'gagal')
            );
    }

    public function foto($foto, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/foto/' . $foto);
        $downloadName = 'foto-' . $cleanJenis . '-' . $foto;

        return response()->download($path, $downloadName);
    }

    public function stnk($stnk, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/stnk/' . $stnk);
        $downloadName = 'stnk-' . $cleanJenis . '-' . $stnk;

        return response()->download($path, $downloadName);
    }

    public function pajak($pajak, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/pajak/' . $pajak);
        $downloadName = 'pajak-' . $cleanJenis . '-' . $pajak;

        return response()->download($path, $downloadName);
    }

    public function kir($kir, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/kir/' . $kir);
        $downloadName = 'kir-' . $cleanJenis . '-' . $kir;

        return response()->download($path, $downloadName);
    }

    public function qr($qr, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/qr/' . $qr);
        $downloadName = 'qr-' . $cleanJenis . '-' . $qr;

        return response()->download($path, $downloadName);
    }

    public function polis($polis, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/polis/' . $polis);
        $downloadName = 'polis-' . $cleanJenis . '-' . $polis;

        return response()->download($path, $downloadName);
    }

    public function service($service, $jenis)
    {
        $cleanJenis = strtolower(str_replace(' ', '-', $jenis));
        $path = storage_path('app/public/vehicles/service/' . $service);
        $downloadName = 'service-' . $cleanJenis . '-' . $service;

        return response()->download($path, $downloadName);
    }

    public function index_pdf()
    {
        $vehicles = Vehicle::all();
        $subsidiary = Subsidiary::find(1);
        $timestamp = now()->format('d/m/Y H:i:s');
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '512M');
        $pdf = pdf::loadview('hrd.vehicle.pdf.index', ['vehicles' => $vehicles, 'timestamp' => $timestamp, 'subsidiary' => $subsidiary])
            ->setPaper('letter', 'landscape');
        return $pdf->stream('data-kendaraan-' . now()->format('d-m-Y') . '.pdf');
    }

    public function show_pdf($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $subsidiary = Subsidiary::find($vehicle->subsidiary_id);
        $timestamp = now()->format('d-m-Y H:i:s');
        $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadview('hrd.vehicle.pdf.show', ['vehicle' => $vehicle, 'subsidiary' => $subsidiary, 'timestamp' => $timestamp])->setPaper('letter', 'landscape');
        return $pdf->stream('data-kendaraan-' . $vehicle->jenis_kendaraan . '-' . now()->format('d-m-Y') . '.pdf');
    }
}
