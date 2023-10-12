<?php

namespace App\Livewire;

use App\Services\PasswordResetService;
use Livewire\Component;

class PasswordForget extends Component
{
    protected $passwordResetService;
    public $email;

    /**
     * Register component boot.
     *
     * @param PasswordResetService $passwordResetService
     */
    public function boot(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function sendPasswordResetLink()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $this->passwordResetService->sendPasswordResetEmail($this->email);

        session()->flash('success', 'Password reset link sent! Check your email.');
    }

    public function render()
    {
        return view('livewire.password-forget');
    }
}
