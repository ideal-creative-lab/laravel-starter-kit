@component('mail::message')

Please make form on your site to reset your password using token {{ $token }}

Request example {{ route('password.reset') . '?token=' . $token . '&email=admin@admin.com&password=12345678' }}

Best regards,<br>
{{ config('app.name') }}

@endcomponent
