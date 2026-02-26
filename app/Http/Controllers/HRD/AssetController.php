<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // cek permission untuk asset
        if ($user->can('asset.list')) {
            $query = Asset::with(['subsidiary', 'user']) // relasi yang relevan
                ->latest();

            if ($user->hasAnyRole(['super-admin', 'holding-admin'])) {
                $allSubsidiaries = \App\Models\HRD\Subsidiary::all();
            } else {
                $subsidiaryIds = $user->roles->flatMap(fn($role) => $role->subsidiaries->pluck('id'))->unique();
                $allSubsidiaries = \App\Models\HRD\Subsidiary::whereIn('id', $subsidiaryIds)->get();
                $query->whereIn('subsidiary_id', $subsidiaryIds);
            }

            if ($subsidiaryId = $request->input('subsidiary_id')) {
                $query->where('subsidiary_id', $subsidiaryId);
            }

            // pencarian
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('subsidiary', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $assets = $query->paginate(25)->appends(['search' => $search]);

            return view('hrd.asset.index', compact('assets', 'search', 'allSubsidiaries'));
        }

        abort(403, 'Anda tidak memiliki izin untuk melihat Asset.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // jika user super-admin atau holding-admin boleh lihat semua
        if ($user->hasRole(['super-admin', 'holding-admin'])) {
            $subsidiaries = Subsidiary::all();
        } else {
            // hanya subsidiary asal user
            $subsidiaries = Subsidiary::where('id', $user->subsidiary_id)->get();
        }

        $categories = ['Tanah & Bangunan', 'Mesin', 'Furniture & Fixture', 'Kendaraan', 'Alat Kerja', 'Fasilitas'];
        $conditions = ['Baik', 'Rusak', 'Lainnya'];
        $owners     = ['Umum', 'Engineering', 'QC & Lab'];

        return view('hrd.asset.create', compact('subsidiaries', 'categories', 'conditions', 'owners'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(AssetRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')
                ->store('assets/attachments', 'public');
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('assets/photos', 'public');
        }

        if ($request->hasFile('delivery_receipt')) {
            $data['delivery_receipt'] = $request->file('delivery_receipt')
                ->store('assets/delivery_receipts', 'public');
        }

        if ($request->hasFile('manual_book')) {
            $data['manual_book'] = $request->file('manual_book')
                ->store('assets/manual_books', 'public');
        }


        $asset = Asset::create($data);

        return redirect()->route('asset.index')->with('success', 'Asset berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $user = auth()->user();

        // batasi akses: hanya super-admin/holding-admin atau asset milik subsidiary user
        if (
            !$user->hasRole(['super-admin', 'holding-admin']) &&
            $user->subsidiary_id !== $asset->subsidiary_id
        ) {
            abort(403, 'Anda tidak memiliki izin untuk melihat Asset ini.');
        }

        // eager load relasi agar view lebih efisien
        $asset->load(['subsidiary', 'user']);

        return view('hrd.asset.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $user = auth()->user();

        // super-admin & holding-admin boleh lihat semua subsidiary
        if ($user->hasRole(['super-admin', 'holding-admin'])) {
            $subsidiaries = Subsidiary::all();
        } else {
            // hanya subsidiary asal user
            $subsidiaries = Subsidiary::where('id', $user->subsidiary_id)->get();

            // tambahan: kalau asset bukan milik subsidiary user, tolak akses
            if ($asset->subsidiary_id !== $user->subsidiary_id) {
                abort(403, 'Anda tidak memiliki izin untuk mengedit Asset ini.');
            }
        }

        return view('hrd.asset.edit', compact('asset', 'subsidiaries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetRequest $request, Asset $asset)
    {

        $asset->update($request->except(['attachment', 'photo', 'delivery_receipt', 'manual_books']));

        $fileFields = ['attachment', 'photo', 'delivery_receipt', 'manual_books'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {

                if ($asset->$field) {
                    Storage::disk('public')->delete($asset->$field);
                }

                $path = $request->file($field)->store("assets/{$field}", 'public');
                $asset->update([$field => $path]);
            }
        }

        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {

        $fileFields = [
            'attachment',
            'photo',
            'delivery_receipt',
            'manual_book',
        ];

        foreach ($fileFields as $field) {
            if ($asset->$field) {
                Storage::disk('public')->delete($asset->$field);
            }
        }

        $asset->delete();

        return redirect()->route('asset.index')->with('success', 'Asset berhasil dihapus.');
    }

    public function photo($id)
    {
        $asset = Asset::findOrFail($id);
        if (!$asset->photo || !Storage::disk('public')->exists($asset->photo)) {
            abort(404, 'File tidak ditemukan');
        }
        return response()->file(Storage::disk('public')->path($asset->photo));
    }

    public function attachment($id)
    {
        $asset = Asset::findOrFail($id);
        $path = $asset->attachment;
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }
        return response()->file(Storage::disk('public')->path($path));
    }
    public function delivery_receipt($id)
    {
        $asset = Asset::findOrFail($id);
        $path = $asset->delivery_receipt;
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }
        return response()->file(Storage::disk('public')->path($path));
    }
    public function manual_book($id)
    {
        $asset = Asset::findOrFail($id);
        $path = $asset->manual_book;
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }
        return response()->file(Storage::disk('public')->path($path));
    }
    protected function getSubsidiaryIdByRole($user, array $roleSubsidiaryMap)
    {
        // Jika user pakai Spatie Permission
        if (method_exists($user, 'hasRole')) {
            foreach ($roleSubsidiaryMap as $role => $subsidiaryId) {
                if ($user->hasRole($role)) {
                    return $subsidiaryId;
                }
            }
        }

        // Jika user punya property role biasa
        if (!empty($user->role) && isset($roleSubsidiaryMap[$user->role])) {
            return $roleSubsidiaryMap[$user->role];
        }

        return null; // tidak dikenali
    }

    public function export_pdf(Request $request)
    {
        $user = auth()->user();
        $subsidiaryId = $user->subsidiary_id;
        if ($user->hasRole('super-admin') || $user->hasRole('holding-admin')) {
            // Jika memilih subsidiary → filter sesuai pilihan
            if ($request->filled('subsidiary_id')) {
                $subsidiaryId = $request->input('subsidiary_id');
                $assets = Asset::with(['subsidiary', 'user'])
                    ->where('subsidiary_id', $subsidiaryId)
                    ->latest()
                    ->get();
                $subsidiary = Subsidiary::findOrFail($subsidiaryId);
            } else {
                // Jika tidak memilih subsidiary → tampilkan semua
                $assets = Asset::with(['subsidiary', 'user'])
                    ->latest()
                    ->get();
                $subsidiary = null; // atau bisa diisi label "Semua Subsidiary"
            }
        } else {
            // User biasa → hanya subsidiary miliknya
            $assets = Asset::with(['subsidiary', 'user'])
                ->where('subsidiary_id', $subsidiaryId)
                ->latest()
                ->get();
            $subsidiary = Subsidiary::findOrFail($subsidiaryId);
        }

        $pdf = Pdf::loadView('hrd.asset.export_pdf', compact('assets', 'subsidiary'));

        return $pdf->stream('asset_list.pdf');
    }
    public function export_excel()
    {
        $assets = Asset::with(['subsidiary', 'user'])->latest()->get();

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="asset_list.xlsx"',
        ];

        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Kode', 'Nama', 'Kategori', 'Lokasi', 'Kondisi', 'Pemilik', 'Subsidiary', 'Dibuat Oleh', 'Dibuat Pada']);

            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->id,
                    $asset->code,
                    $asset->name,
                    $asset->category,
                    $asset->location,
                    $asset->condition,
                    $asset->owner,
                    $asset->subsidiary->name ?? '',
                    $asset->user->name ?? '',
                    $asset->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
