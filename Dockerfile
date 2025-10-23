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
    supervisor \
    default-mysql-client \
    netcat-openbsd

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias y dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chmod +x /usr/local/bin/composer

# Verificar instalación de Composer
RUN composer --version

# Crear usuario para la aplicación Laravel
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copiar configuraciones
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/init-app.sh /usr/local/bin/init-app.sh

# Habilitar sitio de Nginx y permisos de script
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && chmod +x /usr/local/bin/init-app.sh

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos de composer primero para aprovechar cache de Docker
COPY composer.json composer.lock ./

# Configurar Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_PROCESS_TIMEOUT=600

# Instalar dependencias de Composer
# Para desarrollo incluye dependencias de dev, para producción usa --no-dev
ARG INSTALL_DEV=true
RUN if [ "$INSTALL_DEV" = "true" ] ; then \
        composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts --verbose ; \
    else \
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts --verbose ; \
    fi

# Copiar el resto de archivos de la aplicación
COPY . /var/www

# Copiar assets compilados desde el stage de Node
COPY --from=node_builder /app/public/build /var/www/public/build

# Asegurar que vendor existe y tiene los permisos correctos
RUN composer dump-autoload --optimize

# Crear directorios necesarios y configurar permisos
RUN mkdir -p /var/www/storage/framework/cache/data \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Base de datos configurada para MySQL (no SQLite)

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]