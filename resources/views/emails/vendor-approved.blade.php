<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pendaftaran Disetujui</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Halo, {{ $vendor->pic_name }} ({{ $vendor->company_name }})</h2>
    <p>Selamat! Pendaftaran Anda sebagai mitra vendor telah <strong>disetujui</strong> oleh tim Procurement.</p>

    <p>Sistem telah membuatkan akun untuk Anda dengan detail akses berikut:</p>
    <ul>
        <li><strong>Email Login:</strong> {{ $user->email }}</li>
        <li><strong>Password Sementara:</strong> <code style="background: #f1f1f1; padding: 2px 6px; font-weight: bold;">{{ $plainPassword }}</code></li>
    </ul>

    <p>Silakan gunakan kredensial di atas untuk login ke Portal Vendor dan mengakses Purchase Order (PO).</p>

    <p>Salam,<br><strong>Tim Procurement AMSIS</strong></p>
</body>

</html>