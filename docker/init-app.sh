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

## Resolver credenciales de DB desde variables, incluyendo DB_URL si existe
PARSED_DB_HOST=""
PARSED_DB_PORT=""
PARSED_DB_USER=""
PARSED_DB_PASS=""
PARSED_DB_NAME=""
if [ -n "${DB_URL}" ]; then
  # Sanitizar: remover comillas/backticks y espacios alrededor
  RAW_DB_URL="${DB_URL}"
  RAW_DB_URL="${RAW_DB_URL//\"/}"
  RAW_DB_URL="${RAW_DB_URL//\`/}"
  # trim leading spaces
  RAW_DB_URL="${RAW_DB_URL#${RAW_DB_URL%%[![:space:]]*}}"
  # trim trailing spaces
  RAW_DB_URL="${RAW_DB_URL%${RAW_DB_URL##*[![:space:]]}}"

  URL_NO_PROTO="${RAW_DB_URL#mysql://}"
  USERPASS="${URL_NO_PROTO%%@*}"
  HOSTPATH="${URL_NO_PROTO#*@}"
  HOSTPORT="${HOSTPATH%%/*}"
  PARSED_DB_NAME="${HOSTPATH#*/}"
  PARSED_DB_USER="${USERPASS%%:*}"
  PARSED_DB_PASS="${USERPASS#*:}"
  PARSED_DB_HOST="${HOSTPORT%%:*}"
  PARSED_DB_PORT="${HOSTPORT#*:}"
  if [ "${PARSED_DB_PORT}" = "${HOSTPORT}" ] || [ -z "${PARSED_DB_PORT}" ]; then
    PARSED_DB_PORT="3306"
  fi
fi

## Esperar a que MySQL esté listo usando las credenciales de la app
DB_HOST=${DB_HOST:-${PARSED_DB_HOST:-${MYSQLHOST:-mysql}}}
DB_PORT=${DB_PORT:-${PARSED_DB_PORT:-${MYSQLPORT:-3306}}}
APP_DB_USER=${DB_USERNAME:-${PARSED_DB_USER:-${MYSQLUSER:-sumaxia_user}}}
APP_DB_PASS=${DB_PASSWORD:-${PARSED_DB_PASS:-${MYSQLPASSWORD:-sumaxia_password}}}
APP_DB_NAME=${DB_DATABASE:-${PARSED_DB_NAME:-${MYSQLDATABASE:-sumaxia}}}
DB_SSL_MODE=${DB_SSL_MODE:-PREFERRED} # Usa REQUIRED en proveedores que exigen SSL (p.ej. Railway)

echo "🗄️ Esperando a MySQL (${DB_HOST}:${DB_PORT}) con usuario '${APP_DB_USER}' y base '${APP_DB_NAME}'..."
# Algunos clientes no soportan --ssl-mode en mysqladmin. Usamos 'mysql' para verificar con SSL.
for i in $(seq 1 60); do
  if mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${APP_DB_USER}" -p"${APP_DB_PASS}" --ssl-mode="${DB_SSL_MODE}" -e "SELECT 1" >/dev/null 2>&1; then
    READY=1
    break
  else
    echo "Esperando a MySQL..."
    sleep 2
  fi
done
if [ -z "${READY}" ]; then
  echo "❌ No fue posible conectar a MySQL tras varios intentos. Revisa host/puerto y SSL."
  exit 1
fi

echo "✅ MySQL responde y autenticación de la app OK."

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