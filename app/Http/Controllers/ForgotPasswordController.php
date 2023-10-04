<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordResetUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Services\PasswordResetService;

class ForgotPasswordController extends Controller
{
    /**
     * The PasswordResetService instance.
     *
     * @var \App\Services\PasswordResetService
     */
    protected $passwordResetService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\PasswordResetService  $passwordResetService
     * @return void
     */
    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Submit the form to request password reset.
     *
     * @param  \App\Http\Requests\PasswordResetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function forgetPassword(PasswordResetRequest $request)
    {
        $this->passwordResetService->sendPasswordResetEmail($request->email);

        return response('Link sent', '400');
    }

    /**
     * Submit the form to update/reset password.
     *
     * @param  \App\Http\Requests\PasswordUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(PasswordUpdateRequest $request)
    {
        $result = $this->passwordResetService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if ($result) {
            return response('Password successfully changed', '200');
        } else {
            return response('Error, password has not been changed', '400');
        }
    }
}
