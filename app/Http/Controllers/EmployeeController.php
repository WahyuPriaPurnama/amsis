<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Helpers\EntityResolver;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Section;
use App\Models\Subsidiary;
use App\Models\User;
use App\Traits\FileUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
    public function index()
    {
        $this->authorize('view', Employee::class);

        $user = Auth::user();

        // Role yang bisa melihat semua data
        $fullAccessRoles = ['super-admin', 'holding-admin'];

        // Mapping role ke subsidiary_id
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        // Base query
        $query = Employee::Index()->latest();

        // Filter berdasarkan role jika bukan full access
        if (!in_array($user->role, $fullAccessRoles)) {
            $subsidiaryId = $roleSubsidiaryMap[$user->role] ?? null;

            if ($subsidiaryId) {
                $query->whereHas('subsidiary', function ($q) use ($subsidiaryId) {
                    $q->where('id', $subsidiaryId);
                });
            } else {
                // Jika role tidak dikenali, bisa redirect atau tampilkan kosong
                abort(403, 'Role tidak dikenali');
            }
        }

        $employees = $query->paginate(1000);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Employee::class);

        $user = Auth::user();

        // Role yang bisa akses semua subsidiaries
        $fullAccessRoles = ['super-admin', 'holding-admin'];

        // Mapping role ke subsidiary_id
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        if (in_array($user->role, $fullAccessRoles)) {
            $subsidiaries = Subsidiary::all();
        } else {
            $subsidiaryId = $roleSubsidiaryMap[$user->role] ?? null;

            if ($subsidiaryId) {
                $subsidiaries = Subsidiary::where('id', $subsidiaryId)->get();
            } else {
                abort(403, 'Role tidak dikenali');
            }
        }

        $divisions = Division::select('name')
            ->groupBy('name')
            ->orderBy('name', 'asc')
            ->get();
        $departments = Department::select('name')
            ->groupBy('name')
            ->orderBy('name', 'asc')
            ->get();
        $sections = Section::select('name')
            ->groupBy('name')
            ->orderBy('name', 'asc')
            ->get();
        $positions = Position::select('name')
            ->groupBy('name')
            ->orderBy('name', 'asc')
            ->get();

        return view('employees.create', compact('subsidiaries', 'divisions', 'departments', 'sections', 'positions'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $this->authorize('create', Employee::class);
        \App\Helpers\LogActivity::addToLog();
        dd($request->all());
        $employee = Employee::create($request->validated());
        // Handle dynamic relations
        $divisionId = EntityResolver::resolve(Division::class, $request->division_id, $request->division_name);
        $departmentId = EntityResolver::resolve(Department::class, $request->department_id, $request->department_name);
        $sectionId = EntityResolver::resolve(Section::class, $request->section_id, $request->section_name);
        $positionId = EntityResolver::resolve(Position::class, $request->position_id, $request->position_name);

        $data = $request->validated();
        $data['division_id'] = $divisionId;
        $data['department_id'] = $departmentId;
        $data['section_id'] = $sectionId;
        $data['position_id'] = $positionId;

        $employee = Employee::create($data);
        // Buat email dari nama
        $baseEmail = Str::slug($employee->nama, '.');
        $email = strtolower("{$baseEmail}");

        // Pastikan email unik
        $counter = 1;
        $originalEmail = $email;
        while (User::where('email', $email)->exists()) {
            $email = "{$baseEmail}{$counter}";
            $counter++;
        }

        // Buat user
        User::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'name' => $employee->nama,
                'email' => $email,
                'password' => Hash::make('Karyawan_2025'),
                'role' => 'employee',
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
        $this->authorize('update', $employee);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $this->authorize('update', $employee);
        \App\Helpers\LogActivity::addToLog();

        $role = Auth::user()->role;

        if (in_array($role, ['super-admin', 'holding-admin'])) {
            $subsidiaries = Subsidiary::all();
        } elseif ($role === 'eln-admin') {
            $subsidiaries = Subsidiary::where('id', 2)->get();
        } elseif ($role === 'eln2-admin') {
            $subsidiaries = Subsidiary::where('id', 3)->get();
        } elseif ($role === 'bofi-admin') {
            $subsidiaries = Subsidiary::where('id', 4)->get();
        } elseif ($role === 'rmm-admin') {
            $subsidiaries = Subsidiary::where('id', 6)->get();
        } elseif ($role === 'employee') {
            // ✅ Karyawan hanya bisa lihat subsidiary miliknya sendiri
            $subsidiaries = Subsidiary::where('id', $employee->subsidiary_id)->get();
        } else {
            // fallback: hanya subsidiary default
            $subsidiaries = Subsidiary::where('id', 5)->get();
        }
        $user = auth()->user();

        // Cek apakah user adalah karyawan dan sedang edit datanya sendiri
        $isEmployee = $user->role === 'employee' && $user->employee_id === $employee->id;

        return view('employees.edit', compact('employee', 'subsidiaries', 'isEmployee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        \App\Helpers\LogActivity::addToLog();
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
        $this->authorize('delete', Employee::class);
        \App\Helpers\LogActivity::addToLog();
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
        $this->authorize('view', Employee::class);
        $employees = Employee::all();
        $subsidiary = Subsidiary::find(1);
        $timestamp = now()->format('d/m/Y H:i:s');
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '512M');
        $pdf = pdf::loadview('employees.pdf.index', ['employees' => $employees, 'timestamp' => $timestamp, 'subsidiary' => $subsidiary])
            ->setPaper('letter', 'landscape');
        return $pdf->stream('data-karyawan-' . now()->format('d-M-Y') . '.pdf');
    }

    public function index_excel()
    {
        $tgl = now()->format('d-M-Y');
        $namaFile = 'data-karyawan-' . $tgl . '.xlsx';
        return Excel::download(new EmployeeExport, $namaFile);
    }

    public function show_pdf($id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorize('update', $employee);

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
