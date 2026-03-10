<?php

namespace App\Imports;

use App\Models\HRD\Harian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class HarianImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Harian([
            'pin' => $row[0],
            'nip' => $row[1] === '' ? null : $row[1],
            'nama' => $row[2],
            'jadwal_kerja' => $row[3] === '' ? null : $row[3],
            'departemen' => $row[7] === '' ? null : $row[7],
            'bagian' => $row[8] === '' ? null : $row[8],
            'no_telp' => $row[12] === '' ? null : $row[12],
            'gaji' => $row[14] === '' ? 0.00 : (float)$row[14],
        ]);
    }

    public function startRow(): int
    {
        return 2; // Assuming the data starts from the third row in the Excel file
    }
}
