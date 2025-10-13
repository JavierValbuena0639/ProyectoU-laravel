# Guía de Despliegue con Docker en Linux

Esta guía te ayudará a desplegar el sistema Sumaxia utilizando Docker en un entorno Linux con MySQL como base de datos.

## Requisitos Previos

- Docker Engine >= 20.10
- Docker Compose >= 2.0
- Git
- Al menos 2GB de RAM disponible
- 5GB de espacio en disco

## Estructura de Archivos Docker

### 1. Dockerfile

Crea un archivo `Dockerfile` en la raíz del proyecto:

```dockerfile
# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node.js y compilar assets
RUN npm install && npm run build

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Exponer puerto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
```

### 2. Docker Compose

Crea un archivo `docker-compose.yml` en la raíz del proyecto:

```yaml
version: '3.8'

services:
  # Aplicación Laravel
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: sumaxia_app
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=sumaxia
      - DB_USERNAME=sumaxia_user
      - DB_PASSWORD=sumaxia_password
    depends_on:
      mysql:
        condition: service_healthy
    networks:
      - sumaxia_network

  # Base de datos MySQL
  mysql:
    image: mysql:8.0
    container_name: sumaxia_mysql
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: sumaxia
      MYSQL_USER: sumaxia_user
      MYSQL_PASSWORD: sumaxia_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      timeout: 20s
      retries: 10
    networks:
      - sumaxia_network

  # phpMyAdmin (opcional)
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: sumaxia_phpmyadmin
    restart: unless-stopped
    ports:
      - "8080:80"
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: root_password
    depends_on:
      - mysql
    networks:
      - sumaxia_network

volumes:
  mysql_data:
    driver: local

networks:
  sumaxia_network:
    driver: bridge
```

### 3. Configuración de Apache

Crea el directorio `docker/apache/` y el archivo `000-default.conf`:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

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