<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\Subsidiary;
use App\Models\User;
use App\Traits\FileUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = Role::findByName('eln-admin');
        $user = Auth::user();

        $fullAccessRoles = ['super-admin', 'holding-admin'];
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        $query = Employee::Index()->latest();

        if (!(method_exists($user, 'hasAnyRole') ? $user->hasAnyRole($fullAccessRoles) : in_array($user->role, $fullAccessRoles))) {
            $subsidiaryId = $this->getSubsidiaryIdByRole($user, $roleSubsidiaryMap);

            if ($subsidiaryId) {
                $query->whereHas('subsidiary', fn($q) => $q->where('id', $subsidiaryId));
            } else {
                abort(403, 'Role tidak dikenali');
            }
        }

        $employees = $query->paginate(1000);

        return view('employees.index', compact('employees'));
    }
    protected function getSubsidiaryIdByRole($user, array $roleSubsidiaryMap): ?int
    {
        // Gunakan Spatie jika tersedia, fallback ke properti 'role'
        $role = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->first()
            : $user->role;

        return $roleSubsidiaryMap[$role] ?? null;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

        // Tentukan subsidiaries berdasarkan role
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($fullAccessRoles)) {
            $subsidiaries = Subsidiary::all();
        } elseif (method_exists($user, 'hasRole')) {
            foreach ($roleSubsidiaryMap as $role => $id) {
                if ($user->hasRole($role)) {
                    $subsidiaries = Subsidiary::where('id', $id)->get();
                    break;
                }
            }

            if (!isset($subsidiaries)) {
                abort(403, 'Role tidak dikenali');
            }
        } else {
            // Fallback jika trait belum aktif
            $subsidiaries = Subsidiary::where('id', 5)->get();
        }

        return view('employees.create', compact('subsidiaries'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
        $data = $request->validated();
        $employee = Employee::create($data);
        // Buat email dari nama
        $fullname = Str::slug($employee->nama, '.');
        $username = strtolower("{$fullname}");

        // Pastikan email unik
        $counter = 1;
        while (User::where('email', $username)->exists()) {
            $email = "{$username}{$counter}";
            $counter++;
        }

        // Buat user
        User::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'name' => $employee->nama,
                'email' => $email,
                'password' => Hash::make('Karyawan_2025'),
                'employee_id' => $employee->id,
                'subsidiary_id' => $employee->subsidiary_id,
            ]
        );

        $documents = [
            'pp' => 'public/foto_profil',
            'ktp' => 'public/KTP',
            'npwp2' => 'public/NPWP',
            'kk' => 'public/Kartu Keluarga',
            'bpjs_kes' => 'public/BPJS Kesehatan',
            'bpjs_ket' => 'public/BPJS Ketenagakerjaan',
        ];

        foreach ($documents as $field => $path) {
            if ($request->file($field)) {
                $file = $this->fileUpload($request, $path, $field);
                $employee->update([$field => $file->hashName()]);
            }
        }
        $nama = $employee->nama;

        return redirect()
            ->route('employees.index')
            ->with($employee ? 'alert' : 'alert2', "Input data {$nama} " . ($employee ? 'berhasil' : 'gagal'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $user = Auth::user();

        // Subsidiary mapping by role
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        // Full access roles
        $fullAccessRoles = ['super-admin', 'holding-admin'];

        // Determine subsidiaries based on role
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($fullAccessRoles)) {
            $subsidiaries = Subsidiary::all();
        } elseif (method_exists($user, 'hasRole')) {
            foreach ($roleSubsidiaryMap as $role => $id) {
                if ($user->hasRole($role)) {
                    $subsidiaries = Subsidiary::where('id', $id)->get();
                    break;
                }
            }

            // Karyawan hanya bisa lihat subsidiary miliknya sendiri
            if ($user->hasRole('employee')) {
                $subsidiaries = Subsidiary::where('id', $employee->subsidiary_id)->get();
            }

            // Fallback jika tidak cocok dengan role manapun
            if (!isset($subsidiaries)) {
                $subsidiaries = Subsidiary::where('id', 5)->get();
            }
        } else {
            // Fallback jika trait belum aktif
            $subsidiaries = Subsidiary::where('id', 5)->get();
        }

        // Cek apakah user adalah karyawan dan sedang edit datanya sendiri
        $isEmployee = $user->hasRole('employee') && $user->employee_id === $employee->id;

        return view('employees.edit', compact('employee', 'subsidiaries', 'isEmployee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {

        $employee->update($request->validated());

        if ($request->file('pp')) {
            Storage::disk('local')->delete('public/foto_profil/' . $employee->pp);
            $pp = $this->fileUpload($request, 'public/foto_profil/', 'pp');
            $employee->update(['pp' => $pp->hashName()]);
        }
        if ($request->file('ktp')) {
            Storage::disk('local')->delete('public/KTP/' . $employee->ktp);
            $ktp = $this->fileUpload($request, 'public/KTP/', 'ktp');
            $employee->update(['ktp' => $ktp->hashName()]);
        }
        if ($request->file('npwp2')) {
            Storage::disk('local')->delete('public/NPWP/' . $employee->npwp2);
            $npwp = $this->fileUpload($request, 'public/NPWP/', 'npwp2');
            $employee->update(['npwp2' => $npwp->hashName()]);
        }

        if ($request->file('kk')) {
            Storage::disk('local')->delete('public/Kartu Keluarga/' . $employee->kk);
            $kk = $this->fileUpload($request, 'public/Kartu Keluarga/', 'kk');
            $employee->update(['kk' => $kk->hashName()]);
        }

        if ($request->file('bpjs_kes')) {
            Storage::disk('local')->delete('public/BPJS Kesehatan/' . $employee->bpjs_kes);
            $bpjs_kes = $this->fileUpload($request, 'public/BPJS Kesehatan/', 'bpjs_kes');
            $employee->update(['bpjs_kes' => $bpjs_kes->hashName()]);
        }

        if ($request->file('bpjs_ket')) {
            Storage::disk('local')->delete('public/BPJS Ketenagakerjaan/' . $employee->kbpjs_ket);
            $bpjs_ket = $this->fileUpload($request, 'public/BPJS Ketenagakerjaan/', 'bpjs_ket');
            $employee->update(['bpjs_ket' => $bpjs_ket->hashName()]);
        }

        if ($employee) {
            return redirect()->route('employees.show', ['employee' => $employee->id])->with('alert', "update data $request->nama berhasil");
        } else {
            return redirect()->route('employees.show', ['employee' => $employee->id])->with('alert2', "update data $request->nama gagal");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $data = Storage::disk('local');
        $data->delete('/public/foto_profil/' . $employee->pp);
        $data->delete('/public/Kartu Keluarga/' . $employee->kk);
        $data->delete('/public/BPJS Kesehatan/' . $employee->bpjs_kes);
        $data->delete('/public/BPJS Ketenagakerjaan/' . $employee->bpjs_ket);
        $data->delete('/public/KTP/' . $employee->ktp);
        $data->delete('/public/NPWP/' . $employee->npwp2);

        $employee->delete();

        if ($employee) {
            return redirect()->route('employees.index')->with('alert', "hapus data $employee->nama berhasil");
        } else {
            return redirect()->route('employees.index')->with('alert2', "hapus data $employee->nama gagal");
        }
    }

    public function pp($pp)
    {
        return Response::download('storage/foto_profil/' . $pp);
    }
    public function ktp($ktp)
    {

        return Response::download('storage/KTP/' . $ktp);
    }
    public function npwp($npwp)
    {

        return Response::download('storage/NPWP/' . $npwp);
    }
    public function kk($kk)
    {

        return Response::download('storage/Kartu Keluarga/' . $kk);
    }
    public function bpjs_ket($bpjs_ket)
    {

        return Response::download('storage/BPJS Ketenagakerjaan/' . $bpjs_ket);
    }
    public function bpjs_kes($bpjs_kes)
    {

        return Response::download('storage/BPJS Kesehatan/' . $bpjs_kes);
    }

    public function index_pdf()
    {

        $employees = Employee::all();
        $subsidiary = Subsidiary::find(1);
        $timestamp = now()->format('d/m/Y H:i:s');
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '512M');
        $pdf = pdf::loadview('employees.pdf.index', ['employees' => $employees, 'timestamp' => $timestamp, 'subsidiary' => $subsidiary])
            ->setPaper('letter', 'landscape');
        return $pdf->stream('data-karyawan-' . now()->format('d-M-Y') . '.pdf');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new EmployeeImport, $request->file('file'));

            return back()->with('alert', 'Import berhasil. Data pegawai telah ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('alert2', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function index_excel()
    {
        $tgl = now()->format('d-M-Y');
        $namaFile = 'data-karyawan per ' . $tgl . '.xlsx';
        return Excel::download(new EmployeeExport, $namaFile);
    }

    public function show_pdf($id)
    {
        $employee = Employee::findOrFail($id);

        $subsidiary = Subsidiary::find($employee->subsidiary_id);

        $employee->tgl_masuk_formatted = Carbon::make($employee->tgl_masuk)?->format('d/m/Y');

        if ($employee->status_peg === 'PKWT') {
            $employee->awal_kontrak_formatted = Carbon::make($employee->awal_kontrak)?->format('d/m/Y');
            $employee->akhir_kontrak_formatted = Carbon::make($employee->akhir_kontrak)?->format('d/m/Y');
        }

        $timestamp = now()->format('d/m/Y H:i:s');

        $filename = 'data-karyawan-' . Str::slug($employee->nama) . '-' . now()->format('d-m-Y') . '.pdf';

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('employees.pdf.show', compact('employee', 'timestamp', 'subsidiary'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream($filename);
    }
}
