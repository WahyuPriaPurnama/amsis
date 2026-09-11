<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $vendor;
    public $user;
    public $plainPassword; // Tambahkan properti ini

    public function __construct($vendor, $user, $plainPassword)
    {
        $this->vendor = $vendor;
        $this->user = $user;
        $this->plainPassword = $plainPassword; 
    }

    public function build()
    {
        return $this->subject('Selamat! Pendaftaran Vendor Anda Disetujui - AMSIS')
                    ->view('emails.vendor-approved');
    }
}