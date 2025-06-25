FROM php:8.2-fpm

# Install tools and PHP extensions Laravel needs
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

# Install the required extensions (pdo_mysql is standard)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY composer.lock composer.json ./
RUN composer install --no-dev --no-interaction --no-scripts --optimize-autoloader
COPY . .
RUN chown -R www-data:www-data /var/www
CMD ["php-fpm"]