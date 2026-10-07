@component('mail::message')
@if($isForVendor)
# Yth. {{ $vendor->company_name }}

Terima kasih atas kerja sama yang terjalin selama ini.

Kami ingin memberitahukan bahwa perjanjian kontrak kerja sama Anda akan berakhir dalam **7 hari** lagi, yaitu pada **{{ $vendor->contract_end_date?->format('d F Y') }}**.

Mohon untuk menghubungi tim Purchasing kami untuk proses evaluasi dan perpanjangan kontrak.

@else
# Peringatan Kontrak Vendor (7 Hari Lagi)

Halo Tim Purchasing,

Kontrak kerja sama dengan vendor berikut akan segera berakhir:

- **Nama Vendor:** {{ $vendor->company_name }}
- **PIC:** {{ $vendor->pic_name }} ({{ $vendor->email }})
- **No. Kontrak:** {{ $vendor->contract_number ?? '-' }}
- **Tanggal Berakhir:** {{ $vendor->contract_end_date?->format('d F Y') }}

Segera tindak lanjuti perpanjangan atau pembaruan kontrak ini.
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent