# ===========================================================
# Stage 1 - Composer
# ===========================================================
FROM composer:2.8 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --optimize


# ===========================================================
# Stage 2 - Build Frontend (Vite)
# ===========================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build


# ===========================================================
# Stage 3 - Production PHP-FPM
# ===========================================================
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache \
        bash \
        git \
        unzip \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        postgresql-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        zip \
        intl \
        bcmath \
        opcache \
        gd

# Copy application
COPY --from=composer /app /var/www/html

# Copy vendor
COPY --from=composer /app/vendor /var/www/html/vendor

# Copy Vite assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Storage
RUN mkdir -p storage/framework/{cache,sessions,views} \
    storage/logs bootstrap/cache

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

RUN chmod -R 775 \
    storage \
    bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]