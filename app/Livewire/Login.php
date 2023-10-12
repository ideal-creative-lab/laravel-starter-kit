<?php

namespace App\Livewire;

use Livewire\Component;

class Login extends Component
{
    public $email, $password, $remember, $error;

    public function login()
    {
        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            return redirect()->route('home');
        } else {
            $this->error = 'Invalid credentials.';
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
