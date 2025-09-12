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

    public function __construct()
    {
        $this->authorizeResource(Subsidiary::class, 'subsidiary');
    }

    public function index()
    {
        $user = Auth::user();
        $fullAccessRoles = ['super-admin', 'holding-admin'];
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        $query = Subsidiary::Index()->latest();

        if (!$user->hasAnyRole($fullAccessRoles)) {
            $subsidiaryId = $roleSubsidiaryMap[$user->getRoleNames()->first()] ?? null;

            if ($subsidiaryId) {
                $query->where('id', $subsidiaryId);
            } else {
                abort(403, 'Role tidak dikenali atau tidak memiliki akses');
            }
        }

        $subsidiaries = $query->get();

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

        return redirect()->route('subsidiaries.index')->with('alert', "Update data {$subsidiary->name} berhasil");
    }

    public function destroy(Subsidiary $subsidiary)
    {
        Storage::disk('local')->delete("public/subsidiary/logo/{$subsidiary->logo}");
        $subsidiary->delete();

        return redirect()->route('subsidiaries.index')->with('alert', "Hapus data {$subsidiary->name} berhasil");
    }
}
