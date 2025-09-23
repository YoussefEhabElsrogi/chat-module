# Use PHP 8.2 CLI
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
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
RUN npm install

# Build assets
RUN npm run build

# Optimize Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Set proper permissions
RUN chmod -R 755 /var/www/html

# Expose port
EXPOSE 8000

# Start Laravel server with server.php for static files
CMD php -S 0.0.0.0:8000 -t public public/server.php
