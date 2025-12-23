<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Models\HRD\Asset;
use App\Models\HRD\Subsidiary;
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
        $user = Auth::user();

        $fullAccessRoles = ['super-admin', 'holding-admin'];
        $roleSubsidiaryMap = [
            'eln-admin'   => 2,
            'eln2-admin'  => 3,
            'bofi-admin'  => 4,
            'haka-admin'  => 5,
            'rmm-admin'   => 6,
        ];

        // Ganti model Employee -> Asset
        $query = Asset::query()->latest();

        // Batasi akses berdasarkan role
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
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 20);

        $assets = $query->paginate($perPage)->withQueryString();

        return view('hrd.asset.index', compact('assets'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subsidiaries = Subsidiary::all();
        return view('hrd.asset.create', compact('subsidiaries'));
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
        return view('hrd.asset.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $subsidiaries = Subsidiary::all();
        return view('hrd.asset.edit', compact('asset', 'subsidiaries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetRequest $request, Asset $asset)
    {
        $asset->update($request->validated());
        return redirect()->route('asset.index')->with('success', 'Asset berhasil diperbarui.');
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
}
