# Sistema de Gestión Empresarial Sumaxia

## Descripción

Sumaxia es un sistema integral de gestión empresarial desarrollado en Laravel que incluye módulos para facturación, cotizaciones, contabilidad, nómina y administración de usuarios. El sistema está diseñado para pequeñas y medianas empresas que necesitan una solución completa para gestionar sus operaciones financieras y administrativas.

## Actualizaciones Recientes

- Home `/` reescrito con el mismo layout que `/login`: tarjeta centrada, CTAs, enlaces de `Register` y `Log in`, selector ES/US y footer.
- Internacionalización del contenido del inicio:
  - Nuevos archivos de traducción: `resources/lang/es/welcome.php` y `resources/lang/en/welcome.php`.
  - `resources/views/welcome.blade.php` usa `{{ __('welcome.*') }}` para el párrafo y la lista de características.
  - El botón de registro usa `{{ __('welcome.create_account') }}` (antes `auth.create_admin`).
- En `/login`, el texto "SumAxia" ahora es un enlace que vuelve a `/`.
- Ruta de idioma disponible para todos: `GET /locale/{lang}` (`route('locale.switch')`).
- Internacionalización del Dashboard y Facturación:
  - Dashboard: `resources/views/dashboard.blade.php` actualizado para usar `{{ __('dashboard.*') }}`.
    - Nuevos archivos: `resources/lang/es/dashboard.php` y `resources/lang/en/dashboard.php`.
  - Facturación: `resources/views/invoicing/invoices.blade.php` actualizado para usar `{{ __('invoicing.*') }}`.
    - Nuevos archivos: `resources/lang/es/invoicing.php` y `resources/lang/en/invoicing.php`.
  - El atributo `lang` en `<html>` ahora se establece dinámicamente según `app()->getLocale()`.
- Respaldo actualizado: carpeta `bk` sincronizada con el estado actual del proyecto.

- Soporte interno y mantenimiento de BD:
  - Nuevo rol `soporte_interno` reservado para creadores del sistema (no asignable por administradores).
  - Seeder actualizado: usuario de soporte `javi.valbuena0997@gmail.com` con contraseña `Aaa.12715!`.
  - Página "Admin > Base de Datos" (`/admin/database`) con acciones de respaldo, migraciones, optimización y limpieza de caché.
  - Respaldos almacenados en `storage/app/backups/` con archivos `.zip` (un `.json` por tabla).
  - Toggle de respaldo automático (env `DB_AUTO_BACKUP=true/false`).

- Verificación del sistema:
  - Nueva página "Admin > Verificación" (`/admin/system/verify`) que evalúa extensiones PHP, conexión a BD, cache, permisos de almacenamiento y configuración relevante.
  - Muestra últimos eventos de auditoría desde `storage/logs/audit.log`.
  - Indica si el "primer arranque" fue completado (detecta `storage/app/backups/.boot_init_done`).

### Cómo probar rápidamente
- Cierra servidores previos y ejecuta `php artisan serve --host=127.0.0.1 --port=8000` y abre `http://127.0.0.1:8000/`.
- Alterna el idioma con el selector ES/US; el intro y la lista cambian.
- Ve a `http://127.0.0.1:8000/login` y haz clic en "SumAxia" para volver a `/`.
- Visita `http://127.0.0.1:8000/dashboard` y alterna idioma; verifica que subtítulo, métricas, acciones rápidas y actividad reciente cambian.
- Visita `http://127.0.0.1:8000/invoices` y alterna idioma; verifica breadcrumb, filtros, encabezados, estados, acciones, tarjetas resumen y textos de gráfico.



## Características Principales

### 📊 Dashboard Principal
- Panel de control con métricas clave
- Gráficos de ingresos y actividad
- Resumen de facturas, cotizaciones y empleados
- Acceso rápido a todas las funcionalidades

### 💰 Módulo de Facturación
- Creación y gestión de facturas
- Seguimiento de pagos
- Historial de transacciones
- Generación de reportes

