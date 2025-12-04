FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure PHP for better error logging
RUN echo "display_errors=On" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "display_startup_errors=On" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "error_reporting=E_ALL" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "log_errors=On" >> /usr/local/etc/php/conf.d/errors.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Create storage directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && touch storage/logs/laravel.log \
    && chmod 777 storage/logs/laravel.log

# Create a simple test script
RUN echo '<?php echo "PHP is working! " . PHP_VERSION; ?>' > /app/public/test.php

# Expose port
EXPOSE 8080

# Start the application - simple and direct
CMD php artisan migrate --force 2>/dev/null || true; php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
