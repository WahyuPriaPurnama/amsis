<head>
    <title>Slip Gaji {{ $slips->first()->nama }}</title>
    <style>
        .table-container {
            width: 33%;
            height: 33%;
            position: relative;
        }


        table,
        th,
        td {
            width: 100%;
            font-size: 90%;
        }

        tr:nth-child(even) {
            background-color: #e9e9e9;
        }
    </style>
</head>

<body>
    <div class="table-container">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/logo/bofi.png'))) }}"
            style="width: 100%;max-width:100%; height: auto;">
        <br>
        @php
            $slip = $slips->first();
        @endphp
        <table>
            <tr>
                <td style="text-align:left">{{ $slip->harian->nama }}</td>
                <td style="text-align:right">{{ $slip->harian->departemen }}</td>
            </tr>
            <tr>
                <td style="text-align:left">Periode:</td>
                <td style="text-align:right">{{ \Carbon\Carbon::parse($start)->isoFormat('DD/MM/YY') }} -
                    {{ \Carbon\Carbon::parse($end)->isoFormat('DD/MM/YY') }}</td>
            </tr>
        </table>


        <hr>
        <table>
            <tr>
                <th style="width: 80px">TGL</th>
                <th style="width: 50px">JAM</th>
                <th>UPAH</th>
            </tr>
            @foreach ($slips as $slip)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($slip->tgl)->isoFormat('ddd, DD/MM/YY') }}
                    </td>
                    <td style="text-align: center">
                        @if ($slip->dk == null)
                            -
                        @else
                            {{ $slip->dk }}
                        @endif
                    </td>
                    <td style="text-align: right">
                        Rp. {{ number_format($slip->tgaji, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            <tr style="font-weight: bold">
                <td>Total Gaji:</td>
                <td></td>
                <td style="text-align: right">Rp. {{ number_format($totalGaji, 0, ',', '.') }}</td>
            </tr>
        </table>
        <hr>
    </div>

</body>