### 📋 Módulo de Cotizaciones
- Creación de cotizaciones personalizadas
- Conversión de cotizaciones a facturas
- Gestión de clientes y productos
- Seguimiento de estados

### 📚 Módulo de Contabilidad
- Plan de cuentas contables
- Registro de asientos contables
- Balances y estados financieros
- Reportes contables

### 👥 Módulo de Nómina
- Gestión de empleados
- Procesamiento de nóminas
- Cálculo de deducciones e impuestos
- Reportes de nómina

### ⚙️ Administración
- Gestión de usuarios y roles
- Configuración del sistema
- Respaldos de base de datos
- Reportes del sistema
 - Verificación del sistema (salud de entorno)
 - Configuración FE (DIAN)

### 🧪 Validaciones y Middleware
- Validación de dominio al crear usuarios: se bloquea el envío si el dominio del correo no coincide con el dominio esperado y se muestra un aviso.
- Conversión automática de entradas a minúsculas: middleware global transforma todos los campos de texto en minúsculas (excluye `password` y `password_confirmation`).
 - Roles: el rol `soporte_interno` no aparece en formularios de creación/edición de usuarios y no puede ser asignado manualmente.

### 📧 Verificación por Correo
- Envío de código de verificación (6 dígitos) al registrar administrador en `/register` y al crear usuarios desde `/admin/users/create`.
- Mailable: `app/Mail/VerificationCodeMail.php` y plantilla: `resources/views/emails/verification-code.blade.php`.
- En creación de usuarios por administrador se valida que el dominio del email coincida con el dominio esperado antes de enviar el código.

## Requisitos del Sistema

### Requisitos Mínimos
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- NPM o Yarn
- MySQL (recomendado) o SQLite (opcional)
- Servidor web (Apache/Nginx) o PHP built-in server

### Extensiones PHP Requeridas
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Instalación Local

### 1. Clonar el Repositorio
```bash
git clone <repository-url>
cd sumaxia
```

### 2. Instalar Dependencias
```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js
npm install

# (Opcional) Instalar librería para PDF avanzado
# Si quieres renders PDF con HTML/CSS completos
composer require dompdf/dompdf
```

### 3. Configurar el Entorno
```bash
# Copiar el archivo de configuración
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 4. Configurar la Base de Datos

#### MySQL (recomendado)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sumaxia
DB_USERNAME=root
DB_PASSWORD=
```
- Crea la base de datos `sumaxia` en tu servidor MySQL y asegúrate de que el usuario tenga permisos.
- Si usas Docker, `DB_HOST` debe ser `mysql` y las credenciales están en `.env.docker`.

Nota: El proyecto ya no utiliza SQLite por defecto para evitar confusiones.

### 5. Ejecutar Migraciones
```bash
php artisan migrate
```

### 6. Compilar Assets
```bash
npm run dev
# o para producción
npm run build
```

### 7. Iniciar el Servidor
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

El sistema estará disponible en `http://127.0.0.1:8000`

### 8. Mantenimiento de BD y Verificación

- Acceso a mantenimiento de BD (restringido a `soporte_interno`):
  - `GET /admin/database` (mostrar)
  - `POST /admin/database/backups/create` (crear respaldo)
  - `GET /admin/database/backups/download/{file}` (descargar respaldo)
  - `POST /admin/database/backups/delete/{file}` (eliminar respaldo)
  - `POST /admin/database/backups/toggle` (activar/desactivar auto backup)
  - `POST /admin/database/migrate` (ejecutar migraciones)
  - `POST /admin/database/rollback` (revertir última migración)
  - `POST /admin/database/optimize` (optimización)
  - `POST /admin/database/cache/clear` (limpiar caches)

