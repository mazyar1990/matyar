# Use the official PHP 8.2 FPM image on Alpine Linux
FROM php:8.2-fpm-alpine AS base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies needed for Laravel
RUN apk add --no-cache \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    # Add git for composer dependencies from git repos
    git \
    # Add mysql client for database connections
    mysql-client

# Install PHP extensions required by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    gd \
    intl \
    exif \
    pcntl \
    bcmath \
    soap

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Clear cache
RUN rm -rf /var/cache/apk/*

# --- Build Stage for Composer Dependencies ---
FROM base AS vendor
WORKDIR /var/www/html
COPY database database
COPY composer.json composer.lock ./
# Install dependencies
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

# --- Build Stage for Frontend Assets ---
FROM node:18-alpine AS frontend
WORKDIR /var/www/html
COPY package.json package-lock.json vite.config.js ./
RUN npm install
COPY . .
RUN npm run build

# --- Final Production Image ---
FROM base AS final
WORKDIR /var/www/html

# Copy application code, vendor files, and compiled assets
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data --from=frontend /var/www/html/public/build ./public/build

# Fix permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]