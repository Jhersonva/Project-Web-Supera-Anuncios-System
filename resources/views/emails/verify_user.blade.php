@component('mail::message')

# Hola {{ $user->full_name ?? 'Usuario' }}

Gracias por registrarte en **Supera Anuncios**.

Para activar tu cuenta y comenzar a publicar anuncios, solo debes verificar tu correo haciendo clic en el siguiente botón:

@component('mail::button', ['url' => $url])
Verificar mi cuenta
@endcomponent

⏰ **Este enlace expirará el {{ $expires_at->format('d/m/Y H:i') }}**

Si no creaste esta cuenta, puedes ignorar este mensaje sin problema.

Saludos,  
**Equipo de Supera Anuncios**

@endcomponent