- Verificación del sistema (admin o soporte):
  - `GET /admin/system/verify` muestra estado de:
    - Extensiones PHP críticas (`zip`, `pdo`, `mbstring`, `openssl`)
    - Conexión a base de datos y cache
    - Permisos de almacenamiento y respaldo
    - Variables de entorno relevantes (`DB_*`, `MAIL_MAILER`, `DB_AUTO_BACKUP`)
    - Últimas entradas de auditoría

### 9. Scheduler de Respaldo Automático

- Activar auto-respaldo: define `DB_AUTO_BACKUP=true` en `.env`.
- Frecuencia: diaria a las `02:00` (ajustable editando `app/Console/Kernel.php`).
- Comando: `db:auto-backup` genera ZIP con `.json` por tabla en `storage/app/backups/`.
- Requiere scheduler del sistema ejecutando `php artisan schedule:run` cada minuto.
  - Windows (Task Scheduler): crea tarea que ejecute `powershell -NoProfile -ExecutionPolicy Bypass -Command "cd <ruta_proyecto>; php artisan schedule:run"` programada cada minuto.
  - Linux (cron): `* * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1`.

#### Respaldo en primer arranque (solo una vez)
- Si `DB_AUTO_BACKUP=true`, al iniciar el servicio por primera vez se ejecuta automáticamente un respaldo único.
- Se crea un marcador en `storage/app/backups/.boot_init_done` para asegurar que no vuelva a ejecutarse en arranques posteriores.
- Para reactivar esta ejecución única en el futuro, borra el archivo marcador: `storage/app/backups/.boot_init_done`.
- Se evita concurrencia entre procesos con un candado de caché temporal (15 minutos).
- Se registra el evento en `storage/logs/audit.log`.
 - La verificación del sistema muestra el estado del primer arranque (completado o pendiente).

### Auditoría
- Canal de logs `audit` en `storage/logs/audit.log`.
- Se registran: creación/descarga/eliminación de respaldos, toggle de auto-respaldo, ejecución/rollback de migraciones, optimización, limpieza de caches, guardado de conexión y respaldos automáticos.
### Notas
- El respaldo utiliza `ZipArchive` para empaquetar datos de tablas en formato `.json`. Si prefieres `.sql` (mysqldump/pg_dump), se puede integrar según el motor.
- Asegúrate de que el proceso de PHP tiene permisos de escritura para `.env` (al guardar conexión) y `storage/app/backups/`.

## Docker

Resumen rápido con un único `docker-compose.yml`:

- Arranca: `docker compose --env-file .env.docker up -d --build`
- Logs: `docker compose logs -f app`
- Migraciones: `docker compose exec app php artisan migrate --force`

Acceso a la aplicación:
- `http://sumaxia.local:8000` (recomendado). Sigue `SETUP-HOSTS.md` para configurar el alias.
- `http://localhost:8000` (respaldo).

Notas:
- `.env.docker` ya está alineado con MySQL y Mailtrap.
- En el arranque del contenedor `app` se ejecuta `composer install`, `php artisan migrate --force` y `php artisan storage:link`.
- Se ha eliminado `docker-compose.prod.yml` para evitar duplicidad y confusión.
- En Windows, WSL2 mejora compatibilidad con bind mounts.

## Estructura del Proyecto

```
sumaxia/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── ...
├── database/
│   ├── migrations/          # Migraciones de base de datos
│   └── seeders/            # Seeders
├── public/                 # Archivos públicos
├── resources/
│   ├── views/              # Vistas Blade
│   ├── js/                 # JavaScript
│   └── css/               # Estilos CSS
├── routes/
│   └── web.php            # Rutas web
├── storage/               # Archivos de almacenamiento
└── vendor/               # Dependencias de Composer
```

## Rutas Principales

### Autenticación
- `/login` - Inicio de sesión
- `/register` - Registro de administrador (envía código de verificación por email)

### Dashboard
- `/` - Dashboard principal
- `/dashboard` - Dashboard de usuario

### Facturación
- `/invoices` - Lista de facturas
- `/invoices/create` - Crear nueva factura

