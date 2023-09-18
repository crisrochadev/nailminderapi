@component('mail::message')
<h1>Recebemos sua solicitação para redefinir a senha da sua conta</h1>
<p>Você pode usar o seguinte código para recuperar sua conta:</p>

@component('mail::panel')
{{ $code }}
@endcomponent

<p>A duração permitida do código é de uma hora a partir do momento em que a mensagem foi enviada</p>
@endcomponent