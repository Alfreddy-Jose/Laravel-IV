FROM php:8.3-fpm

# Instala dependencias del sistema
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
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copia el código de la aplicación
WORKDIR /var/www/html
COPY . .

# Instala dependencias de PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Configurar permisos para Render (optimizado)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/storage/framework/ \
    && chmod 775 /var/www/html/storage/app/public

# Expone el puerto 8000
EXPOSE 8000

# Refresca la base de datos y ejecuta seeders en cada despliegue
CMD php artisan storage:link && php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=8000