### Cotizaciones
- `/quotes` - Lista de cotizaciones
- `/quotes/create` - Crear nueva cotización

### Contabilidad
- `/accounting` - Panel de contabilidad
- `/accounting/accounts/create` - Crear nueva cuenta

### Nómina
- `/payroll` - Panel de nómina
- `/payroll/employees/create` - Crear nuevo empleado
- `/payroll/process` - Procesar nómina

### Administración
- `/admin/dashboard` - Dashboard de administración
- `/admin/users` - Gestión de usuarios
- `/admin/users/create` - Crear nuevo usuario
- `/admin/roles` - Gestión de roles
- `/admin/roles/create` - Crear nuevo rol
- `/admin/config` - Configuración del sistema
- `/admin/fe/config` - Configuración de Facturación Electrónica (DIAN)
- `/admin/fe/config/save` - Guardar la resolución de facturación electrónica
- `/admin/database` - Gestión de base de datos
- `/admin/system/verify` - Verificación del sistema
- `/admin/reports` - Reportes del sistema
- `/admin/reports/export/{format}` - Exportar reportes (`pdf`, `excel`, `csv`)

## Configuración Adicional

### Variables de Entorno Importantes
```env
APP_NAME=Sumaxia
APP_ENV=local
APP_DEBUG=true
APP_URL=http://sumaxia.local:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sumaxia
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<tu_user_mailtrap>
MAIL_PASSWORD=<tu_pass_mailtrap>
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@sumaxia.com
MAIL_FROM_NAME=SumAxia
```

### Configuración de Correo
Para configurar el envío de correos, actualiza las variables MAIL_* en el archivo .env según tu proveedor de correo.

Ejemplo para desarrollo con Mailtrap:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<tu_user_mailtrap>
MAIL_PASSWORD=<tu_pass_mailtrap>
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@sumaxia.com"
MAIL_FROM_NAME="SumAxia"
```

### Variables de Entorno FE (DIAN)
Configura las credenciales y entorno de Facturación Electrónica en `.env`:
```env
# Facturación Electrónica (DIAN)
FE_SOFTWARE_ID=""
FE_SOFTWARE_PIN=""
FE_CERT_PATH="storage/certs/dian.p12"
FE_CERT_PASSWORD=""
# Ambiente FE: 'test' (habilitación) o 'prod'
FE_ENVIRONMENT=test
```
Los valores se leen desde `config/fe.php` y se muestran en la vista de configuración.

- Seguridad del certificado: coloca tu archivo en `storage/certs/dian.p12` y no lo subas al repositorio. `.gitignore` ya excluye `storage/certs/` y archivos `*.p12`, `*.pfx`, `*.pem` bajo `storage/`.

### Sesiones
- Por defecto se usa `SESSION_DRIVER=database`, lo que requiere la tabla `sessions`.
- La migración que crea `sessions` se incluye dentro de `0001_01_01_000000_create_users_table.php`. Si ves el error de tabla faltante, ejecuta:
```bash
php artisan migrate
```
- Alternativa en desarrollo: usa archivos para sesiones.
```env
SESSION_DRIVER=file
```
Luego limpia configuración:
```bash
php artisan config:clear
```

## Desarrollo

### Comandos Útiles
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Crear migraciones
php artisan make:migration create_table_name

# Crear modelos
php artisan make:model ModelName

# Crear controladores
php artisan make:controller ControllerName

# Ejecutar tests
php artisan test
```

### Compilación de Assets
```bash
# Desarrollo (con watch)
npm run dev

# Producción
npm run build
```

## Solución de Problemas

### Error: `SQLSTATE[42S02]: Base table or view not found: 1146 Table '...sessions' doesn't exist`
- Causa: `SESSION_DRIVER=database` sin haber creado la tabla `sessions`.
- Solución:
  - Ejecuta migraciones: `php artisan migrate`.
  - Verifica conexión a la BD en `.env` (`DB_*`) y que la base existe.
  - Alternativa temporal en local: cambia a `SESSION_DRIVER=file` y corre `php artisan config:clear`.

