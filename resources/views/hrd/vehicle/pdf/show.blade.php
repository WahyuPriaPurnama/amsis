<!DOCTYPE html>

<head>
    <title>Data {{ $vehicle->jenis_kendaraan }}</title>
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
                <h2>{{ $vehicle->jenis_kendaraan }}</h2>
                <h4>{{ $vehicle->nopol }}</h4>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/logo/' . $subsidiary->logo)) }}"
                    style="width: 150px; height: auto;">
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th colspan="2">RINGKASAN</th>
            <th colspan="2">KELENGKAPAN</th>
            <th colspan="2">JATUH TEMPO</th>
        </tr>
        <tr>
            <td colspan="2">
                @php
                    $fields = [
                        'jenis_kendaraan' => 'Kendaraan',
                        'tgl_perolehan' => 'Tanggal Perolehan',
                        'pengguna' => 'Pengguna',
                        'nama_warna' => 'Perusahaan',
                        'nama_warna' => 'Warna',
                        'warna' => 'Kode Warna',
                        'tahun' => 'Tahun Produksi',
                        'atas_nama' => 'Atas Nama',
                        'kondisi' => 'Kondisi',
                        'tgl_service' => 'Tgl Service',
                        'km_akhir' => 'KM Akhir',
                    ];
                @endphp

                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($fields as $key => $label)
                        <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $vehicle->$key }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td colspan="2">
                @php
                    $fields2 = [
                        'no_rangka' => 'No. Rangka',
                        'no_bpkb' => 'No. BPKB',
                        'no_mesin' => 'No. Mesin',
                        'j_asuransi' => 'Asuransi',
                        'p_asuransi' => 'Polis',
                        'no_polis' => 'No. Polis',
                    ];
                @endphp
                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($fields2 as $key => $label)
                       <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $vehicle->$key }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td colspan="2">
                @php
                    $fields3 = [
                        'stnk' => 'STNK',
                        'pajak' => 'Pajak',
                        'kir' => 'KIR',
                    ];
                @endphp
                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($fields3 as $key => $label)
                       <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $vehicle->$key }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    <table style="width: 50%">
        <tr>
            <th colspan="2">KELENGKAPAN BERKAS</th>
            <th>FOTO</th>
        </tr>
        <tr>
            <td colspan="2">
                @php
                    $documents = [
                        'foto' => 'Foto',
                        'f_stnk' => 'STNK',
                        'f_pajak' => 'PAJAK',
                        'f_kir' => 'KIR',
                        'qr' => 'QR Code',
                        'f_polis' => 'Polis',
                    ];
                @endphp
                <table style="width: 100%; font-size: 14px; line-height: 1.2;">
                    @foreach ($documents as $key => $label)
                        <tr>
                            <td style="padding: 2px 6px; white-space: nowrap;">{{ $label }}</td>
                            <td style="padding: 2px 6px;">: {{ $vehicle->$key ? 'ada' : 'kosong' }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>

            <td style="text-align: center;">
                @if (Storage::exists('public/vehicles/foto/' . $vehicle->foto))
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/vehicles/foto/' . $vehicle->foto)) }}"
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
