<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->confirmationUrl = $data['confirmationUrl'];
    }

    public function build()
    {
        return $this->markdown('emails.account_confirmation', [
            'confirmationUrl' => $this->confirmationUrl
        ])
            ->subject('Submit your registration on site ' . config('app.name'));
    }
}
