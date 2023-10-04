<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\AccountConfirmationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $confirmationService;

    /**
     * AuthController constructor.
     *
     * @param AccountConfirmationService $confirmationService
     */
    public function __construct(AccountConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

    /**
     * Handle the user login request.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');
        $user = auth()->getProvider()->retrieveByCredentials($credentials);
        if (!empty($user) && !$user->email_verified_at) {
            return response('User does not exist or is not verified', '401');
        }

        if (auth()->attempt($credentials, $remember)) {
            return response('Successfully log in', '200');
        } else {
            return response('Invalid credentials', '401');
        }
    }

    /**
     * Handle the user registration request.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'confirmation_token' => Str::random(64),
        ]);

        $this->confirmationService->sendConfirmationEmail($user);

        return response('User created, email sent successfully', '200');
    }

    /**
     * Log the user out.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->logout();
        return response('Logout successfully', '200');
    }

    /**
     * Confirm the user account with the given token.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $user = User::where('confirmation_token', $request->token)->firstOrFail();

        $this->confirmationService->confirmAccount($user);

        auth()->login($user);

        return response('Successfully confirmed', '200');
    }
}
