# syntax=docker/dockerfile:1

# ---------- Stage 1: PHP dependencies (composer) ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------- Stage 2: Frontend assets (vite/tailwind) ----------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Stage 3: Runtime image ----------
FROM php:8.2-cli-alpine

WORKDIR /var/www/html

# System deps + PHP extensions Laravel needs (mysql, mbstring, zip, bcmath...)
RUN apk add --no-cache \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
        exif \
        pcntl \
        gd \
        intl \
    && apk del --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev oniguruma-dev icu-dev

# App code + vendor from build stages
COPY --from=vendor /app ./
COPY --from=frontend /app/public/build ./public/build

# Writable dirs Laravel needs at runtime
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Railway injects PORT at runtime; default kept for local `docker run`
EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]