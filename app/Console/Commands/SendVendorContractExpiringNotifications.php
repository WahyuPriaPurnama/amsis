<?php

namespace App\Console\Commands;

use App\Models\Purchasing\Vendor;
use App\Mail\VendorContractExpiringMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendVendorContractExpiringNotifications extends Command
{
    protected $signature = 'vendor:notify-expiring-contracts';
    protected $description = 'Kirim email notifikasi kontrak yang akan habis dalam 7 hari ke vendor & purchasing';

    public function handle(): void
    {

        $expiringVendors = Vendor::approved()
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', now()->addDays(7)->toDateString())
            ->get();

        $purchasingEmail = config('mail.purchasing_admin_email', 'purchasing@company.com');

        foreach ($expiringVendors as $vendor) {
     
            if (!empty($vendor->pic_email)) {
                Mail::to($vendor->pic_email)
                    ->send(new VendorContractExpiringMail($vendor, true));
            }

            Mail::to($purchasingEmail)
                ->send(new VendorContractExpiringMail($vendor, false));
        }

        $this->info("Berhasil mengirim notifikasi untuk {$expiringVendors->count()} vendor.");
    }
}