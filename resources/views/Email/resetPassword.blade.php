@component('mail::message')
# Reset Password

Reset or change your password.

@component('mail::button', ['code' => $code])
Reset Code
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
