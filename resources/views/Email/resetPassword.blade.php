@component('mail::message')
# Reset Password

Reset or change your password.

{{ $code }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
