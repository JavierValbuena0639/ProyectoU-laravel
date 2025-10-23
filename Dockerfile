FROM node:18 AS node_builder

# Preparar build de assets con Vite
WORKDIR /app
COPY package.json ./
# Copiar el resto del proyecto para que el plugin de Laravel Vite resuelva rutas correctamente
COPY . .
RUN npm install && npm run build

FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nginx \
    supervisor

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chmod +x /usr/local/bin/composer

# Verificar instalación de Composer
RUN composer --version

# Crear usuario para la aplicación Laravel
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copiar archivos de configuración
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/local.ini

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos de composer primero para aprovechar cache de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de Composer
# Para desarrollo incluye dependencias de dev, para producción usa --no-dev
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ARG INSTALL_DEV=true
RUN if [ "$INSTALL_DEV" = "true" ] ; then \
        composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts ; \
    else \
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts ; \
    fi

# Copiar el resto de archivos de la aplicación
COPY . /var/www

# Copiar assets compilados desde el stage de Node
COPY --from=node_builder /app/public/build /var/www/public/build

# Asegurar que vendor existe y tiene los permisos correctos
RUN composer dump-autoload --optimize

# Cambiar propietario de los archivos
RUN chown -R www:www /var/www
RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/bootstrap/cache

# Base de datos configurada para MySQL (no SQLite)

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]