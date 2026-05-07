<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubsidiaryRequest;
use App\Http\Requests\TransferEmployeeRequest;
use App\Models\HRD\Employee;
use App\Models\HRD\Subsidiary;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubsidiaryController extends Controller
{
    use FileUpload;

    public function index()
    {
        $subsidiaries = Subsidiary::withCount('employees')->get();
        return view('hrd.subsidiary.index', compact('subsidiaries'));
    }

    public function create()
    {
        return view('hrd.subsidiary.form', [
            'subsidiary' => new Subsidiary(),
            'isEdit' => false,
        ]);
    }

    public function store(SubsidiaryRequest $request)
    {
        $subsidiary = Subsidiary::create($request->validated());

        if ($request->hasFile('logo')) {
            $logo = $this->fileUpload($request, 'public/subsidiary/logo', 'logo');
            $subsidiary->update(['logo' => $logo->hashName()]);
        }
        if ($request->hasFile('kop_header')) {
            $kop_header = $this->fileUpload($request, 'public/subsidiary/kop_header', 'kop_header');
            $subsidiary->update(['kop_header' => $kop_header->hashName()]);
        }
        if ($request->hasFile('kop_footer')) {
            $kop_footer = $this->fileUpload($request, 'public/subsidiary/kop_footer', 'kop_footer');
            $subsidiary->update(['kop_footer' => $kop_footer->hashName()]);
        }

        return redirect()->route('subsidiaries.index')->with('success', 'Input data berhasil');
    }

    public function show(Subsidiary $subsidiary)
    {
        return view('hrd.subsidiary.show', compact('subsidiary'));
    }


    public function edit(Subsidiary $subsidiary)
    {
        return view('hrd.subsidiary.form', [
            'subsidiary' => $subsidiary,
            'isEdit' => true,
        ]);
    }

    public function update(SubsidiaryRequest $request, Subsidiary $subsidiary)
    {
        $subsidiary->update($request->validated());

        if ($request->hasFile('logo')) {
            Storage::disk('local')->delete("public/subsidiary/logo/{$subsidiary->logo}");

            $logo = $this->fileUpload($request, 'public/subsidiary/logo', 'logo');
            $subsidiary->update(['logo' => $logo->hashName()]);
        }
        if ($request->hasFile('kop_header')) {
            Storage::disk('local')->delete("public/subsidiary/kop_header/{$subsidiary->kop_header}");

            $kop_header = $this->fileUpload($request, 'public/subsidiary/kop_header', 'kop_header');
            $subsidiary->update(['kop_header' => $kop_header->hashName()]);
        }
        if ($request->hasFile('kop_footer')) {
            Storage::disk('local')->delete("public/subsidiary/kop_footer/{$subsidiary->kop_footer}");

            $kop_footer = $this->fileUpload($request, 'public/subsidiary/kop_footer', 'kop_footer');
            $subsidiary->update(['kop_footer' => $kop_footer->hashName()]);
        }

        return redirect()->route('subsidiaries.index')->with('success', "Update data {$subsidiary->name} berhasil");
    }

    public function destroy(Subsidiary $subsidiary)
    {
        if ($subsidiary->employees()->count() > 0) {
            return redirect()->route('subsidiaries.index')->with('error', "Tidak dapat menghapus {$subsidiary->name} karena masih ada karyawan di plant tersebut.");
        }
        Storage::disk('local')->delete("public/subsidiary/logo/{$subsidiary->logo}");
        Storage::disk('local')->delete("public/subsidiary/kop_header/{$subsidiary->kop_header}");
        Storage::disk('local')->delete("public/subsidiary/kop_footer/{$subsidiary->kop_footer}");
        $subsidiary->delete();

        return redirect()->route('subsidiaries.index')->with('success', "Hapus data {$subsidiary->name} berhasil");
    }

    public function transferView()
    {
        $subsidiaries = Subsidiary::withCount('employees')->get();
        return view('hrd.subsidiary.transfer', compact('subsidiaries'));
    }

    public function transferStore(TransferEmployeeRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Employee::where('subsidiary_id', $request->from_subsidiary_id)
                    ->update(['subsidiary_id' => $request->to_subsidiary_id]);
            });

            // Ambil data plant tujuan untuk ditampilkan pada pesan sukses
            $toPlant = Subsidiary::find($request->to_subsidiary_id);

            return redirect()->route('subsidiaries.index')
                ->with('success', "Seluruh karyawan berhasil dipindahkan ke {$toPlant->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memindahkan karyawan: ' . $e->getMessage());
        }
    }
}