### Cache / Config desactualizada
- Si cambias `.env` o configuración, ejecuta:
```bash
php artisan config:clear
php artisan cache:clear
```

## Operaciones de Mantenimiento

### Respaldo rápido (Windows)
Crear carpeta `bk` y copiar contenido del proyecto (excluyendo directorios pesados y archivos volátiles):
```powershell
New-Item -ItemType Directory -Path bk -Force
robocopy . bk /MIR \ \
  /XF storage\logs\laravel.log \
  /XD bk vendor storage\framework\cache storage\framework\views .git .github node_modules \
  /NFL /NDL /NP /R:1 /W:1
```

### Rollback de migraciones
Revertir el último lote de migraciones y recrear:
```bash
php artisan migrate:rollback
php artisan migrate
```


### Error de Permisos
```bash
# En Linux/Mac
chmod -R 755 storage bootstrap/cache

# En Windows, asegurar permisos de escritura en:
# - storage/
# - bootstrap/cache/
```

## Novedades y Uso

### Exportación de Reportes (PDF/Excel/CSV)
- Página: `/admin/reports`
- Botones de exportación disponibles que preservan filtros actuales (`period`, `start_date`, `end_date`).
- Rutas:
  - `GET /admin/reports/export/pdf?period=month&start_date=YYYY-MM-DD&end_date=YYYY-MM-DD`
  - `GET /admin/reports/export/excel?...`
  - `GET /admin/reports/export/csv?...`
- Si `dompdf` está instalado, el PDF se genera desde la plantilla HTML `resources/views/admin/reports-export-pdf.blade.php`.
- Si no está instalado, se usa un fallback ligero `App\Support\SimplePdf` para generar un PDF básico.

### Validación de Dominio en Creación de Usuarios
- Página: `/admin/users/create`
- La vista muestra el dominio esperado y valida en el cliente el campo `email` antes de enviar.
- La validación del dominio también existe en el servidor dentro de `Admin\UserController@store`.

### Middleware de Entradas en Minúsculas
- Middleware: `App\Http\Middleware\LowercaseInputMiddleware`
- Aplicación: registrado en el grupo `web` (global para vistas web).
- Convierte en minúsculas cada valor de texto del `Request`, excluyendo `password` y `password_confirmation`.

### Error de Base de Datos
1. Verificar que la base de datos existe
2. Verificar credenciales en .env
3. Ejecutar `php artisan migrate:fresh`

### Error de Assets
1. Ejecutar `npm install`
2. Ejecutar `npm run dev`
3. Verificar que Node.js esté instalado

### Facturación Electrónica (DIAN)
- Página: `/admin/fe/config` (solo administradores)
- Sección "Credenciales del Software DIAN": muestra `Software ID`, `Software PIN`, `Ruta del Certificado`, `Contraseña del Certificado` y `Ambiente` desde configuración (`.env`/`config/fe.php`).
- Sección "Resolución de Facturación": permite definir `Prefijo`, `Consecutivo inicial` y `final`, `Fecha inicio` y `Fecha fin`.
- Rutas:
  - `GET /admin/fe/config` muestra la configuración actual y la resolución activa.
  - `POST /admin/fe/config/save` guarda/actualiza la resolución (marca la nueva como activa).
- Persistencia: se almacena en la tabla `fe_resolutions` (migración `2025_10_16_000001_create_fe_resolutions_table.php`).
- Después de actualizar `.env`, ejecuta `php artisan config:clear` para reflejar los cambios en la vista.

## Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## Soporte

Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo.

---

**Versión:** 1.0.0  
**Última actualización:** Octubre 2025
### Comportamiento de Dashboard por Roles

- Ruta `GET /dashboard`:
  - Admin → redirige a `/admin/dashboard`.
  - Soporte interno (`soporte_interno`) → redirige a `/admin/database`.
  - Usuario regular → muestra el dashboard estándar.
