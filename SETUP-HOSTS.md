# Configuración de Hosts para SumAxia

Para acceder a la aplicación usando el pseudónimo `sumaxia.local`, necesitas agregar una entrada en tu archivo hosts del sistema.

## Windows

1. Abre el **Bloc de notas** como **Administrador**
2. Abre el archivo: `C:\Windows\System32\drivers\etc\hosts`
3. Agrega la siguiente línea al final del archivo:
   ```
   127.0.0.1    sumaxia.local
   ```
4. Guarda el archivo

## macOS/Linux

1. Abre la terminal
2. Ejecuta: `sudo nano /etc/hosts`
3. Agrega la siguiente línea al final del archivo:
   ```
   127.0.0.1    sumaxia.local
   ```
4. Guarda el archivo (Ctrl+X, luego Y, luego Enter en nano)

## Verificación

Después de configurar el archivo hosts:

1. Levanta los contenedores: `docker compose up -d`
2. Accede a la aplicación en: `http://sumaxia.local:8000`
3. También funcionará con: `http://localhost:8000` (como respaldo)

### Paso prioritario en Windows (si no resuelve)

Si ves `DNS_PROBE_FINISHED_NXDOMAIN` al abrir `sumaxia.local:8000`:

- Ejecuta `ipconfig /flushdns` en PowerShell o CMD
- En Chrome/Edge, ve a `chrome://net-internals/#dns` o `edge://net-internals/#dns` y pulsa "Clear host cache"
- Desactiva temporalmente "Usar DNS seguro (DoH)" en el navegador: Ajustes → Privacidad y seguridad → DNS seguro → Off
- Verifica con `ping sumaxia.local` que responde `127.0.0.1`

### Nota sobre cookies y 419

- Si `SESSION_DOMAIN=sumaxia.local` está configurado (por defecto en `.env.docker`), entrar por `localhost:8000` puede causar `419 Page Expired`.
- Usa siempre `http://sumaxia.local:8000`. Si necesitas usar `localhost` como respaldo, deja `SESSION_DOMAIN` vacío temporalmente y limpia cachés (`config:clear`, `cache:clear`, `route:clear`, `view:clear`).

mirar la imagen como_debe_salir.png de acá

## Servicios Disponibles

- **Aplicación principal**: `http://sumaxia.local:8000`
- **phpMyAdmin**: `http://localhost:8080`
- **Mailpit** (opcional): `http://localhost:8025`
- **Correo por defecto (SMTP)**: Mailtrap (`sandbox.smtp.mailtrap.io`)
- **Vite Dev Server**: `http://localhost:5173`

## Notas

- El puerto 8000 siempre se expone automáticamente al crear la imagen
- La aplicación corre bajo el pseudónimo `sumaxia.local` en lugar de localhost
- Nginx está configurado para responder tanto a `sumaxia.local` como a `localhost`