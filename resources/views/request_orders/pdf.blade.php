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

    <h3 style="text-align:center">Request Order</h3>
    <table style="width:100%; border:none; margin-top:10px;">
        <tr>
            <td style="border:none; width:20%;">
                <strong>Divisi:</strong> {{ $order->division }}
            </td>
            <td style="border:none; width:20%;">
                <strong>Tanggal:</strong> {{ $order->request_date }}
            </td>
            <td style="border:none; width:20%;">
                <strong>Nomor RO:</strong> {{ $order->request_number }}
            </td>

        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Tujuan:</strong> {{ $order->purpose }}

    <table style="margin-top:20px; width:100%; border:none; text-align:center;">
        <tr>
            <td style="width:33%; border:none;text-align:center">
                <strong>Dibuat Oleh:</strong>
            </td>
            <td style="width:33%; border:none;text-align:center">
                <strong>Kepala Divisi:</strong>
            </td>
            <td style="width:34%; border:none;text-align:center">
                <strong>Plant Mgr / BOD:</strong>
            </td>
        </tr>
        <tr>
            <td style="border:none; text-align:center">
                @if ($order->requester && $order->requester->employee && $order->requester->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->requester->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @else
                    <div style="width:100px; height:50px; border-bottom:1px solid #000; margin:0 auto;">
                        <small>Belum ditandatangani</small>
                    </div>
                @endif
            </td>

            <td style="border:none; text-align:center">
                @if ($order->divHead && $order->divHead->employee && $order->divHead->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->divHead->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @else
                    <div style="width:100px; height:50px; border-bottom:1px solid #000; margin:0 auto;">
                        <small>Belum ditandatangani</small>
                    </div>
                @endif
            </td>

            <td style="border:none; text-align:center">
                @if ($order->manager && $order->manager->employee && $order->manager->employee->ttd)
                    <img src="data:image/png;base64,{{ base64_encode(Storage::get('public/ttd/' . $order->manager->employee->ttd)) }}"
                        style="width:100px; height:auto;">
                @else
                    <div style="width:100px; height:50px; border-bottom:1px solid #000; margin:0 auto;">
                        <small>Belum ditandatangani</small>
                    </div>
                @endif
            </td>
        </tr>
        <tr>
            <td style="border:none;text-align:center">
                {{ $order->user->name ?? '-' }}
            </td>
            <td style="border:none;text-align:center">
                {{ $order->divHead->name ?? '-' }}
            </td>
            <td style="border:none;text-align:center">
                {{ $order->manager->name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td style="border:none;text-align:center">
                <small>{{ $order->created_at }}</small>
            </td>
            <td style="border:none;text-align:center">
                <small>
                    {{ $order->approved_by_divhead_at }}
                </small>
            </td>
            <td style="border:none;text-align:center">
                <small>
                    {{ $order->approved_by_manager_at }}
                </small>
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
        <p>&copy; {{ date('Y') }} AMS Information System. All rights reserved. Generated on: {{ $timestamp }}
        </p>
    </div>
</body>

</html>