- En páginas accesibles por soporte (p. ej. `/admin/database`), el botón "Admin Dashboard" apunta a `route('dashboard')` para respetar estas reglas; si ya estás en `/admin/database` como soporte, permanecerás en la misma página.

### Idiomas
- Soporta `es` y `en`. Cambia el idioma con `GET /locale/{lang}` o usando los botones ES/US. La selección se guarda en sesión (`app_locale`).

## Seguridad de Sesión y Redis

### Seguridad de cookies de sesión
- Variables clave en `.env`:
  - `SESSION_ENCRYPT=true` cifra los datos de sesión antes de almacenarlos.
  - `SESSION_SECURE_COOKIE=false` en local sin HTTPS; usa `true` en producción con HTTPS.
  - `SESSION_SAME_SITE=lax` por defecto. En producción puedes usar `strict` para máxima protección CSRF.
- Recomendaciones:
  - Desarrollo local (sin HTTPS):
    ```env
    SESSION_ENCRYPT=true
    SESSION_SECURE_COOKIE=false
    SESSION_SAME_SITE=lax
    ```
  - Producción (con HTTPS):
    ```env
    SESSION_ENCRYPT=true
    SESSION_SECURE_COOKIE=true
    # Usa strict si no necesitas flujos cross‑site; de lo contrario mantén lax
    SESSION_SAME_SITE=strict
    ```
  - Si necesitas cookies en iframes/terceros, usa:
    ```env
    SESSION_SECURE_COOKIE=true
    SESSION_SAME_SITE=none
    ```
    Nota: `none` requiere `secure=true` por estándar de navegador.

### Cache y Queue con Redis

Redis mejora significativamente el rendimiento de cache y colas respecto a `database`.

1) Configurar `.env`
```env
# Cache y cola en Redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# Cliente y conexión Redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Conexiones específicas
REDIS_CACHE_CONNECTION=cache
REDIS_CACHE_LOCK_CONNECTION=default
REDIS_QUEUE_CONNECTION=default
REDIS_QUEUE=default
REDIS_QUEUE_RETRY_AFTER=90
```

Aplica cambios:
```bash
php artisan config:clear
php artisan cache:clear
```

2) Arrancar Redis en local (opciones)
- Docker (recomendado):
  ```bash
  docker run --name redis -p 6379:6379 -d redis:7-alpine
  ```
- Windows nativo: usa Memurai (compatible con Redis) o WSL2 + `sudo apt install redis-server`.

3) Validación rápida de rendimiento
- Cache:
  ```bash
  php artisan tinker
  >>> Cache::put('bench_key', 'ok', 60)
  >>> Cache::get('bench_key')
  // Debe retornar: "ok"
  ```
- Queue:
  1. Crear un Job de prueba:
     ```bash
     php artisan make:job RedisProbeJob
     ```
  2. Edita `app/Jobs/RedisProbeJob.php` para registrar en logs dentro de `handle()`:
     ```php
     public function handle(): void
     {
         \Log::channel('audit')->info('Redis queue probe executed');
     }
     ```
  3. Ejecuta el worker:
     ```bash
     php artisan queue:work --queue=default --tries=1 --timeout=30
     ```
  4. En otra terminal, despacha el Job:
     ```bash
     php artisan tinker
     >>> App\Jobs\RedisProbeJob::dispatch()
     ```
  5. Verifica que aparece la entrada en `storage/logs/audit.log` y que el worker muestra ejecución inmediata.

Notas de producción:
- Mantén Redis fuera de la red pública, usa autenticación y reglas de firewall.
- Considera `SESSION_DRIVER=redis` si necesitas escalabilidad horizontal para sesiones.

# Guía de Despliegue con Docker (Linux/Windows/macOS)

Guías rápidas
- Linux: ver [.pasos](./.pasos)
- Windows: ver [pasos_Win](./pasos_Win)
