#!/bin/bash
set -e

echo "🚀 Iniciando configuración de SumAxia..."

# Crear directorios necesarios y permisos
mkdir -p /var/www/storage/framework/{cache/data,sessions,views} /var/www/storage/logs /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instalar dependencias si no existe vendor
if [ ! -f "/var/www/vendor/autoload.php" ]; then
  echo "📦 Instalando dependencias de Composer..."
  composer install --no-scripts --no-interaction --optimize-autoloader
fi

# Esperar a que MySQL esté listo por socket, independientemente de Laravel
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-root_password}

echo "🗄️ Esperando a MySQL (${DB_HOST}:${DB_PORT})..."
until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -uroot -p"${DB_ROOT_PASSWORD}" --silent --skip-ssl; do
  echo "Esperando a MySQL..."
  sleep 2
done

echo "✅ MySQL responde. Validando credenciales de aplicación..."
APP_DB_USER=${DB_USERNAME:-sumaxia_user}
APP_DB_PASS=${DB_PASSWORD:-sumaxia_password}
APP_DB_NAME=${DB_DATABASE:-sumaxia}

if ! mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${APP_DB_USER}" -p"${APP_DB_PASS}" --skip-ssl -e "SELECT 1" >/dev/null 2>&1; then
  echo "⚙️ Creando usuario/BD para la app si no existen..."
  mysql -h"${DB_HOST}" -P"${DB_PORT}" -uroot -p"${DB_ROOT_PASSWORD}" --skip-ssl <<SQL
CREATE DATABASE IF NOT EXISTS \`${APP_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${APP_DB_USER}'@'%' IDENTIFIED BY '${APP_DB_PASS}';
GRANT ALL PRIVILEGES ON \`${APP_DB_NAME}\`.* TO '${APP_DB_USER}'@'%';
FLUSH PRIVILEGES;
SQL
fi

# Limpiar y generar cachés
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Ejecutar migraciones con seeders
echo "🌱 Ejecutando migraciones y seeders..."
php artisan migrate --force || true
php artisan db:seed --force || true

# Enlace de storage
php artisan storage:link || true

# Arrancar servicios
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf