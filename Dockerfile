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

# Create startup script that handles migrations and server
RUN echo '#!/bin/bash\n\
php artisan migrate --force 2>/dev/null || true\n\
echo "Starting PHP server on port 8080..."\n\
exec php -S 0.0.0.0:8080 -t public public/index.php\n\
' > /app/start.sh && chmod +x /app/start.sh

CMD ["/bin/bash", "/app/start.sh"]
