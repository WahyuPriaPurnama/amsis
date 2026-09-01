<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Request Payment {{ $payment->request_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>
    @if ($payment->subsidiary->kop_header)
        <div style="text-align:center; margin-bottom:20px;">
            <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/kop_header/' . $payment->subsidiary->kop_header)) }}"
                style="width: 550px; height: auto;">
        </div>
    @endif


    <table style="width:100%; border-collapse: collapse; margin-top:15px;">
        <thead>
            <tr>
                <th colspan="7" style="text-align:center; height:auto; padding:8px;background-color:lightgray">
                    <h3 style="margin:0;">REQUEST FOR PAYMENT</h3>
                </th>
            </tr>
            <tr>
                <td>No. RFP</td>
                <td colspan="2">{{ $payment->payment_number }}</td>
                <td>Tanggal:</td>
                <td>{{ $payment->date }}</td>
                <td>Divisi:</td>
                <td>{{ $payment->division }}</td>
            </tr>
            <tr style="background-color:lightgray">
                <th style="width:10%;text-align:center">NO.</th>
                <th style="width:50%;">DESKRIPSI</th>
                <th style="width:auto;">JUMLAH</th>
                <th style="width:auto;">SATUAN</th>
                <th style="width:auto;">HARGA SATUAN</th>
                <th>JUMLAH HARGA</th>
                <th>DUE DATE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payment->items as $item)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}.</td>
                    <td>{{ $item->item_name }}</td>
                    <td style="text-align: center">{{ $item->quantity }}</td>
                    <td style="text-align: center">{{ $item->unit }}</td>
                    <td style="text-align: right">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right">{{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->due_date ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:center; font-weight:bold;">Grand Total</td>
                <td style="font-weight:bold; text-align:right">{{ number_format($payment->grand_total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <strong>Note:</strong> {{ $payment->purpose ?? '-' }}


    <table
        style="width:100%; transform:scale(0.8); transform-origin:top left; border:1px solid #000; border-collapse:collapse; text-align:center;">
        <tr>
            <td
                style="width:25%; border:1px solid #000; background-color:lightgray; text-align:center; vertical-align:middle;">
                <strong><i>Request By</i></strong>
            </td>
            <td
                style="width:25%; border:1px solid #000; background-color:lightgray; text-align:center; vertical-align:middle;">
                <strong><i>Approved By</i></strong>
            </td>
            <td colspan="2"
                style="width:50%; border:1px solid #000; background-color:lightgray; text-align:center; vertical-align:middle;">
                <strong><i>Approved By Head Office</i></strong>
            </td>
        </tr>

        {{-- Baris tanda tangan --}}
        <tr>
            <td style="border:1px solid #000; border-bottom:none; text-align:center; vertical-align:middle;">
                @if ($payment->requester?->employee?->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $payment->requester->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
            <td style="border:1px solid #000; border-bottom:none; text-align:center; vertical-align:middle;">
                @if ($payment->plantManager?->employee?->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $payment->plantManager->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
            <td style="border:1px solid #000; border-bottom:none; text-align:center; vertical-align:middle;">
                @if ($payment->bod?->employee?->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $payment->bod->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
            <td style="border:1px solid #000; border-bottom:none; text-align:center; vertical-align:middle;">
                
            </td>
        </tr>

        <tr>
            <td
                style="border:1px solid #000; border-top:none; border-bottom:none; text-align:center; vertical-align:middle;">
                {{ $payment->requester->name ?? '-' }} <br>
                <small>{{ $payment->created_at}}<br>Maker</small>
            </td>
            <td
                style="border:1px solid #000; border-top:none; border-bottom:none; text-align:center; vertical-align:middle;">
                {{ $payment->plantManager->name ?? '-' }} <br>
                <small>{{ $payment->approved_by_manager_at}}<br>Direktur</small>
            </td>
            <td
                style="border:1px solid #000; border-top:none; border-bottom:none; text-align:center; vertical-align:middle;">
                {{ $payment->bod->name ?? '-' }} <br>
                <small>{{ $payment->approved_by_bod_at  }}<br>Direktur Operasional</small>
            </td>
            <td
                style="border:1px solid #000; border-top:none; border-bottom:none; text-align:center; vertical-align:middle;">
                Ahmad Musyafak <br>
                <small>CEO</small>
            </td>
        </tr>
    </table>

    @if ($payment->subsidiary->kop_footer)
        <div style="position: absolute; bottom: 30px; left: 0; width: 100%; text-align: center;">
            <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/kop_footer/' . $payment->subsidiary->kop_footer)) }}"
                style="width: 650px; height: auto;">
        </div>
    @endif

    <div style="position: fixed; bottom: 0; left: 0; width: 100%; text-align: center; font-size: 10px;">
        <p>&copy; {{ date('Y') }} AMS Information System. All rights reserved. Generated on:
            {{ $timestamp }}
        </p>
    </div>
</body>

</html>
