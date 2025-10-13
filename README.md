# Sistema de Gestión Empresarial Sumaxia

## Descripción

Sumaxia es un sistema integral de gestión empresarial desarrollado en Laravel que incluye módulos para facturación, cotizaciones, contabilidad, nómina y administración de usuarios. El sistema está diseñado para pequeñas y medianas empresas que necesitan una solución completa para gestionar sus operaciones financieras y administrativas.

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

### 🧪 Validaciones y Middleware
- Validación de dominio al crear usuarios: se bloquea el envío si el dominio del correo no coincide con el dominio esperado y se muestra un aviso.
- Conversión automática de entradas a minúsculas: middleware global transforma todos los campos de texto en minúsculas (excluye `password` y `password_confirmation`).

## Requisitos del Sistema

### Requisitos Mínimos
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- NPM o Yarn
- SQLite (por defecto) o MySQL
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

#### Opción A: SQLite (Recomendado para desarrollo)
```bash
# Crear el archivo de base de datos
touch database/database.sqlite

# Configurar en .env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/al/proyecto/database/database.sqlite
```

#### Opción B: MySQL
```bash
# Configurar en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sumaxia
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

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
php artisan serve
```

El sistema estará disponible en `http://localhost:8000`

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
- `/admin/database` - Gestión de base de datos
- `/admin/reports` - Reportes del sistema
- `/admin/reports/export/{format}` - Exportar reportes (`pdf`, `excel`, `csv`)

## Configuración Adicional

### Variables de Entorno Importantes
```env
APP_NAME=Sumaxia
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/database/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Configuración de Correo
Para configurar el envío de correos, actualiza las variables MAIL_* en el archivo .env según tu proveedor de correo.

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
**Última actualización:** Enero 2025
