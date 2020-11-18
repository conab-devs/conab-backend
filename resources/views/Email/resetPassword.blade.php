@component('mail::message')
# Recuperação de Senha

Recupere sua senha utilizando o código abaixo.

{{ $code }}

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
