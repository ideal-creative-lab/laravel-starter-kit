<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\AccountConfirmationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    protected $confirmationService;
    public $name, $email, $password;

    /**
     * Register component boot.
     *
     * @param AccountConfirmationService $confirmationService
     */
    public function boot(AccountConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }


    public function register()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'confirmation_token' => Str::random(64),
        ]);

        $this->confirmationService->sendConfirmationEmail($user);

        session()->flash('success', 'Registration successful!');
    }

    public function render()
    {
        return view('livewire.register');
    }
}
