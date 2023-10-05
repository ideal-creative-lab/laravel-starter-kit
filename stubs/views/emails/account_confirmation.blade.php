@component('mail::message')
# Thank you for registering!

Please follow the link below to complete your registration:

@component('mail::button', ['url' => $confirmationUrl, 'color' => 'success'])
Complete Registration
@endcomponent

If you didn't register on our website, please disregard this email.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
