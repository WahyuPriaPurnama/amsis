<!DOCTYPE html>

<head>
    <title>Biodata {{ $employee->nama }}</title>
    <style>
        * {

            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        table,
        tr,
        th,
        td {

            border-collapse: collapse;
            vertical-align: top;
            padding: 1%;
            font-size: 90%;
        }

        table {
            width: 100%;
        }
    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left; vertical-align: middle;">
                <h2>{{ $employee->nama }}</h2>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/logo/' . $subsidiary->logo)) }}"
                    style="width: 150px; height: auto;">
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th colspan="2">ORGANISASI</th>
            <th colspan="2">BIODATA</th>
            <th colspan="2">KONTAK DARURAT</th>
        </tr>
        <tr>
            <td colspan="2">
                @php
                    $employeeData = [
                        'nip' => 'NIP',
                        'nama' => 'Nama',
                        'nik' => 'NIK',
                        'departemen' => 'Departemen',
                        'seksi' => 'Seksi',
                        'posisi' => 'Posisi',
                        'tgl_masuk_formatted' => 'Tanggal Masuk',
                        'status_peg' => 'Status Pegawai',
                    ];
                    if ($employee->status_peg == 'PKWT') {
                        $employeeData['awal_kontrak_formatted'] = 'Awal Kontrak';
                        $employeeData['akhir_kontrak_formatted'] = 'Akhir Kontrak';
                    }

                @endphp
                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($employeeData as $key => $label)
                        <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $employee->$key }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td colspan="2">
                @php
                    $personalData = [
                        'tmpt_lahir' => 'Tempat Lahir',
                        'tgl_lahir' => 'Tanggal Lahir',
                        'jenis_kelamin' => 'Jenis Kelamin',
                        'alamat' => 'Alamat',
                        'no_telp' => 'No. Telp',
                        'email' => 'Email',
                        'pend_trkhr' => 'Pendidikan Terakhir',
                        'jurusan' => 'Jurusan',
                        'thn_lulus' => 'Tahun Lulus',
                        'nama_ibu' => 'Nama Ibu',
                        'npwp' => 'NPWP',
                        'status' => 'Status Pernikahan',
                        'jml_ank' => 'Jumlah Anak',
                    ];
                @endphp

                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($personalData as $key => $label)
                        <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">
                                :
                                @if ($key === 'tgl_lahir')
                                    {{ \Carbon\Carbon::create($employee->$key)->isoFormat('dddd, D MMMM YYYY') }}
                                @else
                                    {{ $employee->$key }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td colspan="2">
                @php
                    $contactData = [
                        'nama_kd' => 'Nama',
                        'no_kd' => 'No. Telp',
                        'hubungan' => 'Hubungan',
                    ];
                @endphp

                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($contactData as $key => $label)
                        <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $employee->$key }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    <table style="width: 50%">
        <tr>
            <th colspan="2">KELENGKAPAN BERKAS</th>
            <th>FOTO PROFIL</th>
        </tr>
        <tr>
            <td colspan="2">
                @php
                    $documents = [
                        'pp' => 'Foto Profil',
                        'ktp' => 'KTP',
                        'npwp2' => 'NPWP',
                        'kk' => 'KK',
                        'bpjs_ket' => 'BPJS Ketenagakerjaan',
                        'bpjs_kes' => 'BPJS Kesehatan',
                    ];
                @endphp
                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($documents as $key => $label)
                        <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $employee->$key ? 'ada' : 'kosong' }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>

            <td style="text-align: center;">
                @if (Storage::exists('public/foto_profil/' . $employee->pp))
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/foto_profil/' . $employee->pp)) }}"
                        style="width: 150px; max-width:150px; height: auto;">
                @else
                    <p>Foto tidak ditemukan.</p>
                @endif
            </td>
        </tr>
    </table>
    <div style="position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center; font-size: 10px;">
        <p>&copy; {{ date('Y') }} AMS Information System. All rights reserved.</p>
        <p>Generated on: {{ $timestamp }}</p>
    </div>
</body>
