# Guía de Despliegue con Docker (Linux/Windows/macOS)

Nota de actualización (2025):
- La imagen oficial del proyecto usa PHP-FPM + Nginx gestionados por `supervisord` y un build multi-stage para compilar assets con Vite durante la construcción de la imagen. Ya no es necesario ejecutar Node en producción.
- Para producción Linux se incluye `docker-compose.prod.yml` sin mapeo de código (usa la imagen construida con assets incluidos) y con `healthcheck` del servicio `app`.

Resumen rápido (Desarrollo y Producción):
- Desarrollo:
  - Arranca: `docker compose -f docker-compose.yml --env-file .env.docker up -d`
  - Construir: `docker compose -f docker-compose.yml build`
  - Migraciones: `docker compose -f docker-compose.yml exec app php artisan migrate`
  - Logs: `docker compose -f docker-compose.yml logs -f app`
- Producción:
  - Arranca: `docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.docker up -d`
  - Construir: `docker compose -f docker-compose.yml -f docker-compose.prod.yml build`
  - Migraciones: `docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan migrate --force`
  - Acceso: `http://localhost:8000/`. phpMyAdmin opcional en `http://localhost:8080/` si está definido.


Esta guía te ayudará a desplegar el sistema Sumaxia utilizando Docker en un entorno Linux con MySQL como base de datos.

## Requisitos Previos

- Docker Engine >= 20.10
- Docker Compose >= 2.0
- Git
- Al menos 2GB de RAM disponible
- 5GB de espacio en disco

## Arquitectura actual

- Contenedor único para la aplicación con `php-fpm` y `nginx` corriendo bajo `supervisord`.
- Multi-stage build: se compilan assets con `Vite` (Node 18) durante la construcción y se copian a `public/build`.
- `docker-compose.prod.yml` elimina el mapeo de código para usar la imagen final y añade `healthcheck`.
- `docker-compose.yml` mantiene un servicio `node` opcional para desarrollo con hot reload (Windows/macOS/Linux).

## Diferencias entre docker-compose.yml y docker-compose.prod.yml

- Base vs override:
  - `docker-compose.yml` es la definición base pensada para desarrollo.
  - `docker-compose.prod.yml` es un override para producción con ajustes de seguridad y robustez.
- Build vs Image:
  - Desarrollo: `build: .` y bind mounts del código (`.:/var/www`).
  - Producción: `image: <repo/app:tag>` sin bind mounts del código, assets ya compilados.
- Variables y entorno:
  - Desarrollo: puede usar `.env` y `APP_DEBUG=true`.
  - Producción: usa `--env-file .env.docker`, `APP_ENV=production`, `APP_DEBUG=false`.
- Puertos y redes:
  - Desarrollo: puertos expuestos para probar localmente.
  - Producción: detrás de proxy/reverse proxy, limitar puertos expuestos.
- Resiliencia:
  - Producción: `healthcheck`, `restart: always`, dependencias con condiciones.
- Servicios auxiliares:
  - Desarrollo: watchers/hot‑reload.
  - Producción: mínimo necesario, logging y métricas adecuados.

## Windows/macOS

- Recomendado WSL2 en Windows para mejor rendimiento de bind mounts.
- Usa siempre `docker compose` (plugin moderno), no `docker-compose` (legacy) cuando sea posible.
- Ejemplos:
  - Dev: `docker compose -f docker-compose.yml --env-file .env.docker up -d`
  - Prod: `docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.docker up -d`

## Estructura de Archivos Docker

### 1. Dockerfile (multi-stage: Node + PHP-FPM)

El `Dockerfile` ya incluido en el repositorio compila assets con Node y construye la imagen final con PHP-FPM + Nginx.

```dockerfile
FROM node:18 AS node_builder

WORKDIR /app
COPY package.json ./
COPY . .
RUN npm install && npm run build

FROM php:8.2-fpm

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader
COPY --from=node_builder /app/public/build /var/www/public/build

# Instalar dependencias de Node.js y compilar assets
RUN npm install && npm run build

# Configurar permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Exponer puerto
EXPOSE 80

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### 2. Docker Compose (desarrollo vs producción)

- Desarrollo: usa `docker-compose.yml` (mapea código y puede incluir servicio `node` para hot reload).
- Producción Linux: usa `docker-compose.prod.yml` (sin mapeo de código, imagen con assets incluidos, healthcheck del `app`).

Para producción, utiliza el nuevo `docker-compose.prod.yml` ya presente en el repositorio.

### 3. Configuración de Nginx y PHP-FPM

Los archivos de configuración están en `docker/nginx.conf`, `docker/php.ini` y `docker/supervisord.conf`.

El `nginx.conf` del proyecto sirve `public/` y redirige a `index.php` cuando corresponde.

### 4. Script de Inicialización MySQL

Crea el directorio `docker/mysql/` y el archivo `init.sql`:

```sql
-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS sumaxia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario si no existe
CREATE USER IF NOT EXISTS 'sumaxia_user'@'%' IDENTIFIED BY 'sumaxia_password';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON sumaxia.* TO 'sumaxia_user'@'%';

-- Aplicar cambios
FLUSH PRIVILEGES;
```

### 5. Archivo de Entorno para Producción

Crea un archivo `.env.docker`:

```env
APP_NAME=Sumaxia
APP_ENV=production
APP_KEY=base64:GENERAR_NUEVA_CLAVE_AQUI
APP_DEBUG=false
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sumaxia
DB_USERNAME=sumaxia_user
DB_PASSWORD=sumaxia_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## Flujo de despliegue y pruebas en Linux

