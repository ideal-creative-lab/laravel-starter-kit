<?php

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Send the password reset email with a token to the given email address.
     *
     * @param  string  $email
     * @return void
     */
    public function sendPasswordResetEmail($email)
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($email)->send(new PasswordResetMail(['token' => $token]));
    }

    /**
     * Reset the password for the user with the given email and token.
     *
     * @param  string  $email
     * @param  string  $token
     * @param  string  $password
     * @return bool True if the password was reset successfully, false otherwise.
     */
    public function resetPassword($email, $token, $password)
    {
        $updatePassword = DB::table('password_reset_tokens')
            ->where(['email' => $email, 'token' => $token])
            ->first();

        if (!$updatePassword) {
            return false;
        }

        User::where('email', $email)
            ->update(['password' => Hash::make($password)]);

        DB::table('password_reset_tokens')->where(['email' => $email])->delete();

        return true;
    }
}
