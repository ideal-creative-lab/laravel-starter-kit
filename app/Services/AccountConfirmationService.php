<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountConfirmationMail;

class AccountConfirmationService
{
    public function sendConfirmationEmail(User $user)
    {
        $confirmationUrl = route('confirm', ['token' => $user->confirmation_token]);
        Mail::to($user->email)->send(new AccountConfirmationMail(['confirmationUrl' => $confirmationUrl]));
    }

    public function confirmAccount(User $user)
    {
        $user->update([
            'email_verified_at' => now()
        ]);
    }
}
