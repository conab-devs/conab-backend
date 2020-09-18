@component('mail::message')
# Reset Password

Reset or change your password.

@component('mail::button', ['url' => $url])
Change Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent