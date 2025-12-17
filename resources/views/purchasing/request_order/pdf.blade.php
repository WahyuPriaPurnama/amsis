<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Request Order {{ $order->request_number }}</title>
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
    @if ($order->subsidiary->kop_header)
        <div style="text-align:center; margin-bottom:20px;">
            <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/kop_header/' . $order->subsidiary->kop_header)) }}"
                style="width: 550px; height: auto;">
        </div>
    @endif


    <table style="width:100%; border-collapse: collapse; margin-top:15px;">
        <thead>
            <tr>
                <th colspan="5" style="text-align:center; height:auto; padding:8px;background-color:lightgray">
                    <h3 style="margin:0;">REQUEST ORDER</h3>
                </th>
            </tr>
            <tr>
                <td rowspan="2">Divisi:</td>
                <th rowspan="2">{{ $order->division }}</th>
                <td>Tanggal:</td>
                <th colspan="2">{{ $order->request_date }}</th>
            </tr>
            <tr>
                <td>Nomor RO:</td>
                <th colspan="2">{{ $order->request_number }}</th>
            </tr>
            <tr style="background-color:lightgray">
                <th style="width:5%;text-align:center">NO.</th>
                <th style="width:50%;">DESKRIPSI</th>
                <th style="width:auto;">JUMLAH</th>
                <th style="width:auto;">SATUAN</th>
                <th style="width:auto;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}.</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->remark ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Note:</strong> {{ $order->purpose ?? '-' }}


    <table
        style=" width:80%; transform:scale(0.8);transform-origin:top left; border:1px solid #000; border-collapse:collapse; text-align:center;">
        <tr>
            <td rowspan="2"
                style="width:25%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>Request By</i></strong>
            </td>
            <td colspan="3"
                style="width:25%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>Approved By Plant</i></strong>
            </td>
        </tr>
        <tr>
            <td
                style="width:25%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>Head of Division</i></strong>
            </td>
            <td
                style="width:25%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>Plant Manager</i></strong>
            </td>
            <td
                style="width:25%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>BOD</i></strong>
            </td>
        </tr>

        {{-- Baris tanda tangan --}}
        <tr>
            <td style="border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
                @if ($order->requester && $order->requester->employee && $order->requester->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->requester->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
            <td style="border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
                @if ($order->divHead && $order->divHead->employee && $order->divHead->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->divHead->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
            <td style="border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
                @if ($order->plantManager && $order->plantManager->employee && $order->plantManager->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->plantManager->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
            <td style="border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
                @if ($order->bod && $order->bod->employee && $order->bod->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->bod->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @endif
            </td>
        </tr>

        {{-- Baris nama + timestamp --}}
        <tr>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none; text-align:center">
                {{ $order->user->name ?? '-' }} <br>
                <small>{{ $order->created_at }}</small>
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                {{ $order->divHead->name ?? '-' }} <br>
                <small>{{ $order->approved_by_divhead_at }}</small>
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                {{ $order->plantManager->name ?? '-' }} <br>
                <small>{{ $order->approved_by_manager_at }}</small>
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                {{ $order->bod->name ?? '-' }} <br>
                <small>{{ $order->approved_by_bod_at }}</small>
            </td>
        </tr>
    </table>
    <table
        style="width:100%;transform:scale(0.8);transform-origin:top left; border:1px solid #000; border-collapse:collapse; text-align:center;">
        <tr>
            <td
                style="width:20%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>Received By</i></strong>
            </td>
            <td colspan="4"
                style="width:20%; border-left:1px solid #000; border-right:1px solid #000;text-align:center;background-color:lightgray">
                <strong><i>Approve by Head Office</i></strong>
            </td>
        </tr>


        {{-- Baris tanda tangan --}}
        <tr>
            <td style="height:100px;border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
            </td>
            <td style="height:100px;border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
            </td>
            <td style="height:100px;border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
            </td>
            <td style="height:100px;border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
            </td>
            <td style="height:100px;border-left:1px solid #000; border-right:1px solid #000;border-bottom:none">
            </td>
        </tr>

        {{-- Baris nama + timestamp --}}
        <tr>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none; text-align:center">
                Purchasing
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                M. Bobsaid<br>
                <small>Purchasing Manager</small>
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                Mahfudi<br>
                <small>Operations Director</small>
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                Sestri Mahanani<br>
                <small>Finance Director</small>
            </td>
            <td
                style="border-left:1px solid #000; border-right:1px solid #000; border-top:none; border-bottom:none;text-align:center">
                Ahmad Musyafak<br>
                <small>Director</small>
            </td>
        </tr>
    </table>
    @if ($order->subsidiary->kop_footer)
        <div style="position: absolute; bottom: 30px; left: 0; width: 100%; text-align: center;">
            <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/subsidiary/kop_footer/' . $order->subsidiary->kop_footer)) }}"
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
