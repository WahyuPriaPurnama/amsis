<?php

namespace App\Http\Controllers\HRD;

use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Imports\EmployeeImport;
use App\Models\HRD\Employee;
use App\Models\HRD\Subsidiary;
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

class EmployeeController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        $query = Employee::query()->latest();

        if (!(method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole($fullAccessRoles)
            : in_array($user->role, $fullAccessRoles))) {
            $subsidiaryId = $this->getSubsidiaryIdByRole($user, $roleSubsidiaryMap);

            if ($subsidiaryId) {
                $query->whereHas('subsidiary', fn($q) => $q->where('id', $subsidiaryId));
            } else {
                abort(403, 'Role tidak dikenali');
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }


        $perPage = $request->input('per_page', 20);

        $employees = $query->paginate($perPage)->withQueryString();

        return view('hrd.employee.index', compact('employees'));
    }

    protected function getSubsidiaryIdByRole($user, array $roleSubsidiaryMap): ?int
    {

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


        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($fullAccessRoles)) {
            $subsidiaries = Subsidiary::all();
        } elseif (method_exists($user, 'hasRole')) {
            $subsidiaries = collect();

            foreach ($roleSubsidiaryMap as $role => $id) {
                if ($user->hasRole($role)) {
                    $subsidiaries = Subsidiary::where('id', $id)->get();
                    break;
                }
            }

            if ($subsidiaries->isEmpty()) {
                abort(403, 'Role tidak dikenali');
            }
        } else {

            $subsidiaries = Subsidiary::where('id', 5)->get();
        }

        return view('hrd.employee.create', compact('subsidiaries'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        $employee = Employee::create($data);

        $fullname = Str::slug($employee->nama, '.');
        $username = strtolower($fullname);
        $email = $username;

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = "{$username}{$counter}";
            $counter++;
        }

        $user = User::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'name' => $employee->nama,
                'email' => $email,
                'password' => Hash::make('Karyawan_2025'),
                'employee_id' => $employee->id,
                'subsidiary_id' => $employee->subsidiary_id,
            ]
        );

        if (!$user->hasRole('employee')) {
            $user->assignRole('employee');
        }

        $documents = [
            'pp' => 'public/foto_profil',
            'ktp' => 'public/KTP',
            'npwp2' => 'public/NPWP',
            'kk' => 'public/Kartu Keluarga',
            'bpjs_kes' => 'public/BPJS Kesehatan',
            'bpjs_ket' => 'public/BPJS Ketenagakerjaan',
            'ttd' => 'public/ttd',
        ];

        foreach ($documents as $field => $path) {
            if ($request->file($field)) {
                $file = $this->fileUpload($request, $path, $field);
                $employee->update([$field => $file->hashName()]);
            }
        }

        return redirect()
            ->route('employee.index')
            ->with('success', "Input data {$employee->nama} berhasil");
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('hrd.employee.show', compact('employee'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $user = Auth::user();
        $fullAccessRoles = ['super-admin', 'holding-admin'];

        // Flag utama untuk otorisasi
        $isLeader = $user->hasAnyRole($fullAccessRoles);
        $isEmployeeSelf = $user->employee_id === $employee->id;

        // Gabungkan pengecekan Role atau Permission spesifik
        $canEditOrganization = $isLeader || $user->can('employee-organization.edit');

        // Tentukan list subsidiary
        $subsidiaries = $isLeader
            ? Subsidiary::all()
            : Subsidiary::where('id', $employee->subsidiary_id)->get();

        return view('hrd.employee.edit', compact(
            'employee',
            'subsidiaries',
            'isLeader',
            'isEmployeeSelf',
            'canEditOrganization',
            'user'
        ));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            \DB::transaction(function () use ($request, $employee) {
                // 1. Update data teks
                $employee->update($request->validated());

                // 2. Definisi mapping file [nama_input => path_storage]
                $fileFields = [
                    'pp'       => 'public/foto_profil',
                    'ktp'      => 'public/KTP',
                    'npwp2'    => 'public/NPWP',
                    'kk'       => 'public/Kartu Keluarga',
                    'bpjs_kes' => 'public/BPJS Kesehatan',
                    'bpjs_ket' => 'public/BPJS Ketenagakerjaan',
                    'ttd'      => 'public/ttd',
                ];

                // 3. Loop proses upload
                foreach ($fileFields as $field => $path) {
                    if ($request->hasFile($field)) {
                        // Hapus file lama jika ada
                        if ($employee->$field) {
                            Storage::disk('local')->delete($path . '/' . $employee->$field);
                        }

                        // Upload file baru
                        $file = $this->fileUpload($request, $path, $field);
                        $employee->update([$field => $file->hashName()]);
                    }
                }
            });

            return redirect()->route('employees.show', $employee->id)
                ->with('success', "Update data {$employee->nama} berhasil");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal memperbarui data: " . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Simpan nama untuk pesan sukses nanti
            $namaEmployee = $employee->nama;

            // Coba hapus data di database dulu
            $employee->delete();

            // JIKA BERHASIL, baru hapus file fisiknya
            $data = Storage::disk('local');
            $data->delete([
                '/public/foto_profil/' . $employee->pp,
                '/public/Kartu Keluarga/' . $employee->kk,
                '/public/BPJS Kesehatan/' . $employee->bpjs_kes,
                '/public/BPJS Ketenagakerjaan/' . $employee->bpjs_ket,
                '/public/KTP/' . $employee->ktp,
                '/public/NPWP/' . $employee->npwp2,
            ]);

            return redirect()->route('employees.index')->with('success', "Hapus data $namaEmployee berhasil");
        } catch (\Illuminate\Database\QueryException $e) {
            // Cek jika error disebabkan oleh relasi data (Foreign Key)
            if ($e->getCode() == "23000") {
                return redirect()->route('employees.index')->with('error', "Data $employee->nama tidak bisa dihapus karena masih terkait dengan data di tabel Request Orders.");
            }

            // Error database lainnya
            return redirect()->route('employees.index')->with('error', "Terjadi kesalahan database.");
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect()->route('employees.index')->with('error', "Gagal menghapus data: " . $e->getMessage());
        }
    }

    public function pp($pp, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));

        $path = storage_path('app/public/foto_profil/' . $pp);

        $downloadName = 'foto-' . $cleanName . '-' . $pp;

        return response()->download($path, $downloadName);
    }

    public function ktp($ktp, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));
        $path = storage_path('app/public/KTP/' . $ktp);
        $downloadName = 'ktp-' . $cleanName . '-' . $ktp;

        return response()->download($path, $downloadName);
    }

    public function npwp($npwp, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));
        $path = storage_path('app/public/NPWP/' . $npwp);
        $downloadName = 'npwp-' . $cleanName . '-' . $npwp;

        return response()->download($path, $downloadName);
    }

    public function kk($kk, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));
        $path = storage_path('app/public/Kartu Keluarga/' . $kk);
        $downloadName = 'kk-' . $cleanName . '-' . $kk;

        return response()->download($path, $downloadName);
    }

    public function bpjs_ket($bpjs_ket, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));
        $path = storage_path('app/public/BPJS Ketenagakerjaan/' . $bpjs_ket);
        $downloadName = 'bpjs-ketenagakerjaan-' . $cleanName . '-' . $bpjs_ket;

        return response()->download($path, $downloadName);
    }

    public function bpjs_kes($bpjs_kes, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));
        $path = storage_path('app/public/BPJS Kesehatan/' . $bpjs_kes);
        $downloadName = 'bpjs-kesehatan-' . $cleanName . '-' . $bpjs_kes;

        return response()->download($path, $downloadName);
    }

    public function ttd($ttd, $name)
    {
        $cleanName = strtolower(str_replace(' ', '-', $name));
        $path = storage_path('app/public/ttd/' . $ttd);
        $downloadName = 'ttd-' . $cleanName . '-' . $ttd;

        return response()->download($path, $downloadName);
    }
    public function index_pdf()
    {

        $employees = Employee::all();
        $subsidiary = Subsidiary::find(1);
        $timestamp = now()->format('d/m/Y H:i:s');
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '512M');
        $pdf = pdf::loadview('hrd.employee.pdf.index', ['employees' => $employees, 'timestamp' => $timestamp, 'subsidiary' => $subsidiary])
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

            return back()->with('success', 'Import berhasil. Data pegawai telah ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
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
        ])->loadView('hrd.employee.pdf.show', compact('employee', 'timestamp', 'subsidiary'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream($filename);
    }
}
