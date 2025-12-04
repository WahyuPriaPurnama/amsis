<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubsidiaryRequest;
use App\Models\Subsidiary;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubsidiaryController extends Controller
{
    use FileUpload;

    public function index()
    {
        $subsidiaries = Subsidiary::withCount('employees')->get();
        return view('subsidiaries.index', compact('subsidiaries'));
    }

    public function create()
    {
        return view('subsidiaries.form', [
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

        return redirect()->route('subsidiaries.index')->with('alert', 'Input data berhasil');
    }

    public function show(Subsidiary $subsidiary)
    {
        return view('subsidiaries.show', compact('subsidiary'));
    }


    public function edit(Subsidiary $subsidiary)
    {
        return view('subsidiaries.form', [
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

        return redirect()->route('subsidiaries.index')->with('alert', "Update data {$subsidiary->name} berhasil");
    }

    public function destroy(Subsidiary $subsidiary)
    {
        Storage::disk('local')->delete("public/subsidiary/logo/{$subsidiary->logo}");
        Storage::disk('local')->delete("public/subsidiary/kop_header/{$subsidiary->kop_header}");
        Storage::disk('local')->delete("public/subsidiary/kop_footer/{$subsidiary->kop_footer}");
        $subsidiary->delete();

        return redirect()->route('subsidiaries.index')->with('alert', "Hapus data {$subsidiary->name} berhasil");
    }
}
