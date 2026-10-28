# ---------- Stage 1: PHP-FPM app ----------
FROM php:8.4-fpm AS php

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
RUN pecl install apcu && docker-php-ext-enable apcu


# Download vendor JS (gitignored), then compile hashed assets
RUN APP_SECRET=build-only php bin/console importmap:install
RUN APP_SECRET=build-only php bin/console asset-map:compile

# Entrypoint: wait for db, run migrations, seed admin user, then start php-fpm
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

# ---------- Stage 2: nginx ----------
FROM nginx:alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=php /var/www/public /var/www/public
