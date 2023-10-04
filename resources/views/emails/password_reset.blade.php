@component('mail::message')

Please follow the link below to reset your password:

Format: route('password.reset')?token={{ $token }}&email=your@email.com&password=your_new_password

Best regards,<br>
{{ config('app.name') }}
@endcomponent
