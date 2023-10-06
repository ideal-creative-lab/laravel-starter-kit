<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->token = $data['token'];
    }

    public function build()
    {
        return $this->markdown('emails.password_reset', [
            'token' => $this->token
        ])
            ->subject('Reset password  ' . config('app.name'));
    }
}
