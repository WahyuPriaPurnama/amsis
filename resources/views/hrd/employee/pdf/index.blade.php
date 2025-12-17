<head>
    <title>Data Karyawan</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            font-size: 50%;
            border-collapse: collapse;
        }

        * {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        table {
            width: 100%;
        }

        a {
            text-decoration: black;
        }
    </style>
</head>

<body>
    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td style="text-align: left; vertical-align: middle; border: none;font-size: 100%;">
                <h1>DATA KARYAWAN</h1>
            </td>
            <td style="text-align: right; vertical-align: middle; border: none;">
                <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/logo/' . $subsidiary->logo)) }}"
                    style="width: 150px; height: auto;">
            </td>
        </tr>
    </table>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="10">ORGANISASI</th>
                <th colspan="10">BIODATA</th>
                <th colspan="3">KONTAK DARURAT</th>
            </tr>
            <tr>
                <th>NO</th>
                <th>NIP</th>
                <th>NAMA</th>
                <th>NIK</th>
                <th>PERUSAHAAN</th>
                <th>DIVISI</th>
                <th>DEPARTEMEN</th>
                <th>SEKSI</th>
                <th>JABATAN</th>
                <th>STATUS</th>

                <th>LHR</th>
                <th>TGL</th>
                <th>USIA</th>
                <th>L/P</th>
                <th>ALAMAT</th>
                <th>TELP</th>
                <th>EMAIL</th>
                <th>PEND.</th>
                <th>JURUSAN</th>
                <th>LULUS</th>

                <th>KD NAMA</th>
                <th>KD TELP</th>
                <th>HUB.</th>
            </tr>
        </thead>
        <tbody>
            {{ $i = 1 }}
            @forelse ($employees as $employee)
                <tr @if (Carbon\Carbon::now()->diffInDays($employee->akhir_kontrak) <= 45 && $employee->status_peg == 'PKWT') style="background-color: #ffeeba;" @endif>
                    <td>{{ $i++ }}</td>
                    <td>{{ $employee->nip }}</td>
                    <td>{{ $employee->nama }}</td>
                    <td>{{ $employee->nik }}</td>
                    <td>{{ $employee->subsidiary->name }}</td>
                    <td>{{ $employee->divisi }}</td>
                    <td>{{ $employee->departemen }}</td>
                    <td>{{ $employee->seksi }}</td>
                    <td>{{ $employee->posisi }}</td>
                    <td>{{ $employee->status_peg }}</td>

                    <td>{{ $employee->tmpt_lahir }}</td>
                    <td>{{ $employee->tgl_lahir }}</td>
                    <td>{{ Carbon\Carbon::parse($employee->tgl_lahir)->age }}</td>
                    <td>{{ $employee->jenis_kelamin }}</td>
                    <td>{{ $employee->alamat }}</td>
                    <td>{{ $employee->no_telp }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->pend_trkhr }}</td>
                    <td>{{ $employee->jurusan }}</td>
                    <td>{{ $employee->thn_lulus }}</td>

                    <td>{{ $employee->nama_kd }}</td>
                    <td>{{ $employee->no_kd }}</td>
                    <td>{{ $employee->hubungan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="23" class="text-center">Tidak ada data...</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center; font-size: 10px;">
        <p>&copy; {{ date('Y') }} AMS Information System. All rights reserved.</p>
        <p>Generated on: {{ $timestamp }}</p>
    </div>
</body>
