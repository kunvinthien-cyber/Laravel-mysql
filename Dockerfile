FROM php:8.4-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    ca-certificates \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev

# Configure & Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        dom \
        intl \
        zip \
        bcmath \
        gd \
        mbstring \
        opcache

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

RUN rm -f bootstrap/cache/*.php

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Port
EXPOSE 8000

# Start Web Server ជាចម្បង (Server នឹងដំណើរការជានិច្ច មិន Crash ឡើយ)
CMD php artisan migrate --force --seed && php artisan package:discover --ansi && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
