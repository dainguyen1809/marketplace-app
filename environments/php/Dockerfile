FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    autoconf \
    g++ \
    make \
    git \
    unzip \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    curl \
    libzip-dev \
    postgresql-dev \
    linux-headers \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip sockets \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
