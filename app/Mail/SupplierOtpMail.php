<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public $supplierName;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $supplierName = null)
    {
        $this->otp = $otp;
        $this->supplierName = $supplierName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('رمز التحقق - منصة فعالياتك')
            ->view('emails.supplier-otp');
    }
}
