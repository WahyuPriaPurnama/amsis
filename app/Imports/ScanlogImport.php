<?php

namespace App\Imports;

use App\Models\HRD\Scanlog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ScanlogImport implements ToModel, WithStartRow, WithChunkReading, ShouldQueue
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquen
     */
    public function model(array $row)
    {

        return new Scanlog([
            'tgl' => date('Y-m-d', strtotime($row[0])),
            'jadwal' => $row[1],
            'jk' => $row[2],
            'pin' => $row[3],
            'nip' => $row[4],
            'nama' => $row[5],
            'dept' => $row[6] === '' ? null : $row[6],
            'bagian' => $row[7] === '' ? null : $row[7],
            'upah' => $row[8] === '' ? null : $row[8],
            'jm' => $row[9] === '' ? null : $row[9],
            'sm' => $row[10] === '' ? null : $row[10],
            'jp' => $row[11] === '' ? null : $row[11],
            'sp' => $row[12] === '' ? null : $row[12],
        ]);
    }

    public function startRow(): int
    {
        return 3;
    }

    public function chunkSize(): int
    {
        return 1000; // Adjust the chunk size as needed
    }
}
