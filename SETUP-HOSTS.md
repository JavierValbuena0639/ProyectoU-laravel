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