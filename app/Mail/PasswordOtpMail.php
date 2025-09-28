<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $token;

    public function __construct($otp, $token = null)
    {
        $this->otp = $otp;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Your password reset OTP')->view('emails.password-otp')->with(['otp' => $this->otp, 'token' => $this->token]);
    }
}
