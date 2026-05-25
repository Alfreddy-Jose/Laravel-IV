FROM php:8.3-fpm

# Instala dependencias del sistema y SUPERVISOR
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copia el código de la aplicación
WORKDIR /var/www/html
COPY . .

# Instala dependencias de PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Configurar permisos para Render
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/storage/framework/ \
    && chmod 775 /var/www/html/storage/app/public

# Optimizaciones de caché de Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Copiar configuración de Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

# Ejecuta enlaces, migraciones seguras y levanta SUPERVISOR para controlar ambos procesos
CMD php artisan storage:link && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
