<?php

namespace App\Mail;

use App\Models\Purchasing\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorContractExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public Vendor $vendor;
    public bool $isForVendor;

    public function __construct(Vendor $vendor, bool $isForVendor = false)
    {
        $this->vendor = $vendor;
        $this->isForVendor = $isForVendor;
    }

    public function build()
    {
        $subject = $this->isForVendor
            ? 'Pemberitahuan Masa Berlaku Kontrak - ' . config('app.name')
            : 'Peringatan Kontrak Vendor Segera Berakhir: ' . $this->vendor->company_name;

        return $this->subject($subject)
            ->markdown('emails.vendors.contract-expiring');
    }
}
