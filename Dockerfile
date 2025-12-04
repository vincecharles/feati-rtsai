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

# Expose port
EXPOSE 8080

# Create a proper router script for PHP built-in server
RUN echo '<?php\n\
$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));\n\
$path = __DIR__ . $uri;\n\
if ($uri !== "/" && file_exists($path)) {\n\
    if (is_file($path)) {\n\
        $ext = pathinfo($path, PATHINFO_EXTENSION);\n\
        $mimeTypes = [\n\
            "css" => "text/css",\n\
            "js" => "application/javascript",\n\
            "json" => "application/json",\n\
            "png" => "image/png",\n\
            "jpg" => "image/jpeg",\n\
            "jpeg" => "image/jpeg",\n\
            "gif" => "image/gif",\n\
            "svg" => "image/svg+xml",\n\
            "ico" => "image/x-icon",\n\
            "woff" => "font/woff",\n\
            "woff2" => "font/woff2",\n\
            "ttf" => "font/ttf",\n\
            "eot" => "application/vnd.ms-fontobject",\n\
        ];\n\
        if (isset($mimeTypes[$ext])) {\n\
            header("Content-Type: " . $mimeTypes[$ext]);\n\
        }\n\
        readfile($path);\n\
        return true;\n\
    }\n\
    return false;\n\
}\n\
require_once __DIR__ . "/index.php";\n\
' > /app/public/router.php

CMD php artisan migrate --force 2>/dev/null || true; \
    php -S 0.0.0.0:8080 -t public public/router.php
