<head>
    <title>Data Kendaraan</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            font-size: 80%;
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

        #warning {
            background-color: yellow;
        }
    </style>
</head>

<body>
    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td style="text-align: left; vertical-align: middle; border: none;font-size: 100%;">
                <h1>DATA KENDARAAN</h1>
            </td>
            <td style="text-align: right; vertical-align: middle; border: none;">
                <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/logo/' . $subsidiary->logo)) }}"
                    style="width: 150px; height: auto;">
            </td>
        </tr>
    </table>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th>NO</th>
            <th>KENDARAAN</th>
            <th>KATEGORI</th>
            <th>PLANT</th>
            <th>NOPOL</th>
            <th>SERVICE</th>
            <th>KM</th>
            <th>STNK</th>
            <th>PAJAK</th>
            <th>KIR</th>
            <th>ASURANSI</th>
            <th>KONDISI</th>
        </tr>

        {{ $i = 1 }}
        @forelse ($vehicles as $vehicle)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $vehicle->jenis_kendaraan }}</td>
                <td>{{ $vehicle->kategori }}</td>
                <td>{{ $vehicle->subsidiary->name }}</td>
                <td>{{ $vehicle->nopol }}</td>
                <td>{{ $vehicle->tgl_service }}</td>
                <td>{{ $vehicle->km_akhir }}</td>
                <td>{{ $vehicle->stnk }}</td>
                <td>{{ $vehicle->pajak }}</td>
                <td>{{ $vehicle->kir }}</td>
                <td>{{ $vehicle->jth_tempo }}</td>
                <td>{{ $vehicle->kondisi }}</td>
            </tr>
        @empty
            <td colspan="10" class="text-center">Tidak ada data...</td>
            </tbody>
        @endforelse
    </table>
    <div style="position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center; font-size: 10px;">
        <p>&copy; {{ date('Y') }} AMS Information System. All rights reserved.</p>
        <p>Generated on: {{ $timestamp }}</p>
    </div>
</body>