- Preparar variables: `cp .env.docker .env` y ajusta claves/credenciales.
- Construir imagen: `docker compose -f docker-compose.prod.yml build`.
- Arrancar servicios: `docker compose -f docker-compose.prod.yml up -d`.
- Comprobar estado: `docker compose -f docker-compose.prod.yml ps` y `docker compose -f docker-compose.prod.yml logs app`.
- Migraciones y seeds: `docker compose -f docker-compose.prod.yml exec app php artisan migrate --force` y opcionalmente `db:seed`.
- Pruebas: `docker compose -f docker-compose.prod.yml exec app php vendor/bin/phpunit -d memory_limit=-1`.

Consejos para Linux:
- Evita `host.docker.internal`; usa nombres de servicio (`mysql`) dentro de la red de Docker.
- Revisa permisos de `storage/` y `bootstrap/cache/` si montas volúmenes; la imagen ya ajusta permisos por defecto.
- Si usas SELinux, permite bind mounts o usa volúmenes gestionados por Docker.

## Instrucciones de Despliegue

### 1. Preparar el Servidor

```bash
# Actualizar el sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo apt install docker-compose-plugin

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER

# Reiniciar sesión o ejecutar:
newgrp docker
```

### 2. Clonar y Configurar el Proyecto

```bash
# Clonar el repositorio
git clone <repository-url> sumaxia
cd sumaxia

# Copiar archivo de entorno
cp .env.docker .env

# Generar nueva clave de aplicación
docker run --rm -v $(pwd):/app composer:latest bash -c "cd /app && php artisan key:generate --show"

# Actualizar la clave en .env
nano .env
```

### 3. Construir y Ejecutar los Contenedores

```bash
# Construir las imágenes
docker-compose build

# Iniciar los servicios
docker-compose up -d

# Verificar que los contenedores estén ejecutándose
docker-compose ps
```

### 4. Configurar la Base de Datos

```bash
# Esperar a que MySQL esté listo
docker-compose logs mysql

# Ejecutar migraciones
docker-compose exec app php artisan migrate --force

# (Opcional) Ejecutar seeders
docker-compose exec app php artisan db:seed --force
```

### 5. Configurar Permisos

```bash
# Configurar permisos de storage
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
```

## Acceso al Sistema

- **Aplicación Principal:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8080
- **Base de datos MySQL:** localhost:3306

### Credenciales por Defecto

- **MySQL Root:** root / root_password
- **MySQL Usuario:** sumaxia_user / sumaxia_password
- **Base de Datos:** sumaxia

## Comandos Útiles

### Gestión de Contenedores

```bash
# Ver logs de la aplicación
docker-compose logs app

# Ver logs de MySQL
docker-compose logs mysql

# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Acceder a MySQL
docker-compose exec mysql mysql -u root -p

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v
```

### Comandos Laravel en Docker

```bash
# Limpiar caché
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Crear usuario administrador
docker-compose exec app php artisan tinker
```

### Backup y Restauración

```bash
# Crear backup de la base de datos
docker-compose exec mysql mysqldump -u root -p sumaxia > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
docker-compose exec -T mysql mysql -u root -p sumaxia < backup_file.sql

# Backup de archivos de storage
tar -czf storage_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/
```

## Configuración de Producción

### 1. Configurar Dominio

```bash
# Editar docker-compose.yml para usar puerto 80
ports:
  - "80:80"

# Configurar proxy reverso con Nginx (recomendado)
# Ver sección de Nginx más abajo
```

### 2. Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d tu-dominio.com

# Configurar renovación automática
sudo crontab -e
# Agregar: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 3. Configuración de Nginx (Proxy Reverso)

Crear `/etc/nginx/sites-available/sumaxia`:

```nginx
server {
    listen 80;
    server_name tu-dominio.com;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Habilitar sitio
sudo ln -s /etc/nginx/sites-available/sumaxia /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Monitoreo y Logs

### 1. Configurar Logrotate

```bash
# Crear /etc/logrotate.d/sumaxia
/var/lib/docker/containers/*/*-json.log {
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 0644 root root
}
```

### 2. Monitoreo con Docker Stats

```bash
# Ver uso de recursos
docker stats

# Monitoreo continuo
watch docker-compose ps
```

## Solución de Problemas

### Problemas Comunes

1. **Error de conexión a MySQL:**
   ```bash
   docker-compose logs mysql
   docker-compose restart mysql
   ```

2. **Permisos de archivos:**
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html/storage
   ```

3. **Error de memoria:**
   ```bash
   # Aumentar memoria en docker-compose.yml
   deploy:
     resources:
       limits:
         memory: 1G
   ```

4. **Limpiar sistema Docker:**
   ```bash
   docker system prune -a
   docker volume prune
   ```

## Actualización del Sistema

```bash
# Hacer backup
docker-compose exec mysql mysqldump -u root -p sumaxia > backup_before_update.sql

# Detener servicios
docker-compose down

# Actualizar código
git pull origin main

# Reconstruir imágenes
docker-compose build --no-cache

# Iniciar servicios
docker-compose up -d

# Ejecutar migraciones
docker-compose exec app php artisan migrate --force
```

## Seguridad

### Recomendaciones de Seguridad

1. **Cambiar contraseñas por defecto**
2. **Usar variables de entorno para secretos**
3. **Configurar firewall:**
   ```bash
   sudo ufw allow 22
   sudo ufw allow 80
   sudo ufw allow 443
   sudo ufw enable
   ```
4. **Actualizar regularmente las imágenes Docker**
5. **Configurar backups automáticos**

---

Esta guía proporciona una configuración completa para desplegar Sumaxia en producción usando Docker en Linux con MySQL.