@component('mail::message')

Please follow the link below to reset your password:

@component('mail::button', ['url' => route('password.reset.form', $token), 'color' => 'success'])
    Reset Password
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent
