@component('mail::message')

Please follow the link below to reset your password:

@component('mail::button', ['url' => route('password.reset'), 'color' => 'success'])
Reset Password using token {{ $token }}
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent
