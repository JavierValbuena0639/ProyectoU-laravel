<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de verificación</title>
</head>
<body style="font-family: Arial, sans-serif; color:#111;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <h1 style="font-size:18px;margin:0 0 12px;">Hola {{ $user->name ?? $user->email }},</h1>
        <p style="margin:0 0 12px;">Tu código de verificación es:</p>
        <p style="font-size:24px;font-weight:bold;letter-spacing:2px;margin:8px 0;">{{ $code }}</p>
        <p style="margin:12px 0;">Ingresa este código en la aplicación para verificar tu correo.</p>
        <p style="margin:12px 0;">Si tú no solicitaste este código, puedes ignorar este mensaje.</p>
        <hr style="margin:20px 0;border:none;border-top:1px solid #e5e7eb;" />
        <p style="font-size:12px;color:#6b7280;">Este correo fue generado automáticamente.</p>
    </div>
    </body>
</html>