FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip default-mysql-client

# Install PHP extensions Symfony needs
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy app files
COPY . .

ENV APP_ENV=prod
ENV APP_DEBUG=0

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN chown -R www-data:www-data /var/www
RUN pecl install apcu && docker-php-ext-enable apcu

# Entrypoint: wait for db, run migrations, seed admin user, then start php-fpm
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
