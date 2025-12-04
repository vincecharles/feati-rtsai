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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Cache Laravel config
RUN php artisan config:clear
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port
EXPOSE 8080

# Start the application
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
