<head>
    <title>Data Aset</title>
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
    </style>
</head>

<body>
    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td style="text-align: left; vertical-align: middle; border: none;font-size: 100%;">
                <h1>DATA ASET</h1>
            </td>
        </tr>
    </table>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>NAMA PLANT</th>
                <th>KODE</th>
                <th>NAMA</th>
                <th>KONDISI</th>
                <th>KATEGORI</th>
                <th>LOKASI</th>
                <th>EDITOR</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $asset)
                <tr>
                    <td>{{ $asset->subsidiary->name ?? '-' }}</td>
                    <td>{{ $asset->code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->condition }}</td>
                    <td>{{ $asset->category }}</td>
                    <td>{{ $asset->location }}</td>
                    <td>{{ $asset->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
