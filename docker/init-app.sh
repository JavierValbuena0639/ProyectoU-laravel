#!/bin/bash

# Script de inicialización para el contenedor de la aplicación
set -e

echo "🚀 Iniciando configuración de SumAxia..."

# Asegurar que los directorios existen
echo "📁 Creando directorios necesarios..."
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions  
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Configurar permisos correctos
echo "🔐 Configurando permisos..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instalar dependencias si es necesario
echo "📦 Verificando dependencias de Composer..."
if [ ! -d "/var/www/vendor" ] || [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-scripts --no-interaction --optimize-autoloader
fi

# Esperar a que MySQL esté disponible
echo "🗄️ Esperando conexión a MySQL..."
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Esperando a MySQL..."
    sleep 2
done

# Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Crear enlace simbólico de storage
echo "🔗 Creando enlace de storage..."
php artisan storage:link || true

# Generar cachés para producción
echo "⚡ Generando cachés..."
php artisan config:cache
php artisan route:cache

echo "✅ Configuración completada. Iniciando servidor..."

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf