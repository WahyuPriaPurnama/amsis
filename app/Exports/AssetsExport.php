<?php

namespace App\Exports;

use App\Models\HRD\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Mengambil data beserta relasi seperti di code Anda
        return Asset::with(['subsidiary', 'user'])->latest()->get();
    }

    // Mengatur baris judul (Header)
    public function headings(): array
    {
        return ['ID', 'Kode', 'Nama', 'Kategori', 'Lokasi', 'Kondisi', 'Pemilik', 'Subsidiary', 'Dibuat Oleh', 'Dibuat Pada'];
    }

    // Mengatur pemetaan data (Mapping)
    public function map($asset): array
    {
        return [
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
        ];
    }
}
