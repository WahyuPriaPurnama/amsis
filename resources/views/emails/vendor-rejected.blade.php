<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Status Pendaftaran Vendor</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Halo, {{ $vendor->pic_name }} ({{ $vendor->company_name }})</h2>
    <p>Mohon maaf, pengajuan pendaftaran vendor Anda belum dapat disetujui pada saat ini karena alasan berikut:</p>

    <blockquote style="background: #f9f9f9; padding: 10px; border-left: 4px solid #dc3545; margin: 20px 0;">
        {{ $vendor->rejection_reason }}
    </blockquote>

    <p>Silakan melakukan penyesuaian atau perbaikan dokumen sesuai catatan di atas, lalu lakukan pendaftaran kembali.</p>

    <p>Salam,<br><strong>Tim Procurement AMS Group</strong></p>
</body>

</html>